<?php

/**
 * Comprehensive Web Functionality Audit Script
 * Tests all pages load correctly, CRUD operations work, modals function properly
 * Run with: php artisan test --filter=ComprehensiveAuditTest
 */

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Discount;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\FixedAsset;
use App\Models\Outlet;
use App\Models\OutletProduct;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\ReceiptSetting;
use App\Models\RestockPlan;
use App\Models\RestockPlanItem;
use App\Models\SaleReturn;
use App\Models\SaleReturnItem;
use App\Models\StockMutation;
use App\Models\Supplier;
use App\Models\Tax;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ComprehensiveAuditTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $cashier;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->cashier = User::factory()->create([
            'name' => 'Kasir Test',
            'email' => 'kasir@test.com',
            'role' => 'cashier',
            'is_active' => true,
        ]);

        // Create main outlet
        Outlet::create([
            'name' => 'Pusat',
            'code' => 'PST',
            'address' => 'Jl. Test',
            'is_main' => true,
            'is_active' => true,
        ]);

        // Receipt settings
        ReceiptSetting::create([
            'store_name' => 'Syafa Frozen Food',
            'store_address' => 'Jl. Test No. 1',
            'default_paper_size' => '58mm',
        ]);
    }

    // ==========================================
    // 1. PAGE ACCESSIBILITY TESTS (ALL ADMIN PAGES)
    // ==========================================

    /** @test */
    public function all_admin_pages_render_successfully(): void
    {
        $this->actingAs($this->admin);

        $pages = [
            '/admin/dashboard' => 200,
            '/admin/products' => 200,
            '/admin/products/create' => 200,
            '/admin/categories' => 200,
            '/admin/customers' => 200,
            '/admin/suppliers' => 200,
            '/admin/purchases' => 200,
            '/admin/purchases/create' => 200,
            '/admin/purchases/restock-planner' => 200,
            '/admin/stock' => 200,
            '/admin/transactions' => 200,
            '/admin/returns' => 200,
            '/admin/receivables' => 200,
            '/admin/reports' => 200,
            '/admin/reports/laba-rugi' => 200,
            '/admin/reports/valuasi-stok' => 200,
            '/admin/reports/buku-hutang' => 200,
            '/admin/expenses' => 200,
            '/admin/assets' => 200,
            '/admin/finance/ai-advisor' => 200,
            '/admin/employees' => 200,
            '/admin/employees/create' => 200,
            '/admin/outlets' => 200,
            '/admin/taxes' => 200,
            '/admin/discounts' => 200,
            '/admin/users' => 200,
            '/admin/settings/receipt' => 200,
        ];

        foreach ($pages as $url => $expectedCode) {
            $response = $this->get($url);
            $this->assertEquals($expectedCode, $response->getStatusCode(), "Page {$url} returned status {$response->getStatusCode()} instead of {$expectedCode}");
        }
    }

    /** @test */
    public function pos_page_renders_for_cashier(): void
    {
        $this->actingAs($this->cashier);
        $this->get('/pos')->assertStatus(200);
    }

    /** @test */
    public function cashier_cannot_access_admin_only_pages(): void
    {
        $this->actingAs($this->cashier);

        // Cashier CAN access shared pages (dashboard, products, categories, customers, suppliers)
        $sharedPages = [
            '/admin/dashboard',
            '/admin/products',
            '/admin/categories',
            '/admin/customers',
            '/admin/suppliers',
        ];

        foreach ($sharedPages as $url) {
            $response = $this->get($url);
            $this->assertEquals(200, $response->getStatusCode(), "Cashier should have access to {$url}");
        }

        // Cashier CANNOT access admin-only pages
        $adminOnlyPages = [
            '/admin/transactions',
        ];

        foreach ($adminOnlyPages as $url) {
            $response = $this->get($url);
            $this->assertNotEquals(200, $response->getStatusCode(), "Cashier should NOT have access to {$url}");
        }
    }

    /** @test */
    public function guest_is_redirected_to_login(): void
    {
        $this->get('/admin/dashboard')->assertRedirect('/login');
        $this->get('/pos')->assertRedirect('/login');
    }

    // ==========================================
    // 2. CATEGORY CRUD + MODAL TESTS
    // ==========================================

    /** @test */
    public function category_full_crud_lifecycle(): void
    {
        $this->actingAs($this->admin);

        // CREATE
        $component = Livewire::test(\App\Livewire\Admin\Category\CategoryIndex::class);
        $component->call('openCreateModal')
            ->assertSet('showModal', true);

        $component->set('name', 'Frozen Seafood')
            ->set('description', 'Produk laut beku')
            ->call('saveCategory')
            ->assertSet('showModal', false);

        $this->assertDatabaseHas('categories', ['name' => 'Frozen Seafood']);
        $category = Category::where('name', 'Frozen Seafood')->first();

        // UPDATE
        $component->call('openEditModal', $category->id)
            ->assertSet('showModal', true)
            ->assertSet('name', 'Frozen Seafood');

        $component->set('name', 'Frozen Seafood Premium')
            ->call('saveCategory')
            ->assertSet('showModal', false);

        $this->assertDatabaseHas('categories', ['name' => 'Frozen Seafood Premium']);

        // DELETE (via confirm + modal)
        $component->call('confirmDelete', $category->id)
            ->assertSet('showDeleteModal', true)
            ->assertSet('deletingCategoryId', $category->id);

        $component->call('deleteCategory')
            ->assertSet('showDeleteModal', false);

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    // ==========================================
    // 3. PRODUCT CRUD + MODAL TESTS
    // ==========================================

    /** @test */
    public function product_full_crud_lifecycle(): void
    {
        $this->actingAs($this->admin);
        $category = Category::create(['name' => 'Test Category', 'slug' => 'test-category']);

        // CREATE
        $component = Livewire::test(\App\Livewire\Admin\Product\ProductIndex::class);
        $component->call('openCreateModal')
            ->assertSet('showModal', true);

        $component->set('name', 'Udang Vaname 500g')
            ->set('category_id', $category->id)
            ->set('cost_price', 25000)
            ->set('selling_price', 35000)
            ->set('stock', 50)
            ->set('min_stock_alert', 10)
            ->set('unit', 'pack')
            ->call('saveProduct')
            ->assertSet('showModal', false);

        $this->assertDatabaseHas('products', ['name' => 'Udang Vaname 500g']);
        $product = Product::where('name', 'Udang Vaname 500g')->first();

        // UPDATE
        $component->call('openEditModal', $product->id)
            ->assertSet('showModal', true)
            ->assertSet('name', 'Udang Vaname 500g');

        $component->set('selling_price', 38000)
            ->call('saveProduct')
            ->assertSet('showModal', false);

        $product->refresh();
        $this->assertEquals(38000, (int) $product->selling_price);

        // TOGGLE STATUS
        $component->call('toggleStatus', $product->id);
        $product->refresh();
        $this->assertFalse($product->is_active);

        // DELETE (via confirm modal)
        $product->update(['is_active' => true]);
        $component->call('confirmDelete', $product->id)
            ->assertSet('showDeleteModal', true);

        $component->call('deleteProduct')
            ->assertSet('showDeleteModal', false);

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    /** @test */
    public function product_with_history_cannot_be_hard_deleted(): void
    {
        $this->actingAs($this->admin);
        $product = Product::create([
            'name' => 'Product With History',
            'slug' => 'product-with-history',
            'cost_price' => 10000,
            'selling_price' => 15000,
            'stock' => 10,
            'min_stock_alert' => 3,
            'unit' => 'pcs',
        ]);

        // Create a transaction referencing this product
        $trx = Transaction::create([
            'invoice_number' => 'INV-TEST-001',
            'user_id' => $this->admin->id,
            'customer_name' => 'Test',
            'subtotal' => 15000,
            'grand_total' => 15000,
            'paid_amount' => 15000,
            'change_amount' => 0,
            'payment_method' => 'cash',
            'status' => 'completed',
        ]);

        TransactionItem::create([
            'transaction_id' => $trx->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 1,
            'unit_price' => 15000,
            'cost_price' => 10000,
            'subtotal' => 15000,
        ]);

        $component = Livewire::test(\App\Livewire\Admin\Product\ProductIndex::class);
        $component->call('confirmDelete', $product->id)
            ->assertSet('showDeleteModal', true)
            ->assertSet('deletingProductHasHistory', true);

        // Try force delete — should block
        $component->call('deleteProduct');

        // Product should still exist
        $this->assertDatabaseHas('products', ['id' => $product->id]);

        // Deactivation should work
        $component->call('confirmDelete', $product->id);
        $component->call('deactivateProduct');
        $product->refresh();
        $this->assertFalse($product->is_active);
    }

    // ==========================================
    // 4. CUSTOMER CRUD + MODAL TESTS
    // ==========================================

    /** @test */
    public function customer_full_crud_lifecycle(): void
    {
        $this->actingAs($this->admin);

        // CREATE
        $component = Livewire::test(\App\Livewire\Admin\Customer\CustomerIndex::class);
        $component->call('openCreateModal')
            ->assertSet('showModal', true);

        $component->set('name', 'Budi Santoso')
            ->set('phone', '081234567890')
            ->call('saveCustomer')
            ->assertSet('showModal', false);

        $this->assertDatabaseHas('customers', ['name' => 'Budi Santoso']);
        $customer = Customer::where('name', 'Budi Santoso')->first();

        // UPDATE
        $component->call('openEditModal', $customer->id)
            ->assertSet('showModal', true);

        $component->set('phone', '089876543210')
            ->call('saveCustomer')
            ->assertSet('showModal', false);

        $customer->refresh();
        $this->assertEquals('089876543210', $customer->phone);

        // TOGGLE
        $component->call('toggleStatus', $customer->id);
        $customer->refresh();
        $this->assertFalse($customer->is_active);

        // DELETE (via modal)
        $component->call('confirmDelete', $customer->id)
            ->assertSet('showDeleteModal', true);

        $component->call('deleteCustomer')
            ->assertSet('showDeleteModal', false);

        $this->assertDatabaseMissing('customers', ['id' => $customer->id]);
    }

    // ==========================================
    // 5. SUPPLIER CRUD + MODAL TESTS
    // ==========================================

    /** @test */
    public function supplier_full_crud_lifecycle(): void
    {
        $this->actingAs($this->admin);

        // CREATE
        $component = Livewire::test(\App\Livewire\Admin\Supplier\SupplierIndex::class);
        $component->call('openCreateModal')
            ->assertSet('showFormModal', true);

        $component->set('name', 'PT Ikan Segar')
            ->set('phone', '021-12345')
            ->set('contact_person', 'Hadi')
            ->set('payment_term', 'cash')
            ->call('saveSupplier')
            ->assertSet('showFormModal', false);

        $this->assertDatabaseHas('suppliers', ['name' => 'PT Ikan Segar']);
        $supplier = Supplier::where('name', 'PT Ikan Segar')->first();

        // UPDATE
        $component->call('openEditModal', $supplier->id)
            ->assertSet('showFormModal', true);

        $component->set('phone', '021-99999')
            ->call('saveSupplier')
            ->assertSet('showFormModal', false);

        // DETAIL MODAL
        $component->call('openDetail', $supplier->id)
            ->assertSet('showDetailModal', true);
        $component->call('closeDetail')
            ->assertSet('showDetailModal', false);

        // DELETE (via modal)
        $component->call('confirmDelete', $supplier->id)
            ->assertSet('showDeleteModal', true);

        $component->call('deleteSupplier')
            ->assertSet('showDeleteModal', false);

        $this->assertDatabaseMissing('suppliers', ['id' => $supplier->id]);
    }

    // ==========================================
    // 6. TAX CRUD + MODAL TESTS
    // ==========================================

    /** @test */
    public function tax_full_crud_lifecycle(): void
    {
        $this->actingAs($this->admin);

        // CREATE
        $component = Livewire::test(\App\Livewire\Admin\Tax\TaxIndex::class);
        $component->call('openCreateModal')
            ->assertSet('showModal', true);

        $component->set('name', 'PPN 11%')
            ->set('code', 'PPN11')
            ->set('rate', 11.00)
            ->set('is_default', true)
            ->call('saveTax')
            ->assertSet('showModal', false);

        $this->assertDatabaseHas('taxes', ['name' => 'PPN 11%']);
        $tax = Tax::where('name', 'PPN 11%')->first();
        $this->assertTrue($tax->is_default);

        // UPDATE
        $component->call('openEditModal', $tax->id);
        $component->set('rate', 12.00)
            ->call('saveTax')
            ->assertSet('showModal', false);

        $tax->refresh();
        $this->assertEquals(12.00, (float) $tax->rate);

        // TOGGLE
        $component->call('toggleStatus', $tax->id);
        $tax->refresh();
        $this->assertFalse($tax->is_active);
        $this->assertFalse($tax->is_default); // deactivating clears default

        // DELETE (via modal)
        $component->call('confirmDelete', $tax->id)
            ->assertSet('showDeleteModal', true);
        $component->call('deleteTax')
            ->assertSet('showDeleteModal', false);

        $this->assertDatabaseMissing('taxes', ['id' => $tax->id]);
    }

    // ==========================================
    // 7. DISCOUNT CRUD + MODAL TESTS
    // ==========================================

    /** @test */
    public function discount_full_crud_lifecycle(): void
    {
        $this->actingAs($this->admin);

        // CREATE
        $component = Livewire::test(\App\Livewire\Admin\Discount\DiscountIndex::class);
        $component->call('openCreateModal')
            ->assertSet('showModal', true);

        $component->set('name', 'Promo Weekend')
            ->set('type', 'fixed')
            ->set('value', 5000)
            ->call('saveDiscount')
            ->assertSet('showModal', false);

        $this->assertDatabaseHas('discounts', ['name' => 'Promo Weekend']);
        $discount = Discount::where('name', 'Promo Weekend')->first();

        // UPDATE
        $component->call('openEditModal', $discount->id);
        $component->set('value', 10000)
            ->call('saveDiscount')
            ->assertSet('showModal', false);

        $discount->refresh();
        $this->assertEquals(10000, (int) $discount->value);

        // DELETE (via modal)
        $component->call('confirmDelete', $discount->id)
            ->assertSet('showDeleteModal', true);
        $component->call('deleteDiscount')
            ->assertSet('showDeleteModal', false);

        $this->assertDatabaseMissing('discounts', ['id' => $discount->id]);
    }

    // ==========================================
    // 8. EXPENSE CRUD + MODAL TESTS
    // ==========================================

    /** @test */
    public function expense_full_crud_lifecycle(): void
    {
        $this->actingAs($this->admin);

        // CREATE
        $component = Livewire::test(\App\Livewire\Admin\Finance\ExpenseIndex::class);
        $component->call('openCreateModal')
            ->assertSet('showFormModal', true);

        $component->set('expenseCategory', 'electricity_utility')
            ->set('expenseTitle', 'Bayar PLN')
            ->set('expenseAmount', 500000)
            ->set('expenseDate', '2026-09-23')
            ->set('expensePaymentMethod', 'cash')
            ->call('saveExpense')
            ->assertSet('showFormModal', false);

        $this->assertDatabaseHas('expenses', ['title' => 'Bayar PLN']);
        $expense = Expense::where('title', 'Bayar PLN')->first();

        // UPDATE
        $component->call('openEditModal', $expense->id)
            ->assertSet('showFormModal', true);
        $component->set('expenseAmount', 600000)
            ->call('saveExpense')
            ->assertSet('showFormModal', false);

        $expense->refresh();
        $this->assertEquals(600000, (int) $expense->amount);

        // DELETE (via modal)
        $component->call('confirmDelete', $expense->id)
            ->assertSet('showDeleteModal', true);
        $component->call('deleteExpense')
            ->assertSet('showDeleteModal', false);

        $this->assertDatabaseMissing('expenses', ['id' => $expense->id]);
    }

    // ==========================================
    // 9. RESTOCK PLANNER - DELETE MODAL TEST
    // ==========================================

    /** @test */
    public function restock_planner_delete_modal_works(): void
    {
        $this->actingAs($this->admin);
        $supplier = Supplier::create([
            'name' => 'Supplier Restock',
            'code' => 'SUP-RS1',
            'is_active' => true,
            'payment_term' => 'cash',
        ]);

        $product = Product::create([
            'name' => 'Produk Restock Test',
            'slug' => 'produk-restock-test',
            'cost_price' => 10000,
            'selling_price' => 15000,
            'stock' => 5,
            'min_stock_alert' => 10,
            'unit' => 'pcs',
            'supplier_id' => $supplier->id,
        ]);

        $plan = RestockPlan::create([
            'plan_number' => 'PO-PLAN-TEST-0001',
            'supplier_id' => $supplier->id,
            'user_id' => $this->admin->id,
            'status' => 'draft',
            'total_estimated_cost' => 100000,
        ]);

        RestockPlanItem::create([
            'restock_plan_id' => $plan->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'current_stock' => 5,
            'min_stock_alert' => 10,
            'quantity_ordered' => 10,
            'unit' => 'pcs',
            'unit_cost' => 10000,
            'subtotal' => 100000,
        ]);

        $component = Livewire::test(\App\Livewire\Admin\Purchase\RestockPlanner::class);

        // Open delete confirmation modal
        $component->call('confirmDelete', $plan->id)
            ->assertSet('showDeleteModal', true)
            ->assertSet('deletingPlanId', $plan->id);

        // Confirm delete
        $component->call('deletePlan')
            ->assertSet('showDeleteModal', false);

        $this->assertDatabaseMissing('restock_plans', ['id' => $plan->id]);
        $this->assertDatabaseMissing('restock_plan_items', ['restock_plan_id' => $plan->id]);
    }

    // ==========================================
    // 10. SALE RETURN - DELETE MODAL + STOCK ROLLBACK TEST
    // ==========================================

    /** @test */
    public function sale_return_delete_modal_with_stock_rollback(): void
    {
        $this->actingAs($this->admin);

        $product = Product::create([
            'name' => 'Produk Retur Test',
            'slug' => 'produk-retur-test',
            'cost_price' => 10000,
            'selling_price' => 15000,
            'stock' => 20, // After restock from return
            'min_stock_alert' => 5,
            'unit' => 'pcs',
        ]);

        $trx = Transaction::create([
            'invoice_number' => 'INV-RET-001',
            'user_id' => $this->admin->id,
            'customer_name' => 'Customer Retur',
            'subtotal' => 30000,
            'grand_total' => 30000,
            'paid_amount' => 30000,
            'change_amount' => 0,
            'payment_method' => 'cash',
            'status' => 'completed',
        ]);

        $return = SaleReturn::create([
            'return_number' => 'RET-TEST-001',
            'transaction_id' => $trx->id,
            'invoice_number' => $trx->invoice_number,
            'user_id' => $this->admin->id,
            'total_refund' => 15000,
            'total_items' => 1,
            'reason' => 'Barang Rusak / Cacat',
            'restock' => true,
        ]);

        SaleReturnItem::create([
            'sale_return_id' => $return->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 1,
            'unit_price' => 15000,
            'subtotal' => 15000,
        ]);

        $component = Livewire::test(\App\Livewire\Admin\Transaction\SaleReturnIndex::class);

        // Open delete confirmation modal
        $component->call('confirmDelete', $return->id)
            ->assertSet('showDeleteModal', true)
            ->assertSet('deletingReturnId', $return->id);

        // Confirm delete — should rollback stock
        $component->call('deleteReturn')
            ->assertSet('showDeleteModal', false);

        // Return should be deleted
        $this->assertDatabaseMissing('sale_returns', ['id' => $return->id]);

        // Stock should have been decremented (rolled back from the restock)
        $product->refresh();
        $this->assertEquals(19, $product->stock); // 20 - 1 = 19

        // Stock mutation 'out' should be recorded
        $this->assertDatabaseHas('stock_mutations', [
            'product_id' => $product->id,
            'type' => 'out',
            'quantity' => 1,
        ]);
    }

    // ==========================================
    // 11. TRANSACTION OPERATIONS TEST
    // ==========================================

    /** @test */
    public function transaction_drill_down_navigation_works(): void
    {
        $this->actingAs($this->admin);

        $component = Livewire::test(\App\Livewire\Admin\Transaction\TransactionIndex::class);

        // Navigate: Yearly → Monthly → Daily → Transactions
        $component->assertSet('viewLevel', 'yearly');

        $component->call('goToMonthly', 2026)
            ->assertSet('viewLevel', 'monthly')
            ->assertSet('selectedYear', 2026);

        $component->call('goToDaily', 2026, 9)
            ->assertSet('viewLevel', 'daily')
            ->assertSet('selectedYear', 2026)
            ->assertSet('selectedMonth', 9);

        $component->call('goToTransactions', '2026-09-23')
            ->assertSet('viewLevel', 'transactions')
            ->assertSet('selectedDate', '2026-09-23');

        // Back to yearly
        $component->call('goToYearly')
            ->assertSet('viewLevel', 'yearly');
    }

    /** @test */
    public function transaction_cancel_restores_stock(): void
    {
        $this->actingAs($this->admin);

        $product = Product::create([
            'name' => 'Delete Test',
            'slug' => 'delete-test',
            'cost_price' => 5000,
            'selling_price' => 10000,
            'stock' => 8,
            'min_stock_alert' => 3,
            'unit' => 'pcs',
        ]);

        $trx = Transaction::create([
            'invoice_number' => 'INV-DEL-001',
            'user_id' => $this->admin->id,
            'customer_name' => 'Test',
            'subtotal' => 20000,
            'grand_total' => 20000,
            'paid_amount' => 20000,
            'change_amount' => 0,
            'payment_method' => 'cash',
            'status' => 'completed',
        ]);

        TransactionItem::create([
            'transaction_id' => $trx->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 2,
            'unit_price' => 10000,
            'cost_price' => 5000,
            'subtotal' => 20000,
        ]);

        $component = Livewire::test(\App\Livewire\Admin\Transaction\TransactionIndex::class, [
            'viewLevel' => 'transactions',
            'selectedDate' => '2026-09-23',
        ]);

        // Open delete confirmation modal
        $component->call('confirmDelete', $trx->id)
            ->assertSet('showDeleteModal', true);

        // Confirm cancel → stock should be restored
        $component->call('deleteTransaction')
            ->assertSet('showDeleteModal', false);

        $this->assertDatabaseMissing('transactions', ['id' => $trx->id]);
        $product->refresh();
        $this->assertEquals(10, $product->stock); // 8 + 2 = 10
    }

    // ==========================================
    // 12. DASHBOARD MODAL TESTS
    // ==========================================

    /** @test */
    public function dashboard_transaction_detail_modal_works(): void
    {
        $this->actingAs($this->admin);

        $trx = Transaction::create([
            'invoice_number' => 'INV-DASH-001',
            'user_id' => $this->admin->id,
            'customer_name' => 'Dashboard Customer',
            'subtotal' => 50000,
            'grand_total' => 50000,
            'paid_amount' => 50000,
            'change_amount' => 0,
            'payment_method' => 'cash',
            'status' => 'completed',
        ]);

        $component = Livewire::test(\App\Livewire\Admin\Dashboard::class);

        // Open transaction detail modal
        $component->call('openTransactionDetail', $trx->id)
            ->assertSet('showDetailModal', true)
            ->assertSet('selectedTransactionId', $trx->id);

        // Close
        $component->call('closeTransactionDetail')
            ->assertSet('showDetailModal', false)
            ->assertSet('selectedTransactionId', null);
    }

    // ==========================================
    // 13. USER MANAGEMENT TEST
    // ==========================================

    /** @test */
    public function user_crud_lifecycle(): void
    {
        $this->actingAs($this->admin);

        $component = Livewire::test(\App\Livewire\Admin\User\UserIndex::class);

        // CREATE
        $component->call('openCreateModal')
            ->assertSet('showModal', true);

        $component->set('name', 'New Cashier')
            ->set('email', 'newcashier@test.com')
            ->set('password', 'password123')
            ->set('role', 'cashier')
            ->call('saveUser')
            ->assertSet('showModal', false);

        $this->assertDatabaseHas('users', ['email' => 'newcashier@test.com']);
        $newUser = User::where('email', 'newcashier@test.com')->first();

        // DELETE
        $component->call('confirmDelete', $newUser->id)
            ->assertSet('showDeleteModal', true);
        $component->call('deleteUser')
            ->assertSet('showDeleteModal', false);

        $this->assertDatabaseMissing('users', ['id' => $newUser->id]);
    }

    // ==========================================
    // 14. STOCK ADJUSTMENT MODAL TEST
    // ==========================================

    /** @test */
    public function stock_adjustment_in_and_out(): void
    {
        $this->actingAs($this->admin);

        $product = Product::create([
            'name' => 'Stock Adjust Test',
            'slug' => 'stock-adjust-test',
            'cost_price' => 5000,
            'selling_price' => 10000,
            'stock' => 20,
            'min_stock_alert' => 5,
            'unit' => 'pcs',
        ]);

        $component = Livewire::test(\App\Livewire\Admin\Stock\StockAdjustment::class);

        // Stock In
        $component->call('openAdjustmentModal', $product->id)
            ->assertSet('showFormModal', true)
            ->assertSet('type', 'in');

        $component->set('quantity', 5)
            ->set('description', 'Penerimaan tambahan')
            ->call('saveAdjustment')
            ->assertSet('showFormModal', false);

        $product->refresh();
        $this->assertEquals(25, $product->stock);

        // Stock Out
        $component->call('openAdjustmentModal', $product->id);
        $component->set('type', 'out')
            ->set('quantity', 3)
            ->set('description', 'Barang rusak')
            ->call('saveAdjustment');

        $product->refresh();
        $this->assertEquals(22, $product->stock);

        // Verify mutations
        $this->assertDatabaseHas('stock_mutations', [
            'product_id' => $product->id,
            'type' => 'in',
            'quantity' => 5,
        ]);
        $this->assertDatabaseHas('stock_mutations', [
            'product_id' => $product->id,
            'type' => 'out',
            'quantity' => -3,
        ]);
    }

    // ==========================================
    // 15. POS TERMINAL CHECKOUT FLOW TEST
    // ==========================================

    /** @test */
    public function pos_full_checkout_flow(): void
    {
        $this->actingAs($this->cashier);
        $outlet = Outlet::where('is_main', true)->first();
        $this->cashier->update(['outlet_id' => $outlet->id]);

        $product = Product::create([
            'name' => 'Pos Test Product',
            'slug' => 'pos-test-product',
            'cost_price' => 5000,
            'selling_price' => 12000,
            'stock' => 30,
            'min_stock_alert' => 5,
            'unit' => 'pcs',
        ]);

        OutletProduct::create([
            'outlet_id' => $outlet->id,
            'product_id' => $product->id,
            'selling_price' => 12000,
            'cost_price' => 5000,
            'stock' => 30,
        ]);

        $component = Livewire::test(\App\Livewire\Pos\PosTerminal::class);

        // Add to cart
        $component->call('addToCart', $product->id);
        $cartKey = "{$product->id}:unit";
        $this->assertArrayHasKey($cartKey, $component->get('cart'));

        // Increase quantity
        $component->call('incrementQty', $cartKey);
        $component->call('incrementQty', $cartKey);
        $this->assertEquals(3, $component->get('cart')[$cartKey]['qty']);

        // Set step to payment & proceed to checkout
        $component->call('setStep', 'payment');
        $component->set('paymentMethod', 'cash')
            ->set('paidAmount', 50000)
            ->call('processPayment');

        // Transaction should be created
        $this->assertDatabaseHas('transactions', [
            'status' => 'completed',
            'payment_method' => 'cash',
        ]);

        // Stock should be decremented
        $product->refresh();
        $this->assertEquals(27, $product->stock); // 30 - 3 = 27
    }

    // ==========================================
    // 16. PURCHASE CREATE + STOCK INCREMENT TEST
    // ==========================================

    /** @test */
    public function purchase_creates_and_updates_stock(): void
    {
        $this->actingAs($this->admin);
        $supplier = Supplier::create([
            'name' => 'Purchase Supplier',
            'code' => 'SUP-PUR',
            'is_active' => true,
            'payment_term' => 'cash',
        ]);

        $product = Product::create([
            'name' => 'Purchase Product',
            'slug' => 'purchase-product',
            'cost_price' => 8000,
            'selling_price' => 15000,
            'stock' => 10,
            'min_stock_alert' => 5,
            'unit' => 'pcs',
            'supplier_id' => $supplier->id,
        ]);

        $component = Livewire::test(\App\Livewire\Admin\Purchase\PurchaseCreate::class);

        $component->set('supplier_id', $supplier->id)
            ->set('payment_type', 'cash')
            ->set('paid_amount', 80000)
            ->set('notes', 'Test purchase');

        // Add item
        $component->set('selectedProductId', $product->id)
            ->set('inputQuantity', 10)
            ->set('inputUnitCost', 8000)
            ->call('addToCart');

        // Process
        $component->call('savePurchase');

        // Stock should increase
        $product->refresh();
        $this->assertEquals(20, $product->stock); // 10 + 10

        $this->assertDatabaseHas('purchases', [
            'supplier_id' => $supplier->id,
            'payment_type' => 'cash',
        ]);
    }

    // ==========================================
    // 17. RECEIPT SETTINGS TEST
    // ==========================================

    /** @test */
    public function receipt_settings_can_be_updated(): void
    {
        $this->actingAs($this->admin);

        $component = Livewire::test(\App\Livewire\Admin\Setting\ReceiptSettingManager::class);

        $component->set('store_name', 'Syafa Frozen Food Updated')
            ->set('store_address', 'Jl. Baru No. 2')
            ->set('store_phone', '08123456')
            ->call('save');

        $settings = ReceiptSetting::getSettings();
        $this->assertEquals('Syafa Frozen Food Updated', $settings->store_name);
    }

    // ==========================================
    // 18. TRANSACTION RETUR FLOW TEST
    // ==========================================

    /** @test */
    public function transaction_return_process_works(): void
    {
        $this->actingAs($this->admin);

        $product = Product::create([
            'name' => 'Return Flow Product',
            'slug' => 'return-flow-product',
            'cost_price' => 5000,
            'selling_price' => 10000,
            'stock' => 7,
            'min_stock_alert' => 3,
            'unit' => 'pcs',
        ]);

        $trx = Transaction::create([
            'invoice_number' => 'INV-RETFLOW-001',
            'user_id' => $this->admin->id,
            'customer_name' => 'Customer',
            'subtotal' => 20000,
            'grand_total' => 20000,
            'paid_amount' => 20000,
            'change_amount' => 0,
            'payment_method' => 'cash',
            'status' => 'completed',
        ]);

        $item = TransactionItem::create([
            'transaction_id' => $trx->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 2,
            'unit_price' => 10000,
            'cost_price' => 5000,
            'subtotal' => 20000,
        ]);

        $component = Livewire::test(\App\Livewire\Admin\Transaction\TransactionIndex::class, [
            'viewLevel' => 'transactions',
            'selectedDate' => now()->format('Y-m-d'),
        ]);

        // Open return modal
        $component->call('openReturnModal', $trx->id)
            ->assertSet('showReturnModal', true);

        // Select item for return
        $component->set("returnItems.{$item->id}.selected", true)
            ->set("returnItems.{$item->id}.qty", 1)
            ->set('returnReason', 'Barang Rusak / Cacat')
            ->set('returnRestock', true)
            ->call('processReturn')
            ->assertSet('showReturnModal', false);

        // Stock should increase (restocked)
        $product->refresh();
        $this->assertEquals(8, $product->stock); // 7 + 1 = 8

        // Sale return should be created
        $this->assertDatabaseHas('sale_returns', [
            'transaction_id' => $trx->id,
            'total_items' => 1,
            'restock' => true,
        ]);
    }
}
