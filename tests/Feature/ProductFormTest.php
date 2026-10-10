<?php

namespace Tests\Feature;

use App\Livewire\Admin\Product\ProductForm;
use App\Models\Category;
use App\Models\Outlet;
use App\Models\OutletProduct;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProductFormTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Outlet $mainOutlet;
    protected Outlet $branchOutlet;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->mainOutlet = Outlet::create([
            'name' => 'Toko Pusat Syafa',
            'code' => 'PST-01',
            'is_main' => true,
            'is_active' => true,
        ]);

        $this->branchOutlet = Outlet::create([
            'name' => 'Cabang Kaliwungu',
            'code' => 'KLW-01',
            'is_main' => false,
            'is_active' => true,
        ]);
    }

    public function test_admin_can_access_product_create_page(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.products.create'))
            ->assertStatus(200)
            ->assertSee('Tambah Produk Baru');
    }

    public function test_admin_can_create_product_via_form(): void
    {
        $category = Category::create(['name' => 'Olahan Seafood', 'slug' => 'olahan-seafood']);
        $supplier = Supplier::create(['name' => 'Aza Frozen Food', 'code' => 'SUP-001', 'is_active' => true]);

        Livewire::actingAs($this->admin)
            ->test(ProductForm::class)
            ->set('name', 'Cedea Fish Dumpling Cheese 500g')
            ->set('category_id', $category->id)
            ->set('supplier_id', $supplier->id)
            ->set('cost_price', '28.000')
            ->set('selling_price', '35.000')
            ->set('loyalty_price', '32.000')
            ->set('pieces_per_box', 20)
            ->set('box_selling_price', '680.000')
            ->set('box_loyalty_price', '650.000')
            ->set('stock', 25)
            ->set('min_stock_alert', 5)
            ->set('unit', 'pack')
            ->call('save')
            ->assertRedirect(route('admin.products.index'));

        $this->assertDatabaseHas('products', [
            'name' => 'Cedea Fish Dumpling Cheese 500g',
            'category_id' => $category->id,
            'supplier_id' => $supplier->id,
            'cost_price' => 28000,
            'selling_price' => 35000,
            'loyalty_price' => 32000,
            'pieces_per_box' => 20,
            'box_selling_price' => 680000,
            'box_loyalty_price' => 650000,
            'stock' => 25,
            'unit' => 'pack',
        ]);

        $product = Product::where('name', 'Cedea Fish Dumpling Cheese 500g')->first();
        $this->assertNotNull($product);

        // Check sync to outlets
        $this->assertDatabaseHas('outlet_products', [
            'outlet_id' => $this->mainOutlet->id,
            'product_id' => $product->id,
            'selling_price' => 35000,
            'stock' => 25,
        ]);

        $this->assertDatabaseHas('outlet_products', [
            'outlet_id' => $this->branchOutlet->id,
            'product_id' => $product->id,
            'selling_price' => 35000,
            'stock' => 0,
        ]);
    }

    public function test_admin_can_access_product_edit_page_and_update(): void
    {
        $product = Product::create([
            'name' => 'Fiesta Nugget 500g',
            'slug' => 'fiesta-nugget-500g',
            'barcode' => '8991234567890',
            'cost_price' => 35000,
            'selling_price' => 45000,
            'loyalty_price' => 42000,
            'stock' => 30,
            'min_stock_alert' => 5,
            'unit' => 'pack',
            'is_active' => true,
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.products.edit', $product->id))
            ->assertStatus(200)
            ->assertSee('Fiesta Nugget 500g');

        Livewire::actingAs($this->admin)
            ->test(ProductForm::class, ['id' => $product->id])
            ->assertSet('name', 'Fiesta Nugget 500g')
            ->assertSet('cost_price', 35000)
            ->assertSet('selling_price', 45000)
            ->set('selling_price', '48.000')
            ->set('loyalty_price', '44.000')
            ->set('stock', 35)
            ->call('save')
            ->assertRedirect(route('admin.products.index'));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'selling_price' => 48000,
            'loyalty_price' => 44000,
            'stock' => 35,
        ]);
    }
}
