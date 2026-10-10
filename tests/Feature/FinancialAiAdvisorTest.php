<?php

namespace Tests\Feature;

use App\Livewire\Admin\Finance\FinancialAiAdvisor;
use App\Models\Category;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FinancialAiAdvisorTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $cashier;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->cashier = User::factory()->create([
            'role' => 'cashier',
            'is_active' => true,
        ]);

        $this->category = Category::create([
            'name' => 'Nugget & Frozen',
            'slug' => 'nugget-frozen',
        ]);
    }

    public function test_admin_can_access_financial_ai_advisor_page(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.finance.ai-advisor'))
            ->assertStatus(200)
            ->assertSee('Penasihat Keuangan')
            ->assertSee('Target Penjualan Minimum');
    }

    public function test_cashier_cannot_access_financial_ai_advisor_page(): void
    {
        $this->actingAs($this->cashier)
            ->get(route('admin.finance.ai-advisor'))
            ->assertRedirect(route('pos.index'));
    }

    public function test_financial_ai_advisor_computes_bep_and_metrics_accurately(): void
    {
        // 1. Create Product
        $product = Product::create([
            'category_id' => $this->category->id,
            'name' => 'Nugget Premium',
            'cost_price' => 20000,
            'selling_price' => 30000,
            'stock' => 50,
            'is_active' => true,
        ]);

        // 2. Create Completed Transaction (Omzet Rp 300.000, HPP Rp 200.000, Margin Rp 100.000 = 33.3%)
        $trx = Transaction::create([
            'invoice_number' => 'TRX-TEST-001',
            'user_id' => $this->cashier->id,
            'status' => 'completed',
            'total_amount' => 300000,
            'discount_amount' => 0,
            'grand_total' => 300000,
            'paid_amount' => 300000,
            'change_amount' => 0,
            'payment_status' => 'paid',
            'payment_method' => 'cash',
            'created_at' => now(),
        ]);

        TransactionItem::create([
            'transaction_id' => $trx->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 10,
            'price' => 30000,
            'cost_price' => 20000,
            'subtotal' => 300000,
        ]);

        // 3. Create Operational Expenses (Electricity Rp 50.000)
        Expense::create([
            'expense_number' => 'EXP-TEST-001',
            'user_id' => $this->admin->id,
            'title' => 'Listrik PLN Freezer Toko',
            'category' => 'electricity_utility',
            'amount' => 50000,
            'expense_date' => now()->toDateString(),
            'payment_method' => 'cash',
        ]);

        $this->actingAs($this->admin);

        Livewire::test(FinancialAiAdvisor::class)
            ->assertSet('analysisPeriod', '30_days')
            ->assertViewHas('netSales', 300000.0)
            ->assertViewHas('totalOpex', 50000.0)
            ->assertViewHas('electricityOpex', 50000.0)
            ->assertViewHas('netProfit', 50000.0)
            ->assertSee('Titik Impas (BEP)');
    }

    public function test_financial_ai_advisor_simulator_reacts_to_interactive_inputs(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(FinancialAiAdvisor::class)
            ->set('simAssetPrice', 8000000)
            ->set('simEstimatedMonthlyRevenue', 5000000)
            ->set('simExtraElectricity', 300000)
            ->assertSet('simAssetPrice', 8000000.0)
            ->assertSet('simEstimatedMonthlyRevenue', 5000000.0)
            ->set('simEmployeeSalary', 2000000)
            ->assertSet('simEmployeeSalary', 2000000.0);
    }
}
