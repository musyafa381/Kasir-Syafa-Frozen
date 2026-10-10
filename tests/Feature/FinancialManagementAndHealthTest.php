<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FinancialManagementAndHealthTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name' => 'Admin Keuangan',
            'role' => 'admin',
            'is_active' => true,
        ]);
    }

    public function test_can_create_operational_expense()
    {
        $this->actingAs($this->admin);

        Livewire::test(\App\Livewire\Admin\Finance\ExpenseIndex::class)
            ->call('openCreateModal')
            ->set('expenseDate', now()->toDateString())
            ->set('expenseCategory', 'electricity_utility')
            ->set('expenseTitle', 'Tagihan Listrik PLN Freezer Bulan Ini')
            ->set('expenseAmount', 850000)
            ->set('expensePaymentMethod', 'transfer')
            ->set('expenseNotes', 'Pembayaran via Bank BCA')
            ->call('saveExpense')
            ->assertHasNoErrors()
            ->assertSet('showFormModal', false);

        $this->assertDatabaseHas('expenses', [
            'category' => 'electricity_utility',
            'title' => 'Tagihan Listrik PLN Freezer Bulan Ini',
            'amount' => 850000,
            'payment_method' => 'transfer',
        ]);
    }

    public function test_can_update_and_delete_expense()
    {
        $this->actingAs($this->admin);

        $expense = Expense::create([
            'expense_number' => 'EXP-20260917-0001',
            'user_id' => $this->admin->id,
            'expense_date' => now()->toDateString(),
            'category' => 'salary_labor',
            'title' => 'Gaji Karyawan Toko',
            'amount' => 2500000,
            'payment_method' => 'cash',
        ]);

        Livewire::test(\App\Livewire\Admin\Finance\ExpenseIndex::class)
            ->call('openEditModal', $expense->id)
            ->set('expenseAmount', 2750000)
            ->call('saveExpense')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'amount' => 2750000,
        ]);

        Livewire::test(\App\Livewire\Admin\Finance\ExpenseIndex::class)
            ->call('confirmDelete', $expense->id)
            ->call('deleteExpense')
            ->assertSet('showDeleteModal', false);

        $this->assertDatabaseMissing('expenses', [
            'id' => $expense->id,
        ]);
    }

    public function test_can_create_fixed_asset_freezer()
    {
        $this->actingAs($this->admin);

        Livewire::test(\App\Livewire\Admin\Finance\AssetIndex::class)
            ->call('openCreateModal')
            ->set('assetName', 'Chest Freezer RSA 600L')
            ->set('assetCategory', 'freezer_refrigeration')
            ->set('assetBrand', 'RSA')
            ->set('assetSpecs', 'Daya 240 Watt, Kapasitas 600 Liter')
            ->set('assetPurchaseDate', '2026-01-15')
            ->set('assetPurchaseCost', 5500000)
            ->set('assetCondition', 'good')
            ->set('assetLocation', 'Area Toko Depan')
            ->call('saveAsset')
            ->assertHasNoErrors()
            ->assertSet('showFormModal', false);

        $this->assertDatabaseHas('assets', [
            'name' => 'Chest Freezer RSA 600L',
            'category' => 'freezer_refrigeration',
            'purchase_cost' => 5500000,
            'condition' => 'good',
        ]);
    }

    public function test_true_net_profit_and_financial_health_scorecard_calculation()
    {
        $this->actingAs($this->admin);

        // 1. Create Category & Products
        $category = Category::create(['name' => 'Nugget & Sosis', 'slug' => 'nugget-sosis']);
        $product = Product::create([
            'category_id' => $category->id,
            'sku' => 'NGT-001',
            'name' => 'Nugget Fiesta 500g',
            'cost_price' => 35000,
            'selling_price' => 50000,
            'stock' => 100,
            'min_stock' => 10,
            'unit' => 'pack',
            'is_active' => true,
        ]);

        // 2. Create Sale: Sell 10 packs @ 50,000 = Revenue 500,000, COGS = 350,000 => Gross Profit = 150,000
        $transaction = Transaction::create([
            'invoice_number' => 'INV-20260917-0001',
            'user_id' => $this->admin->id,
            'subtotal' => 500000,
            'discount_amount' => 0,
            'tax_amount' => 0,
            'grand_total' => 500000,
            'payment_method' => 'cash',
            'payment_status' => 'paid',
            'status' => 'completed',
            'paid_amount' => 500000,
            'change_amount' => 0,
            'created_at' => now(),
        ]);

        TransactionItem::create([
            'transaction_id' => $transaction->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 10,
            'cost_price' => 35000,
            'unit_price' => 50000,
            'subtotal' => 500000,
        ]);

        // 3. Create OPEX: Listrik 50,000 & Packing 10,000 = Total OPEX 60,000
        Expense::create([
            'expense_number' => 'EXP-20260917-0001',
            'user_id' => $this->admin->id,
            'expense_date' => now()->toDateString(),
            'category' => 'electricity_utility',
            'title' => 'Listrik Freezer',
            'amount' => 50000,
            'payment_method' => 'cash',
            'created_at' => now(),
        ]);

        Expense::create([
            'expense_number' => 'EXP-20260917-0002',
            'user_id' => $this->admin->id,
            'expense_date' => now()->toDateString(),
            'category' => 'packaging_supplies',
            'title' => 'Plastik HD Kresek',
            'amount' => 10000,
            'payment_method' => 'cash',
            'created_at' => now(),
        ]);

        // 4. Create Fixed Asset: Freezer 4,000,000
        Asset::create([
            'asset_code' => 'AST-0001',
            'user_id' => $this->admin->id,
            'name' => 'Freezer RSA 300L',
            'category' => 'freezer_refrigeration',
            'purchase_date' => now()->toDateString(),
            'purchase_cost' => 4000000,
            'condition' => 'good',
        ]);

        // 5. Test Livewire ProfitLossReport
        // Expected:
        // Revenue: 500,000
        // HPP / COGS: 350,000
        // Gross Profit: 150,000
        // Total OPEX: 60,000
        // True Net Profit: 150,000 - 60,000 = 90,000
        Livewire::test(\App\Livewire\Admin\Report\ProfitLossReport::class)
            ->assertViewHas('grossProfit', 150000)
            ->assertViewHas('totalExpenses', 60000)
            ->assertViewHas('trueNetProfit', 90000)
            ->assertViewHas('totalFixedAssetsValuation', 4000000)
            ->assertSee('90.000')
            ->assertSee('Listrik PLN')
            ->assertSee('Plastik Kresek');
    }
}
