<?php

namespace Tests\Feature;

use App\Livewire\Admin\Tax\TaxIndex;
use App\Livewire\Pos\PosTerminal;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Discount;
use App\Models\Product;
use App\Models\Tax;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TaxManagementTest extends TestCase
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
            'selling_price' => 50000,
            'loyalty_price' => 45000,
            'stock' => 50,
            'min_stock' => 5,
            'unit' => 'pack',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_access_taxes_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.taxes.index'));
        $response->assertStatus(200);
        $response->assertSee('Kelola Pajak');
    }

    public function test_cashier_can_access_admin_taxes_page(): void
    {
        $response = $this->actingAs($this->cashier)->get(route('admin.taxes.index'));
        $response->assertStatus(200);
    }

    public function test_admin_can_create_tax(): void
    {
        Livewire::actingAs($this->admin)
            ->test(TaxIndex::class)
            ->call('openCreateModal')
            ->set('name', 'PPN 11%')
            ->set('code', 'PPN11')
            ->set('rate', 11.00)
            ->set('is_active', true)
            ->set('is_default', true)
            ->set('description', 'Pajak pertambahan nilai standar ritel')
            ->call('saveTax')
            ->assertHasNoErrors()
            ->assertSet('showModal', false);

        $this->assertDatabaseHas('taxes', [
            'name' => 'PPN 11%',
            'code' => 'PPN11',
            'rate' => 11.00,
            'is_active' => true,
            'is_default' => true,
        ]);
    }

    public function test_admin_can_edit_tax_and_toggle_status(): void
    {
        $tax = Tax::create([
            'name' => 'PB1 10%',
            'code' => 'PB1',
            'rate' => 10.00,
            'is_active' => true,
            'is_default' => false,
        ]);

        Livewire::actingAs($this->admin)
            ->test(TaxIndex::class)
            ->call('openEditModal', $tax->id)
            ->assertSet('name', 'PB1 10%')
            ->set('name', 'PB1 Resto 10%')
            ->call('saveTax')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('taxes', [
            'id' => $tax->id,
            'name' => 'PB1 Resto 10%',
        ]);

        // Toggle status
        Livewire::actingAs($this->admin)
            ->test(TaxIndex::class)
            ->call('toggleStatus', $tax->id);

        $this->assertEquals(false, $tax->fresh()->is_active);
    }

    public function test_admin_can_set_default_tax(): void
    {
        $tax1 = Tax::create([
            'name' => 'PPN 11%',
            'rate' => 11.00,
            'is_active' => true,
            'is_default' => true,
        ]);

        $tax2 = Tax::create([
            'name' => 'PB1 10%',
            'rate' => 10.00,
            'is_active' => true,
            'is_default' => false,
        ]);

        Livewire::actingAs($this->admin)
            ->test(TaxIndex::class)
            ->call('setDefault', $tax2->id);

        $this->assertEquals(false, $tax1->fresh()->is_default);
        $this->assertEquals(true, $tax2->fresh()->is_default);
    }

    public function test_admin_can_delete_tax(): void
    {
        $tax = Tax::create([
            'name' => 'Pajak Lama',
            'rate' => 5.00,
            'is_active' => false,
            'is_default' => false,
        ]);

        Livewire::actingAs($this->admin)
            ->test(TaxIndex::class)
            ->call('confirmDelete', $tax->id)
            ->assertSet('showDeleteModal', true)
            ->call('deleteTax')
            ->assertSet('showDeleteModal', false);

        $this->assertDatabaseMissing('taxes', [
            'id' => $tax->id,
        ]);
    }

    public function test_tax_calculation_logic(): void
    {
        $tax = Tax::create([
            'name' => 'PPN 11%',
            'rate' => 11.00,
            'is_active' => true,
        ]);

        $this->assertEquals(11000.00, $tax->calculateTax(100000));
        $this->assertEquals(5500.00, $tax->calculateTax(50000));
        $this->assertEquals(0.00, $tax->calculateTax(0));
    }

    public function test_pos_terminal_can_toggle_tax_and_select_active_tax(): void
    {
        $tax = Tax::create([
            'name' => 'PPN 11%',
            'rate' => 11.00,
            'is_active' => true,
            'is_default' => true,
        ]);

        Livewire::actingAs($this->cashier)
            ->test(PosTerminal::class)
            ->call('addToCart', $this->product->id) // 1 x 50000 = 50000
            ->assertSet('subtotal', 50000)
            ->assertSet('grandTotal', 50000) // Default tax is off
            ->call('toggleTax') // Toggle ON
            ->assertSet('enableTax', true)
            ->assertSet('selectedTaxId', $tax->id)
            ->assertSet('taxAmount', 5500.00) // 11% of 50000 = 5500
            ->assertSet('grandTotal', 55500.00)
            ->call('toggleTax') // Toggle OFF
            ->assertSet('enableTax', false)
            ->assertSet('taxAmount', 0)
            ->assertSet('grandTotal', 50000);
    }

    public function test_pos_checkout_records_tax_id_rate_and_amount_in_database(): void
    {
        $tax = Tax::create([
            'name' => 'PPN 11%',
            'code' => 'PPN11',
            'rate' => 11.00,
            'is_active' => true,
            'is_default' => true,
        ]);

        $discount = Discount::create([
            'name' => 'Diskon 10rb',
            'type' => 'fixed',
            'value' => 10000,
            'min_purchase' => 0,
            'is_active' => true,
        ]);

        // Subtotal = 50000, Diskon = 10000 => DPP = 40000.
        // PPN 11% of 40000 = 4400.
        // Grand Total = 40000 + 4400 = 44400.
        Livewire::actingAs($this->cashier)
            ->test(PosTerminal::class)
            ->call('addToCart', $this->product->id)
            ->call('selectDiscount', $discount->id)
            ->call('selectTax', $tax->id)
            ->assertSet('subtotal', 50000)
            ->assertSet('discountAmount', 10000)
            ->assertSet('taxAmount', 4400.00)
            ->assertSet('grandTotal', 44400.00)
            ->set('paymentMethod', 'cash')
            ->set('paidAmount', 50000)
            ->call('processPayment')
            ->assertSet('currentStep', 'receipt');

        $this->assertDatabaseHas('transactions', [
            'discount_id' => $discount->id,
            'tax_id' => $tax->id,
            'tax_rate' => 11.00,
            'subtotal' => 50000,
            'discount_amount' => 10000,
            'tax_amount' => 4400.00,
            'grand_total' => 44400.00,
            'paid_amount' => 50000,
            'change_amount' => 5600,
        ]);
    }

    public function test_pos_checkout_without_tax_records_zero_tax(): void
    {
        Livewire::actingAs($this->cashier)
            ->test(PosTerminal::class)
            ->call('addToCart', $this->product->id)
            ->set('paymentMethod', 'cash')
            ->set('paidAmount', 50000)
            ->call('processPayment')
            ->assertSet('currentStep', 'receipt');

        $this->assertDatabaseHas('transactions', [
            'tax_id' => null,
            'tax_rate' => 0.00,
            'tax_amount' => 0.00,
            'subtotal' => 50000,
            'grand_total' => 50000,
        ]);
    }
}
