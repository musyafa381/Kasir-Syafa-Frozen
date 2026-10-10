<?php

namespace Tests\Feature;

use App\Livewire\Admin\Category\CategoryDetail;
use App\Livewire\Admin\Category\CategoryIndex;
use App\Livewire\Admin\Product\ProductIndex;
use App\Livewire\Admin\Stock\StockAdjustment;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class InventoryManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_crud_category(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        Livewire::actingAs($admin)
            ->test(CategoryIndex::class)
            ->set('name', 'Olahan Daging Sapi')
            ->set('description', 'Daging slice dan patty burger')
            ->call('saveCategory')
            ->assertSet('showModal', false);

        $this->assertDatabaseHas('categories', [
            'name' => 'Olahan Daging Sapi',
            'slug' => 'olahan-daging-sapi',
        ]);
    }

    public function test_admin_can_create_product_without_category(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        Livewire::actingAs($admin)
            ->test(ProductIndex::class)
            ->set('category_id', null)
            ->set('name', 'Es Lilin Durian Lokal')
            ->set('cost_price', 2000)
            ->set('selling_price', 3500)
            ->set('stock', 50)
            ->set('min_stock_alert', 10)
            ->set('unit', 'pcs')
            ->call('saveProduct')
            ->assertSet('showModal', false);

        $this->assertDatabaseHas('products', [
            'name' => 'Es Lilin Durian Lokal',
            'category_id' => null,
            'selling_price' => 3500,
        ]);
    }

    public function test_admin_can_delete_category_and_detach_products(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $category = Category::create(['name' => 'Dimsum', 'slug' => 'dimsum']);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Siomay Ayam Isi 10',
            'cost_price' => 15000,
            'selling_price' => 20000,
            'stock' => 10,
            'unit' => 'pack',
            'is_active' => true,
        ]);

        // Delete with detach mode
        Livewire::actingAs($admin)
            ->test(CategoryIndex::class)
            ->call('confirmDelete', $category->id)
            ->assertSet('deleteMode', 'detach')
            ->call('deleteCategory')
            ->assertSet('showDeleteModal', false);

        // Category deleted
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);

        // Product still exists but category_id is null
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'category_id' => null,
        ]);
    }

    public function test_admin_can_delete_category_along_with_products(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $category = Category::create(['name' => 'Minuman Es', 'slug' => 'minuman-es']);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Es Campur Segar',
            'cost_price' => 5000,
            'selling_price' => 8000,
            'stock' => 15,
            'unit' => 'cup',
            'is_active' => true,
        ]);

        // Delete with delete_products mode
        Livewire::actingAs($admin)
            ->test(CategoryIndex::class)
            ->call('confirmDelete', $category->id)
            ->set('deleteMode', 'delete_products')
            ->call('deleteCategory')
            ->assertSet('showDeleteModal', false);

        // Both category and product are deleted
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_admin_can_view_category_detail_and_detach_product(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $category = Category::create(['name' => 'Seafood Frozen', 'slug' => 'seafood-frozen']);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Udang Kupas 500g',
            'cost_price' => 40000,
            'selling_price' => 55000,
            'stock' => 20,
            'unit' => 'pack',
            'is_active' => true,
        ]);

        Livewire::actingAs($admin)
            ->test(CategoryDetail::class, ['category' => $category])
            ->assertSee('Seafood Frozen')
            ->assertSee('Udang Kupas 500g')
            ->call('detachProduct', $product->id);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'category_id' => null,
        ]);
    }

    public function test_admin_can_adjust_stock_in_and_out(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $category = Category::create(['name' => 'Nugget & Sosis', 'slug' => 'nugget-sosis']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Fiesta Nugget 500g',
            'cost_price' => 40000,
            'selling_price' => 50000,
            'stock' => 10,
            'unit' => 'pack',
            'is_active' => true,
        ]);

        // Stock In (Kulakan +15)
        Livewire::actingAs($admin)
            ->test(StockAdjustment::class)
            ->set('product_id', $product->id)
            ->set('type', 'in')
            ->set('quantity', 15)
            ->set('reference', 'PO-SUPPLIER-01')
            ->call('saveAdjustment')
            ->assertSet('showFormModal', false);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 25, // 10 + 15 = 25
        ]);

        $this->assertDatabaseHas('stock_mutations', [
            'product_id' => $product->id,
            'type' => 'in',
            'quantity' => 15,
            'stock_before' => 10,
            'stock_after' => 25,
        ]);
    }
}
