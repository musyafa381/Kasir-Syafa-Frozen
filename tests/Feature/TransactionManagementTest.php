<?php

namespace Tests\Feature;

use App\Livewire\Admin\Transaction\TransactionIndex;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TransactionManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_transactions_and_open_digital_receipt(): void
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

        $customer = Customer::create([
            'name' => 'Budi Santoso',
            'phone' => '081234567890',
            'member_code' => 'MBR-0001',
            'is_active' => true,
        ]);

        $transaction = Transaction::create([
            'invoice_number' => 'INV-20260916-0001',
            'user_id' => $cashier->id,
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'is_loyalty' => true,
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

        // Test Transaction Listing & Detail Modal
        Livewire::actingAs($admin)
            ->test(TransactionIndex::class)
            ->assertSee('85.000')
            ->call('openDetail', $transaction->id)
            ->assertSet('showDetailModal', true)
            ->assertSee('INV-20260916-0001')
            ->assertSee('Budi Santoso')
            ->assertSee('Fiesta Nugget 500g')
            ->assertSee('Kirim WA')
            ->assertSet('waPhoneNumber', '081234567890');
    }

    public function test_admin_can_edit_transaction(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

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
            'invoice_number' => 'INV-20260916-0002',
            'user_id' => $admin->id,
            'customer_name' => 'Pelanggan Umum',
            'is_loyalty' => false,
            'subtotal' => 45000,
            'discount_amount' => 0,
            'grand_total' => 45000,
            'paid_amount' => 50000,
            'change_amount' => 5000,
            'payment_method' => 'cash',
            'status' => 'completed',
        ]);

        $item = TransactionItem::create([
            'transaction_id' => $transaction->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'cost_price' => 35000,
            'unit_price' => 45000,
            'quantity' => 1,
            'subtotal' => 45000,
        ]);

        // Test Edit Modal & Updating Transaction
        Livewire::actingAs($admin)
            ->test(TransactionIndex::class)
            ->call('openEditModal', $transaction->id)
            ->assertSet('showEditModal', true)
            ->set('editCustomerName', 'Pak Bambang')
            ->set('editPaymentMethod', 'qris')
            ->set('editPaidAmount', 90000)
            ->call('updateItemQuantity', 0, 2)
            ->call('saveEditTransaction')
            ->assertSet('showEditModal', false);

        $transaction->refresh();
        $this->assertEquals('Pak Bambang', $transaction->customer_name);
        $this->assertEquals('qris', $transaction->payment_method);
        $this->assertEquals(90000, $transaction->grand_total);
        $this->assertEquals(90000, $transaction->paid_amount);

        // Product stock should have been decremented by 1 additional unit (from 50 to 49)
        $product->refresh();
        $this->assertEquals(49, $product->stock);
    }

    public function test_admin_can_cancel_transaction_and_rollback_stock(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $category = Category::create(['name' => 'Nugget & Sosis', 'slug' => 'nugget-sosis']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Champ Nugget 500g',
            'cost_price' => 25000,
            'selling_price' => 32000,
            'stock' => 10,
            'unit' => 'pack',
            'is_active' => true,
        ]);

        $transaction = Transaction::create([
            'invoice_number' => 'INV-20260916-0003',
            'user_id' => $admin->id,
            'customer_name' => 'Pelanggan Retur',
            'subtotal' => 64000,
            'discount_amount' => 0,
            'grand_total' => 64000,
            'paid_amount' => 100000,
            'change_amount' => 36000,
            'payment_method' => 'cash',
            'status' => 'completed',
        ]);

        TransactionItem::create([
            'transaction_id' => $transaction->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'cost_price' => 25000,
            'unit_price' => 32000,
            'quantity' => 2,
            'subtotal' => 64000,
        ]);

        Livewire::actingAs($admin)
            ->test(TransactionIndex::class)
            ->call('confirmDelete', $transaction->id)
            ->assertSet('showDeleteModal', true)
            ->call('deleteTransaction')
            ->assertSet('showDeleteModal', false);

        $this->assertDatabaseMissing('transactions', ['id' => $transaction->id]);

        // Stock must have rolled back (+2)
        $product->refresh();
        $this->assertEquals(12, $product->stock);
    }
}
