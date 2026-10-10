<?php

namespace Tests\Feature;

use App\Livewire\Admin\Customer\CustomerIndex;
use App\Livewire\Pos\PosTerminal;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CustomerManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_crud_customer(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        // 1. Create Customer
        Livewire::actingAs($admin)
            ->test(CustomerIndex::class)
            ->set('name', 'Ibu Hajjah Maryam')
            ->set('phone', '08123456789')
            ->set('member_code', 'MBR-9999')
            ->set('address', 'Jl. Merdeka No. 10')
            ->set('notes', 'Pelanggan setia grosir')
            ->call('saveCustomer')
            ->assertSet('showModal', false);

        $this->assertDatabaseHas('customers', [
            'name' => 'Ibu Hajjah Maryam',
            'phone' => '08123456789',
            'member_code' => 'MBR-9999',
            'is_active' => true,
        ]);

        $customer = Customer::where('member_code', 'MBR-9999')->first();

        // 2. Edit Customer
        Livewire::actingAs($admin)
            ->test(CustomerIndex::class)
            ->call('openEditModal', $customer->id)
            ->set('name', 'Ibu Hajjah Maryam Updated')
            ->call('saveCustomer')
            ->assertSet('showModal', false);

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'name' => 'Ibu Hajjah Maryam Updated',
        ]);

        // 3. Toggle Status
        Livewire::actingAs($admin)
            ->test(CustomerIndex::class)
            ->call('toggleStatus', $customer->id);

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'is_active' => false,
        ]);
    }

    public function test_pos_terminal_applies_loyalty_price_when_member_is_selected(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier', 'is_active' => true]);
        $category = Category::create(['name' => 'Nugget & Sosis', 'slug' => 'nugget-sosis']);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Fiesta Crispy Nugget 500g',
            'barcode' => '8992775210011',
            'cost_price' => 45000,
            'selling_price' => 52000,
            'loyalty_price' => 49000, // Harga diskon member
            'stock' => 10,
            'unit' => 'pack',
            'is_active' => true,
        ]);

        $customer = Customer::create([
            'name' => 'Pak Bambang Sentosa',
            'phone' => '085712345678',
            'member_code' => 'MBR-0001',
            'is_active' => true,
        ]);

        // Step 1: Add to cart as Regular customer -> Selling price is 52.000
        $test = Livewire::actingAs($cashier)
            ->test(PosTerminal::class)
            ->call('addToCart', $product->id)
            ->assertSet('cart.' . $product->id . ':unit.price', 52000.0)
            ->assertSet('subtotal', 52000.0);

        // Step 2: Select Member -> Price automatically recalculates to 49.000 (Loyalty)
        $test->call('selectCustomer', $customer->id)
            ->assertSet('selectedCustomerId', $customer->id)
            ->assertSet('isLoyalty', true)
            ->assertSet('cart.' . $product->id . ':unit.price', 49000.0)
            ->assertSet('subtotal', 49000.0);

        // Step 3: Complete payment
        $test->set('paidAmount', 50000.0)
            ->set('paymentMethod', 'cash')
            ->call('processPayment')
            ->assertSet('showReceiptModal', true);

        // Verify Database has customer_id and is_loyalty = true
        $this->assertDatabaseHas('transactions', [
            'customer_id' => $customer->id,
            'customer_name' => 'Pak Bambang Sentosa',
            'is_loyalty' => true,
            'grand_total' => 49000.0,
            'paid_amount' => 50000.0,
            'change_amount' => 1000.0,
        ]);
    }
}
