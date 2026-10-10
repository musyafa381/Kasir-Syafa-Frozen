<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\Payroll;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentExportAndPrintTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $cashier;
    private Product $product;
    private Customer $customer;
    private Supplier $supplier;
    private Transaction $transaction;
    private Purchase $purchase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->cashier = User::factory()->create(['role' => 'cashier', 'is_active' => true]);

        $category = Category::create([
            'name' => 'Frozen Food',
            'slug' => 'frozen-food',
        ]);

        $this->product = Product::create([
            'category_id' => $category->id,
            'name' => 'Nugget Crispy 500g',
            'barcode' => '8991234567890',
            'cost_price' => 30000,
            'selling_price' => 45000,
            'stock' => 50,
            'unit' => 'pack',
            'min_stock' => 10,
            'is_active' => true,
        ]);

        $this->customer = Customer::create([
            'name' => 'Resto Berkah',
            'member_code' => 'MBR-001',
            'phone' => '081234567890',
            'email' => 'resto@berkah.com',
            'address' => 'Jl. Merdeka No. 10',
        ]);

        $this->supplier = Supplier::create([
            'name' => 'PT Sumber Pangan Sejahtera',
            'contact_person' => 'Budi Santoso',
            'phone' => '081298765432',
            'address' => 'Kawasan Industri Cikarang',
        ]);

        // Sample Transaction
        $this->transaction = Transaction::create([
            'invoice_number' => 'INV-20260920-0001',
            'user_id' => $this->admin->id,
            'customer_id' => $this->customer->id,
            'customer_name' => 'Resto Berkah',
            'subtotal' => 90000,
            'discount_amount' => 0,
            'tax_amount' => 0,
            'grand_total' => 90000,
            'total_cost' => 60000,
            'paid_amount' => 50000,
            'change_amount' => 0,
            'payment_method' => 'tempo',
            'payment_status' => 'partial',
            'remaining_amount' => 40000,
            'due_date' => Carbon::now()->addDays(7),
        ]);

        TransactionItem::create([
            'transaction_id' => $this->transaction->id,
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'quantity' => 2,
            'unit_price' => 45000,
            'cost_price' => 30000,
            'subtotal' => 90000,
            'total_cost' => 60000,
        ]);

        // Sample Purchase
        $this->purchase = Purchase::create([
            'invoice_number' => 'PO-20260920-001',
            'supplier_invoice_number' => 'SUPP-INV-999',
            'supplier_id' => $this->supplier->id,
            'user_id' => $this->admin->id,
            'purchase_date' => Carbon::today(),
            'payment_type' => 'tempo',
            'due_date' => Carbon::today()->addDays(14),
            'payment_status' => 'partial',
            'total_amount' => 300000,
            'paid_amount' => 100000,
            'remaining_amount' => 200000,
        ]);

        PurchaseItem::create([
            'purchase_id' => $this->purchase->id,
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'quantity' => 10,
            'unit_cost' => 30000,
            'subtotal' => 300000,
        ]);
    }

    public function test_admin_can_view_formal_invoice(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.print.invoice.formal', $this->transaction->id));

        $response->assertStatus(200)
            ->assertSee('FAKTUR PENJUALAN')
            ->assertSee($this->transaction->invoice_number)
            ->assertSee('Nugget Crispy 500g');
    }

    public function test_admin_can_view_sales_report_print_and_export_excel(): void
    {
        $responsePrint = $this->actingAs($this->admin)
            ->get(route('admin.print.sales.report'));

        $responsePrint->assertStatus(200)
            ->assertSee('LAPORAN PENJUALAN')
            ->assertSee($this->transaction->invoice_number);

        $responseCsv = $this->actingAs($this->admin)
            ->get(route('admin.print.sales.excel'));

        $responseCsv->assertStatus(200)
            ->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    public function test_admin_can_view_profit_loss_report_and_export_excel(): void
    {
        $responsePrint = $this->actingAs($this->admin)
            ->get(route('admin.print.profit-loss.report'));

        $responsePrint->assertStatus(200)
            ->assertSee('LAPORAN LABA RUGI')
            ->assertSee('Penjualan Bersih Toko (Gross Sales)')
            ->assertSee('LABA BERSIH RIIL USAHA');

        $responseCsv = $this->actingAs($this->admin)
            ->get(route('admin.print.profit-loss.excel'));

        $responseCsv->assertStatus(200)
            ->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    public function test_admin_can_view_inventory_valuation_and_export_excel(): void
    {
        $responsePrint = $this->actingAs($this->admin)
            ->get(route('admin.print.inventory-valuation.report'));

        $responsePrint->assertStatus(200)
            ->assertSee('VALUASI NILAI STOK')
            ->assertSee('Nugget Crispy 500g');

        $responseCsv = $this->actingAs($this->admin)
            ->get(route('admin.print.inventory-valuation.excel'));

        $responseCsv->assertStatus(200)
            ->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    public function test_admin_can_view_stock_opname_worksheet(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.print.stock-opname.sheet'));

        $response->assertStatus(200)
            ->assertSee('FORM STOCK OPNAME')
            ->assertSee('Nugget Crispy 500g');
    }

    public function test_admin_can_view_receivables_book_invoice_and_export_excel(): void
    {
        $responseBook = $this->actingAs($this->admin)
            ->get(route('admin.print.receivables.book'));

        $responseBook->assertStatus(200)
            ->assertSee('BUKU PIUTANG')
            ->assertSee('Resto Berkah');

        $responseInvoice = $this->actingAs($this->admin)
            ->get(route('admin.print.receivables.invoice', $this->transaction->id));

        $responseInvoice->assertStatus(200)
            ->assertSee('SURAT TAGIHAN TEMPO')
            ->assertSee('Resto Berkah');

        $responseCsv = $this->actingAs($this->admin)
            ->get(route('admin.print.receivables.excel'));

        $responseCsv->assertStatus(200)
            ->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    public function test_admin_can_view_purchase_order_and_accounts_payable(): void
    {
        $responsePo = $this->actingAs($this->admin)
            ->get(route('admin.print.purchases.po', $this->purchase->id));

        $responsePo->assertStatus(200)
            ->assertSee('PURCHASE ORDER')
            ->assertSee('PT Sumber Pangan Sejahtera');

        $responseAp = $this->actingAs($this->admin)
            ->get(route('admin.print.accounts-payable.report'));

        $responseAp->assertStatus(200)
            ->assertSee('BUKU HUTANG SUPPLIER')
            ->assertSee('PT Sumber Pangan Sejahtera');

        $responseCsv = $this->actingAs($this->admin)
            ->get(route('admin.print.accounts-payable.excel'));

        $responseCsv->assertStatus(200)
            ->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    public function test_admin_can_view_payroll_summary_and_export_excel(): void
    {
        $employee = Employee::create([
            'employee_code' => 'EMP-001',
            'name' => 'Siti Rahma',
            'position' => 'Kasir Senior',
            'employment_status' => 'tetap',
            'phone' => '081234567000',
            'base_salary' => 2500000,
            'is_active' => true,
        ]);

        Payroll::create([
            'payroll_number' => 'PAY-202609-001',
            'employee_id' => $employee->id,
            'period_month' => now()->month,
            'period_year' => now()->year,
            'payment_date' => Carbon::today(),
            'base_salary' => 2500000,
            'allowance_amount' => 200000,
            'loan_deduction' => 0,
            'other_deduction' => 0,
            'net_salary' => 2700000,
            'payment_method' => 'transfer',
            'status' => 'paid',
            'created_by' => $this->admin->id,
        ]);

        $responsePayroll = $this->actingAs($this->admin)
            ->get(route('admin.print.payroll.summary'));

        $responsePayroll->assertStatus(200)
            ->assertSee('REKAP GAJI BULANAN')
            ->assertSee('Siti Rahma');

        $responseCsv = $this->actingAs($this->admin)
            ->get(route('admin.print.payroll.excel'));

        $responseCsv->assertStatus(200)
            ->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }
}
