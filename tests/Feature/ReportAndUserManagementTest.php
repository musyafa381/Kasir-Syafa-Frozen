<?php

namespace Tests\Feature;

use App\Livewire\Admin\Report\ProfitLossReport;
use App\Livewire\Admin\Report\SalesReport;
use App\Livewire\Admin\User\UserIndex;
use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ReportAndUserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_sales_report_and_metrics(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $cashier = User::factory()->create(['role' => 'cashier', 'is_active' => true]);

        $category = Category::create(['name' => 'Nugget & Sosis', 'slug' => 'nugget-sosis']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Fiesta Nugget 500g',
            'cost_price' => 35000,
            'selling_price' => 45000,
            'stock' => 50,
            'unit' => 'pack',
            'is_active' => true,
        ]);

        $transaction = Transaction::create([
            'invoice_number' => 'INV-20260916-0001',
            'user_id' => $cashier->id,
            'customer_name' => 'Pelanggan Umum',
            'subtotal' => 90000,
            'discount_amount' => 5000,
            'grand_total' => 85000,
            'paid_amount' => 100000,
            'change_amount' => 15000,
            'payment_method' => 'cash',
            'status' => 'completed',
        ]);

        TransactionItem::create([
            'transaction_id' => $transaction->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'cost_price' => 35000,
            'unit_price' => 45000,
            'quantity' => 2,
            'subtotal' => 90000,
        ]);

        // Test Sales Report
        Livewire::actingAs($admin)
            ->test(SalesReport::class)
            ->assertSet('period', 'this_month')
            ->assertSee('INV-20260916-0001')
            ->assertSee('85.000')
            ->call('openDetail', $transaction->id)
            ->assertSet('showDetailModal', true)
            ->assertSee('Fiesta Nugget 500g');

        // Test Dedicated Profit Loss Report Page
        Livewire::actingAs($admin)
            ->test(ProfitLossReport::class)
            ->assertSet('period', 'this_month')
            ->assertSee('Laporan Arus Keuangan')
            ->assertSee('Laba Bersih Riil')
            ->assertSee('85.000');
    }

    public function test_admin_can_crud_user_accounts(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        // 1. Create Cashier User
        Livewire::actingAs($admin)
            ->test(UserIndex::class)
            ->set('name', 'Siti Rahmawati')
            ->set('email', 'siti@syafafrozen.com')
            ->set('phone', '08987654321')
            ->set('role', 'cashier')
            ->set('password', 'rahasia123')
            ->set('is_active', true)
            ->call('saveUser')
            ->assertSet('showModal', false);

        $this->assertDatabaseHas('users', [
            'name' => 'Siti Rahmawati',
            'email' => 'siti@syafafrozen.com',
            'role' => 'cashier',
            'is_active' => true,
        ]);

        $user = User::where('email', 'siti@syafafrozen.com')->first();

        // 2. Edit User
        Livewire::actingAs($admin)
            ->test(UserIndex::class)
            ->call('openEditModal', $user->id)
            ->set('name', 'Siti Rahmawati SPd')
            ->call('saveUser')
            ->assertSet('showModal', false);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Siti Rahmawati SPd',
        ]);

        // 3. Toggle Status
        Livewire::actingAs($admin)
            ->test(UserIndex::class)
            ->call('toggleStatus', $user->id);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'is_active' => false,
        ]);
    }
}
