<?php

namespace Tests\Feature;

use App\Livewire\Auth\Login;
use App\Livewire\Pos\PosTerminal;
use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PosTerminalTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/pos');
        $response->assertRedirect('/login');
    }

    public function test_cashier_can_access_pos_and_admin_dashboard(): void
    {
        $cashier = User::factory()->create([
            'role' => 'cashier',
            'is_active' => true,
        ]);

        $this->actingAs($cashier);

        $responsePos = $this->get('/pos');
        $responsePos->assertStatus(200);

        $responseAdmin = $this->get('/admin/dashboard');
        $responseAdmin->assertStatus(200);
    }

    public function test_admin_can_access_both_pos_and_admin_dashboard(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->actingAs($admin);

        $responsePos = $this->get('/pos');
        $responsePos->assertStatus(200);

        $responseAdmin = $this->get('/admin/dashboard');
        $responseAdmin->assertStatus(200);
    }

    public function test_pos_terminal_can_add_items_and_checkout(): void
    {
        $cashier = User::factory()->create([
            'role' => 'cashier',
            'is_active' => true,
        ]);

        $category = Category::create([
            'name' => 'Nugget & Sosis',
            'slug' => 'nugget-sosis',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Fiesta Crispy Nugget 500g',
            'barcode' => '8992775210011',
            'cost_price' => 45000,
            'selling_price' => 52000,
            'stock' => 10,
            'unit' => 'pack',
            'is_active' => true,
        ]);

        Livewire::actingAs($cashier)
            ->test(PosTerminal::class)
            ->call('addToCart', $product->id)
            ->assertSet('cart.' . $product->id . ':unit.qty', 1)
            ->assertSet('subtotal', 52000.0)
            ->call('incrementQty', $product->id . ':unit')
            ->assertSet('cart.' . $product->id . ':unit.qty', 2)
            ->assertSet('subtotal', 104000.0)
            ->set('paidAmount', 150000.0)
            ->set('paymentMethod', 'cash')
            ->call('processPayment')
            ->assertSet('showReceiptModal', true)
            ->assertCount('cart', 0);

        // Verify Database
        $this->assertDatabaseHas('transactions', [
            'user_id' => $cashier->id,
            'grand_total' => 104000.0,
            'paid_amount' => 150000.0,
            'change_amount' => 46000.0,
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 8, // 10 - 2 = 8
        ]);

        $this->assertDatabaseHas('stock_mutations', [
            'product_id' => $product->id,
            'type' => 'out',
            'quantity' => -2,
            'stock_before' => 10,
            'stock_after' => 8,
        ]);
    }

    public function test_pos_terminal_multi_step_page_navigation(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier', 'is_active' => true]);
        $category = Category::create(['name' => 'Frozen Food', 'slug' => 'frozen-food']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Kentang Shoestring 1kg',
            'cost_price' => 25000,
            'selling_price' => 35000,
            'stock' => 20,
            'unit' => 'pack',
            'is_active' => true,
        ]);

        $component = Livewire::actingAs($cashier)
            ->test(PosTerminal::class)
            // Initial step is catalog
            ->assertSet('currentStep', 'catalog')
            // Cannot go to payment when cart is empty
            ->call('goToPayment')
            ->assertSet('currentStep', 'catalog')
            ->assertSet('posAlertType', 'warning')
            // Add product to cart
            ->call('addToCart', $product->id)
            ->assertCount('cart', 1)
            // Customer selection is modal
            ->call('goToCustomer')
            ->assertSet('showCustomerModal', true)
            ->assertSet('currentStep', 'catalog')
            // Close customer modal
            ->call('closeCustomerModal')
            ->assertSet('showCustomerModal', false)
            ->assertSet('currentStep', 'catalog')
            // Go to payment step (Page transition)
            ->call('goToPayment')
            ->assertSet('currentStep', 'payment')
            ->assertSet('paidAmount', 35000.0)
            // Complete payment (Page transition to receipt)
            ->call('processPayment')
            ->assertSet('currentStep', 'receipt')
            ->assertSet('lastTransactionId', fn($id) => !is_null($id))
            // Start new transaction (Page transition back to catalog)
            ->call('startNewTransaction')
            ->assertSet('currentStep', 'catalog')
            ->assertCount('cart', 0)
            ->assertSet('lastTransactionId', null);
    }

    public function test_pos_terminal_autofocus_and_barcode_search(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier', 'is_active' => true]);
        $product = Product::create([
            'name' => 'Sosis Sapi Premium',
            'barcode' => '8999888777111',
            'cost_price' => 20000,
            'selling_price' => 30000,
            'stock' => 15,
            'unit' => 'pack',
            'is_active' => true,
        ]);

        Livewire::actingAs($cashier)
            ->test(PosTerminal::class)
            ->assertDispatched('focus-search')
            ->call('addToCart', $product->id)
            ->assertDispatched('focus-search')
            ->assertCount('cart', 1)
            ->set('search', '8999888777111')
            ->call('submitBarcodeOrSearch')
            ->assertDispatched('focus-search')
            ->assertSet('cart.' . $product->id . ':unit.qty', 2)
            ->assertSet('search', '');
    }

    public function test_pos_terminal_can_toggle_price_per_item_and_apply_item_discount(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier', 'is_active' => true]);
        $product = Product::create([
            'name' => 'Nugget Ayam Spesial',
            'barcode' => '8991234567890',
            'cost_price' => 30000,
            'selling_price' => 40000,
            'loyalty_price' => 35000, // Harga Sales
            'stock' => 20,
            'unit' => 'pack',
            'is_active' => true,
        ]);

        $cartKey = $product->id . ':unit';

        Livewire::actingAs($cashier)
            ->test(PosTerminal::class)
            // 1. Add item to cart (defaults to regular price 40,000)
            ->call('addToCart', $product->id)
            ->assertSet('cart.' . $cartKey . '.price', 40000.0)
            ->assertSet('cart.' . $cartKey . '.subtotal', 40000.0)
            ->assertSet('subtotal', 40000.0)
            
            // 2. Toggle item to sales/loyalty price (35,000)
            ->call('toggleItemPriceType', $cartKey)
            ->assertSet('cart.' . $cartKey . '.price_type', 'loyalty')
            ->assertSet('cart.' . $cartKey . '.price', 35000.0)
            ->assertSet('cart.' . $cartKey . '.subtotal', 35000.0)
            ->assertSet('subtotal', 35000.0)
            
            // 3. Open item discount modal and apply 10% discount
            ->call('openItemDiscountModal', $cartKey)
            ->assertSet('showItemDiscountModal', true)
            ->set('itemDiscountType', 'percent')
            ->set('itemDiscountRate', 10)
            ->call('applyItemDiscount')
            ->assertSet('showItemDiscountModal', false)
            ->assertSet('cart.' . $cartKey . '.discount_amount', 3500.0)
            ->assertSet('cart.' . $cartKey . '.subtotal', 31500.0)
            ->assertSet('subtotal', 31500.0)
            
            // 4. Checkout and verify transaction items database
            ->set('paidAmount', 50000.0)
            ->set('paymentMethod', 'cash')
            ->call('processPayment')
            ->assertSet('showReceiptModal', true);

        $this->assertDatabaseHas('transaction_items', [
            'product_id' => $product->id,
            'unit_price' => 35000.0,
            'price_type' => 'loyalty',
            'discount_type' => 'percent',
            'discount_rate' => 10.0,
            'discount_amount' => 3500.0,
            'quantity' => 1,
            'subtotal' => 31500.0,
        ]);
    }

    public function test_pos_terminal_can_increment_decrement_and_remove_with_cart_key(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier', 'is_active' => true]);
        $product = Product::create([
            'name' => 'ILM Bintang',
            'cost_price' => 10000,
            'selling_price' => 14000,
            'stock' => 10,
            'unit' => 'pack',
            'is_active' => true,
        ]);

        $cartKey = "{$product->id}:unit";

        Livewire::actingAs($cashier)
            ->test(PosTerminal::class)
            ->call('addToCart', $product->id)
            ->assertCount('cart', 1)
            ->assertSet("cart.{$cartKey}.qty", 1)
            ->call('goToPayment')
            ->assertSet('currentStep', 'payment')
            // Increment
            ->call('incrementQty', $cartKey)
            ->assertSet("cart.{$cartKey}.qty", 2)
            ->assertSet("cart.{$cartKey}.subtotal", 28000.0)
            ->call('incrementQty', $cartKey)
            ->assertSet("cart.{$cartKey}.qty", 3)
            ->assertSet("cart.{$cartKey}.subtotal", 42000.0)
            // Decrement
            ->call('decrementQty', $cartKey)
            ->assertSet("cart.{$cartKey}.qty", 2)
            ->assertSet("cart.{$cartKey}.subtotal", 28000.0)
            // Remove
            ->call('removeFromCart', $cartKey)
            ->assertCount('cart', 0);
    }

    public function test_pos_terminal_can_quick_add_product(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier', 'is_active' => true]);
        $category = Category::create(['name' => 'Nugget Frozen', 'slug' => 'nugget-frozen']);

        Livewire::actingAs($cashier)
            ->test(PosTerminal::class)
            ->call('openQuickProductModal')
            ->assertSet('showQuickProductModal', true)
            ->set('quickProductName', 'Nugget Ayam Crispy 500g')
            ->set('quickProductCategoryId', $category->id)
            ->set('quickProductUnit', 'pack')
            ->set('quickProductCostPrice', 18000)
            ->set('quickProductSellingPrice', 25000)
            ->set('quickProductStock', 20)
            ->set('quickProductAutoAddToCart', true)
            ->call('saveQuickProduct')
            ->assertSet('showQuickProductModal', false)
            ->assertCount('cart', 1);

        $this->assertDatabaseHas('products', [
            'name' => 'Nugget Ayam Crispy 500g',
            'cost_price' => 18000.0,
            'selling_price' => 25000.0,
            'unit' => 'pack',
        ]);
    }

    public function test_pos_terminal_can_quick_add_stock(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier', 'is_active' => true]);
        $product = Product::create([
            'name' => 'Bakso Sapi Urat',
            'cost_price' => 15000,
            'selling_price' => 22000,
            'stock' => 5,
            'unit' => 'pack',
            'is_active' => true,
        ]);

        Livewire::actingAs($cashier)
            ->test(PosTerminal::class)
            ->call('openQuickStockModal', $product->id)
            ->assertSet('showQuickStockModal', true)
            ->assertSet('quickStockCurrentStock', 5)
            ->set('quickStockQuantity', 15)
            ->set('quickStockNotes', 'Restock dari freezer belakang')
            ->call('saveQuickStock')
            ->assertSet('showQuickStockModal', false);

        $this->assertDatabaseHas('stock_mutations', [
            'product_id' => $product->id,
            'quantity' => 15,
            'stock_before' => 5,
            'stock_after' => 20,
            'description' => 'Restock dari freezer belakang',
        ]);
    }
}

