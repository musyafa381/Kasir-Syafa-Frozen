<?php

namespace Tests\Feature;

use App\Livewire\Admin\Discount\DiscountIndex;
use App\Livewire\Pos\PosTerminal;
use App\Models\Category;
use App\Models\Discount;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DiscountManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $cashier;
    protected Category $category;
    protected Product $product;

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
            'name' => 'Nugget & Sosis',
            'slug' => 'nugget-sosis',
        ]);

        $this->product = Product::create([
            'category_id' => $this->category->id,
            'name' => 'Nugget Ayam Premium 500g',
            'barcode' => '8991234567890',
            'cost_price' => 30000,
            'selling_price' => 45000,
            'loyalty_price' => 40000,
            'stock' => 50,
            'min_stock' => 5,
            'unit' => 'pack',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_access_discounts_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.discounts.index'));
        $response->assertStatus(200);
        $response->assertSee('Kelola Diskon & Promo');
    }

    public function test_cashier_can_access_admin_discounts_page(): void
    {
        $response = $this->actingAs($this->cashier)->get(route('admin.discounts.index'));
        $response->assertStatus(200);
    }

    public function test_admin_can_create_fixed_discount(): void
    {
        Livewire::actingAs($this->admin)
            ->test(DiscountIndex::class)
            ->call('openCreateModal')
            ->set('name', 'Voucher Hemat 10rb')
            ->set('code', 'HEMAT10')
            ->set('type', 'fixed')
            ->set('value', 10000)
            ->set('min_purchase', 50000)
            ->call('saveDiscount')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('discounts', [
            'name' => 'Voucher Hemat 10rb',
            'code' => 'HEMAT10',
            'type' => 'fixed',
            'value' => 10000,
            'min_purchase' => 50000,
            'is_active' => true,
        ]);
    }

    public function test_admin_can_create_percentage_discount_with_max_cap(): void
    {
        Livewire::actingAs($this->admin)
            ->test(DiscountIndex::class)
            ->call('openCreateModal')
            ->set('name', 'Promo Gajian 10%')
            ->set('code', 'GAJIAN10')
            ->set('type', 'percentage')
            ->set('value', 10)
            ->set('min_purchase', 100000)
            ->set('max_discount_amount', 25000)
            ->call('saveDiscount')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('discounts', [
            'name' => 'Promo Gajian 10%',
            'code' => 'GAJIAN10',
            'type' => 'percentage',
            'value' => 10,
            'min_purchase' => 100000,
            'max_discount_amount' => 25000,
        ]);
    }

    public function test_admin_can_edit_and_toggle_discount_status(): void
    {
        $discount = Discount::create([
            'name' => 'Promo Awal',
            'code' => 'AWAL',
            'type' => 'fixed',
            'value' => 5000,
            'min_purchase' => 0,
            'is_active' => true,
        ]);

        Livewire::actingAs($this->admin)
            ->test(DiscountIndex::class)
            ->call('openEditModal', $discount->id)
            ->set('name', 'Promo Diperbarui')
            ->set('value', 8000)
            ->call('saveDiscount')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('discounts', [
            'id' => $discount->id,
            'name' => 'Promo Diperbarui',
            'value' => 8000,
        ]);

        // Toggle Status
        Livewire::actingAs($this->admin)
            ->test(DiscountIndex::class)
            ->call('toggleStatus', $discount->id);

        $this->assertEquals(false, $discount->fresh()->is_active);
    }

    public function test_admin_can_delete_discount(): void
    {
        $discount = Discount::create([
            'name' => 'Diskon Mau Dihapus',
            'type' => 'fixed',
            'value' => 5000,
            'is_active' => true,
        ]);

        Livewire::actingAs($this->admin)
            ->test(DiscountIndex::class)
            ->call('confirmDelete', $discount->id)
            ->call('deleteDiscount');

        $this->assertDatabaseMissing('discounts', [
            'id' => $discount->id,
        ]);
    }

    public function test_discount_calculation_logic(): void
    {
        // 1. Fixed Discount
        $fixed = Discount::create([
            'name' => 'Potongan 10rb',
            'type' => 'fixed',
            'value' => 10000,
            'min_purchase' => 50000,
            'is_active' => true,
        ]);

        // Belum mencapai min_purchase
        $this->assertEquals(0.0, $fixed->calculateDiscount(40000));
        // Mencapai min_purchase
        $this->assertEquals(10000.0, $fixed->calculateDiscount(60000));
        // Subtotal lebih kecil dari discount tapi min_purchase 0
        $fixedSmall = Discount::create([
            'name' => 'Potongan 20rb',
            'type' => 'fixed',
            'value' => 20000,
            'min_purchase' => 0,
            'is_active' => true,
        ]);
        $this->assertEquals(15000.0, $fixedSmall->calculateDiscount(15000));

        // 2. Percentage Discount without Cap
        $percentNoCap = Discount::create([
            'name' => 'Diskon 10%',
            'type' => 'percentage',
            'value' => 10,
            'min_purchase' => 0,
            'is_active' => true,
        ]);
        $this->assertEquals(10000.0, $percentNoCap->calculateDiscount(100000));

        // 3. Percentage Discount with Cap
        $percentWithCap = Discount::create([
            'name' => 'Diskon 10% Maks 15rb',
            'type' => 'percentage',
            'value' => 10,
            'min_purchase' => 0,
            'max_discount_amount' => 15000,
            'is_active' => true,
        ]);
        $this->assertEquals(15000.0, $percentWithCap->calculateDiscount(200000)); // 10% of 200k = 20k -> capped at 15k
    }

    public function test_pos_terminal_can_select_and_apply_master_discount(): void
    {
        $discount = Discount::create([
            'name' => 'Promo Diskon 10rb',
            'code' => 'HEMAT10',
            'type' => 'fixed',
            'value' => 10000,
            'min_purchase' => 40000,
            'is_active' => true,
        ]);

        Livewire::actingAs($this->cashier)
            ->test(PosTerminal::class)
            ->call('addToCart', $this->product->id) // 1 x 45000 = 45000
            ->assertSet('subtotal', 45000)
            ->call('selectDiscount', $discount->id)
            ->assertSet('selectedDiscountId', $discount->id)
            ->assertSet('grandTotal', 35000) // 45000 - 10000
            ->call('removeDiscount')
            ->assertSet('selectedDiscountId', null)
            ->assertSet('grandTotal', 45000);
    }

    public function test_pos_terminal_recalculates_discount_on_cart_update(): void
    {
        $percentDiscount = Discount::create([
            'name' => 'Diskon 10%',
            'type' => 'percentage',
            'value' => 10,
            'min_purchase' => 0,
            'is_active' => true,
        ]);

        Livewire::actingAs($this->cashier)
            ->test(PosTerminal::class)
            ->call('addToCart', $this->product->id) // 1 x 45000 = 45000
            ->call('selectDiscount', $percentDiscount->id)
            ->assertSet('grandTotal', 40500) // 45000 - 4500
            ->call('incrementQty', $this->product->id) // 2 x 45000 = 90000
            ->assertSet('subtotal', 90000)
            ->assertSet('grandTotal', 81000); // 90000 - 9000
    }

    public function test_pos_checkout_records_discount_id_and_amount_in_database(): void
    {
        $discount = Discount::create([
            'name' => 'Voucher 15rb',
            'code' => 'HEMAT15',
            'type' => 'fixed',
            'value' => 15000,
            'min_purchase' => 0,
            'is_active' => true,
        ]);

        Livewire::actingAs($this->cashier)
            ->test(PosTerminal::class)
            ->call('addToCart', $this->product->id) // 45000
            ->call('selectDiscount', $discount->id)
            ->set('paymentMethod', 'cash')
            ->set('paidAmount', 50000)
            ->call('processPayment')
            ->assertSet('currentStep', 'receipt');

        $this->assertDatabaseHas('transactions', [
            'discount_id' => $discount->id,
            'subtotal' => 45000,
            'discount_amount' => 15000,
            'grand_total' => 30000,
            'paid_amount' => 50000,
            'change_amount' => 20000,
        ]);
    }

    public function test_admin_can_access_discount_poster_page(): void
    {
        $discount = Discount::create([
            'name' => 'Promo Merdeka',
            'code' => 'MERDEKA45',
            'type' => 'percentage',
            'value' => 17,
            'min_purchase' => 50000,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.discounts.poster', $discount->id));
        $response->assertStatus(200);
        $response->assertSee('Studio Poster', false);
        $response->assertSee('Promo Merdeka');
        $response->assertSee('MERDEKA45');
    }
}
