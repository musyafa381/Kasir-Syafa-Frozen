<?php

namespace Tests\Feature;

use App\Livewire\Admin\Report\AccountsPayableReport;
use App\Livewire\Admin\Report\InventoryValuationReport;
use App\Livewire\Admin\Transaction\ReceivableIndex;
use App\Livewire\Pos\PosTerminal;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FinanceValuationAndReceivableTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $cashier;
    private Category $category;
    private Product $product1;
    private Product $product2;
    private Customer $customer;
    private Supplier $supplier;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->cashier = User::factory()->create(['role' => 'cashier', 'is_active' => true]);

        $this->category = Category::create([
            'name' => 'Frozen Meat & Poultry',
            'slug' => 'frozen-meat-poultry',
        ]);

        $this->product1 = Product::create([
            'category_id' => $this->category->id,
            'name' => 'Daging Sapi Slice 500g',
            'barcode' => '899123456001',
            'cost_price' => 50000,
            'selling_price' => 65000,
            'stock' => 20,
            'unit' => 'pack',
            'min_stock' => 5,
            'is_active' => true,
        ]);

        $this->product2 = Product::create([
            'category_id' => $this->category->id,
            'name' => 'Ayam Karage 500g',
            'barcode' => '899123456002',
            'cost_price' => 30000,
            'selling_price' => 42000,
            'stock' => 10,
            'unit' => 'pack',
            'min_stock' => 5,
            'is_active' => true,
        ]);

        $this->customer = Customer::create([
            'name' => 'Ibu Rahmawati',
            'phone' => '081234567890',
            'member_code' => 'MBR-0001',
            'is_active' => true,
        ]);

        $this->supplier = Supplier::create([
            'name' => 'PT Sumber Frozen Prima',
            'contact_person' => 'Budi Santoso',
            'phone' => '081198765432',
            'is_active' => true,
        ]);
    }

    // ================= 1. INVENTORY VALUATION TESTS =================
    public function test_inventory_valuation_calculates_asset_metrics_correctly(): void
    {
        Livewire::actingAs($this->admin)
            ->test(InventoryValuationReport::class)
            ->assertStatus(200)
            ->assertViewHas('totalAssetCostValue', function ($val) {
                // (20 * 50.000) + (10 * 30.000) = 1.000.000 + 300.000 = 1.300.000
                return (float)$val === 1300000.0;
            })
            ->assertViewHas('totalPotentialRevenue', function ($val) {
                // (20 * 65.000) + (10 * 42.000) = 1.300.000 + 420.000 = 1.720.000
                return (float)$val === 1720000.0;
            })
            ->assertViewHas('totalPotentialProfit', function ($val) {
                // 1.720.000 - 1.300.000 = 420.000
                return (float)$val === 420000.0;
            })
            ->assertSee('Daging Sapi Slice 500g')
            ->assertSee('Ayam Karage 500g');
    }

    // ================= 2. ACCOUNTS PAYABLE (HUTANG SUPPLIER) TESTS =================
    public function test_accounts_payable_report_and_installment_recording(): void
    {
        $purchase = Purchase::create([
            'invoice_number' => 'PO-20260917-0001',
            'supplier_invoice_number' => 'INV-SUP-9988',
            'supplier_id' => $this->supplier->id,
            'user_id' => $this->admin->id,
            'purchase_date' => Carbon::now()->subDays(5),
            'total_amount' => 1000000,
            'paid_amount' => 300000, // DP 300k, Sisa 700k
            'payment_status' => 'partial',
            'payment_method' => 'tempo',
            'due_date' => Carbon::today()->addDays(5),
            'status' => 'received',
        ]);

        Livewire::actingAs($this->admin)
            ->test(AccountsPayableReport::class)
            ->assertStatus(200)
            ->assertViewHas('totalRemainingDebt', function ($val) {
                return (float)$val === 700000.0;
            })
            ->assertSee('PO-20260917-0001')
            ->assertSee('PT Sumber Frozen Prima')
            ->call('openPaymentModal', $purchase->id)
            ->assertSet('showPaymentModal', true)
            ->assertSet('paymentAmount', 700000.0)
            ->set('paymentAmount', 400000.0) // Bayar cicilan 400k
            ->call('recordPayment')
            ->assertSet('showPaymentModal', false);

        // Verify purchase updated to paid_amount = 700k, status = partial
        $purchase->refresh();
        $this->assertEquals(700000.0, (float)$purchase->paid_amount);
        $this->assertEquals(300000.0, (float)$purchase->remaining_amount);
        $this->assertEquals('partial', $purchase->payment_status);

        // Pelunasan sisa 300k
        Livewire::actingAs($this->admin)
            ->test(AccountsPayableReport::class)
            ->call('openPaymentModal', $purchase->id)
            ->set('paymentAmount', 300000.0)
            ->call('recordPayment');

        $purchase->refresh();
        $this->assertEquals(1000000.0, (float)$purchase->paid_amount);
        $this->assertEquals(0.0, (float)$purchase->remaining_amount);
        $this->assertEquals('paid', $purchase->payment_status);
    }

    // ================= 3. POS TEMPO SALES & RECEIVABLE RECORDING TESTS =================
    public function test_pos_tempo_sale_requires_customer_selection(): void
    {
        Livewire::actingAs($this->cashier)
            ->test(PosTerminal::class)
            ->call('addToCart', $this->product1->id)
            ->call('openPaymentModal')
            ->call('setPaymentMethod', 'tempo')
            ->call('processPayment')
            ->assertSet('posAlertType', 'error')
            ->assertCount('cart', 1);

        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_pos_tempo_sale_successfully_creates_credit_transaction(): void
    {
        Livewire::actingAs($this->cashier)
            ->test(PosTerminal::class)
            ->call('selectCustomer', $this->customer->id)
            ->call('addToCart', $this->product1->id) // 65.000
            ->call('addToCart', $this->product1->id) // 65.000 (Subtotal 130.000)
            ->call('openPaymentModal')
            ->call('setPaymentMethod', 'tempo')
            ->set('paidAmount', 50000.0) // DP 50.000
            ->set('tempoDueDate', Carbon::today()->addDays(14)->format('Y-m-d'))
            ->call('processPayment')
            ->assertSet('showReceiptModal', true)
            ->assertCount('cart', 0);

        // Verify transaction
        $this->assertDatabaseHas('transactions', [
            'customer_id' => $this->customer->id,
            'grand_total' => 130000.0,
            'paid_amount' => 50000.0,
            'payment_method' => 'tempo',
            'payment_status' => 'partial',
        ]);

        // Verify stock deducted
        $this->assertDatabaseHas('products', [
            'id' => $this->product1->id,
            'stock' => 18, // 20 - 2 = 18
        ]);
    }

    // ================= 4. ACCOUNTS RECEIVABLE (PIUTANG PELANGGAN) TESTS =================
    public function test_receivable_index_lists_and_records_customer_installment(): void
    {
        $trx = Transaction::create([
            'invoice_number' => 'INV-20260917-0001',
            'user_id' => $this->cashier->id,
            'customer_id' => $this->customer->id,
            'customer_name' => $this->customer->name,
            'subtotal' => 200000,
            'grand_total' => 200000,
            'paid_amount' => 50000, // DP 50.000, Sisa 150.000
            'change_amount' => 0,
            'payment_method' => 'tempo',
            'payment_status' => 'partial',
            'due_date' => Carbon::today()->addDays(7),
            'status' => 'completed',
        ]);

        Livewire::actingAs($this->admin)
            ->test(ReceivableIndex::class)
            ->assertStatus(200)
            ->assertViewHas('totalActiveReceivables', function ($val) {
                return (float)$val === 150000.0;
            })
            ->assertSee('INV-20260917-0001')
            ->assertSee('Ibu Rahmawati')
            ->call('openPaymentModal', $trx->id)
            ->assertSet('showPaymentModal', true)
            ->assertSet('paymentAmount', 150000.0)
            ->set('paymentAmount', 100000.0) // Cicil 100k
            ->set('paymentMethod', 'transfer')
            ->call('recordPayment')
            ->assertSet('showPaymentModal', false);

        $trx->refresh();
        $this->assertEquals(150000.0, (float)$trx->paid_amount);
        $this->assertEquals(50000.0, (float)$trx->remaining_amount);
        $this->assertEquals('partial', $trx->payment_status);

        // Pelunasan sisa 50k
        Livewire::actingAs($this->admin)
            ->test(ReceivableIndex::class)
            ->call('openPaymentModal', $trx->id)
            ->set('paymentAmount', 50000.0)
            ->set('paymentMethod', 'cash')
            ->call('recordPayment');

        $trx->refresh();
        $this->assertEquals(200000.0, (float)$trx->paid_amount);
        $this->assertEquals(0.0, (float)$trx->remaining_amount);
        $this->assertEquals('paid', $trx->payment_status);
    }
}
