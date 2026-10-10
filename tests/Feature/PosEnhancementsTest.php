<?php

namespace Tests\Feature;

use App\Models\CashierShift;
use App\Models\HoldTransaction;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PosEnhancementsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $cashier;
    protected Outlet $outlet;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->outlet = Outlet::create([
            'name' => 'Outlet Utama',
            'code' => 'OUT-01',
            'address' => 'Jl. Test No. 1',
            'phone' => '08123456789',
            'is_main' => true,
            'is_active' => true,
        ]);

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
            'outlet_id' => $this->outlet->id,
        ]);

        $this->cashier = User::factory()->create([
            'role' => 'cashier',
            'is_active' => true,
            'outlet_id' => $this->outlet->id,
        ]);

        $this->product = Product::create([
            'name' => 'Nugget Ayam 500g',
            'sku' => 'NGT-001',
            'barcode' => '8991234567890',
            'cost_price' => 20000,
            'selling_price' => 30000,
            'stock' => 50,
            'min_stock' => 5,
            'unit' => 'pcs',
            'is_active' => true,
        ]);
    }

    public function test_cashier_shift_lifecycle_open_and_close_with_over_short(): void
    {
        // 1. Open shift via PosTerminal
        $pos = Livewire::actingAs($this->cashier)
            ->test(\App\Livewire\Pos\PosTerminal::class)
            ->set('shiftStartingCash', 200000)
            ->set('shiftOpeningNotes', 'Buka shift pagi')
            ->call('startShift')
            ->assertSet('showOpenShiftModal', false);

        $shift = CashierShift::getActiveShift($this->cashier->id, $this->outlet->id);
        $this->assertNotNull($shift);
        $this->assertEquals(200000, $shift->starting_cash);
        $this->assertEquals('open', $shift->status);

        // 2. Perform a cash transaction linked to shift
        $pos->call('addToCart', $this->product->id)
            ->call('addToCart', $this->product->id) // 2 pcs = 60.000
            ->set('paymentMethod', 'cash')
            ->set('paidAmount', 100000)
            ->call('processPayment')
            ->assertSet('showReceiptModal', true);

        $shift->refresh();
        $this->assertEquals(60000, $shift->total_cash_sales);
        $this->assertEquals(260000, $shift->expected_ending_cash);

        // 3. Close shift with physical cash count (e.g. Rp 255.000 -> Short Rp 5.000)
        $pos->call('openCloseShiftModal')
            ->assertSet('showCloseShiftModal', true)
            ->set('shiftActualEndingCash', 255000)
            ->set('shiftClosingNotes', 'Selisih 5rb uang koin kurang')
            ->call('submitCloseShift')
            ->assertSet('showCloseShiftModal', false)
            ->assertSet('showShiftReceiptModal', true);

        $shift->refresh();
        $this->assertEquals('closed', $shift->status);
        $this->assertEquals(255000, $shift->actual_ending_cash);
        $this->assertEquals(-5000, $shift->cash_difference);
    }

    public function test_hold_and_resume_cart_transaction(): void
    {
        // Open shift first
        CashierShift::create([
            'user_id' => $this->cashier->id,
            'outlet_id' => $this->outlet->id,
            'opened_at' => now(),
            'starting_cash' => 100000,
            'status' => 'open',
        ]);

        // 1. Add items to cart and hold
        $component = Livewire::actingAs($this->cashier)
            ->test(\App\Livewire\Pos\PosTerminal::class)
            ->call('addToCart', $this->product->id)
            ->call('addToCart', $this->product->id)
            ->call('addToCart', $this->product->id) // 3 pcs = 90.000
            ->set('holdCustomerNote', 'Pelanggan ambil sosis di freezer')
            ->call('confirmHoldCart')
            ->assertSet('cart', [])
            ->assertSet('showHoldModal', false);

        $hold = HoldTransaction::where('outlet_id', $this->outlet->id)->first();
        $this->assertNotNull($hold);
        $this->assertEquals('Pelanggan ambil sosis di freezer', $hold->customer_name);
        $this->assertEquals(90000, $hold->grand_total);
        $this->assertCount(1, $hold->cart_data);

        // 2. Resume the held cart
        $component->call('resumeHoldTransaction', $hold->id)
            ->assertSet('showHoldListModal', false);

        $this->assertCount(1, $component->get('cart'));
        $this->assertEquals(0, HoldTransaction::where('id', $hold->id)->count());
    }

    public function test_split_payment_multi_payment(): void
    {
        $shift = CashierShift::create([
            'user_id' => $this->cashier->id,
            'outlet_id' => $this->outlet->id,
            'opened_at' => now(),
            'starting_cash' => 100000,
            'status' => 'open',
        ]);

        // Cart total: 3 x 30.000 = 90.000
        // Split: 50.000 Cash + 40.000 QRIS
        Livewire::actingAs($this->cashier)
            ->test(\App\Livewire\Pos\PosTerminal::class)
            ->call('addToCart', $this->product->id)
            ->call('addToCart', $this->product->id)
            ->call('addToCart', $this->product->id)
            ->set('paymentMethod', 'split')
            ->set('splitCashAmount', 50000)
            ->set('splitNonCashAmount', 40000)
            ->set('splitNonCashMethod', 'qris')
            ->call('processPayment')
            ->assertSet('showReceiptModal', true);

        $transaction = Transaction::latest('id')->first();
        $this->assertNotNull($transaction);
        $this->assertEquals('split', $transaction->payment_method);
        $this->assertEquals(50000, $transaction->split_cash_amount);
        $this->assertEquals(40000, $transaction->split_non_cash_amount);
        $this->assertEquals('qris', $transaction->split_non_cash_method);
        $this->assertEquals($shift->id, $transaction->cashier_shift_id);

        $shift->refresh();
        $this->assertEquals(50000, $shift->total_cash_sales);
        $this->assertEquals(40000, $shift->total_qris_sales);
        $this->assertEquals(90000, $shift->total_split_sales);
        $this->assertEquals(150000, $shift->expected_ending_cash); // 100k start + 50k cash split
    }

    public function test_whatsapp_digital_receipt_url_generation(): void
    {
        $transaction = Transaction::create([
            'invoice_number' => 'TRX-20260925-0001',
            'outlet_id' => $this->outlet->id,
            'user_id' => $this->cashier->id,
            'subtotal' => 30000,
            'tax_amount' => 0,
            'discount_amount' => 0,
            'grand_total' => 30000,
            'payment_method' => 'cash',
            'payment_amount' => 50000,
            'change_amount' => 20000,
            'status' => 'completed',
        ]);

        $transaction->items()->create([
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'cost_price' => 20000,
            'unit_price' => 30000,
            'quantity' => 1,
            'subtotal' => 30000,
            'unit_type' => 'unit',
            'pieces_per_unit' => 1,
        ]);

        $waUrl = $transaction->getWhatsAppUrl('081298765432');
        $this->assertStringContainsString('https://api.whatsapp.com/send?phone=6281298765432', $waUrl);
        $this->assertStringContainsString(urlencode('TRX-20260925-0001'), $waUrl);
        $this->assertStringContainsString(urlencode('Nugget Ayam 500g'), $waUrl);
    }

    public function test_admin_can_access_cashier_shift_management(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.shifts.index'));
        $response->assertStatus(200);
        $response->assertSeeLivewire(\App\Livewire\Admin\CashierShift\CashierShiftIndex::class);
    }
}
