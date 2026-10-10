<?php

namespace Tests\Feature;

use App\Livewire\Admin\Purchase\PurchaseCreate;
use App\Livewire\Admin\Purchase\PurchaseIndex;
use App\Livewire\Admin\Supplier\SupplierIndex;
use App\Models\Category;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\StockMutation;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SupplierAndPurchaseManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_crud_supplier(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        // 1. Create Supplier
        Livewire::actingAs($admin)
            ->test(SupplierIndex::class)
            ->call('openCreateModal')
            ->set('name', 'PT Sumber Frozen Food')
            ->set('code', 'SUP-001')
            ->set('contact_person', 'Pak Rudi Sales')
            ->set('phone', '081234567890')
            ->set('payment_term', 'tempo_14')
            ->call('saveSupplier')
            ->assertHasNoErrors()
            ->assertSet('showFormModal', false);

        $this->assertDatabaseHas('suppliers', [
            'name' => 'PT Sumber Frozen Food',
            'code' => 'SUP-001',
            'contact_person' => 'Pak Rudi Sales',
            'payment_term' => 'tempo_14',
        ]);

        $supplier = Supplier::where('code', 'SUP-001')->first();

        // 2. Edit Supplier
        Livewire::actingAs($admin)
            ->test(SupplierIndex::class)
            ->call('openEditModal', $supplier->id)
            ->set('name', 'PT Sumber Frozen Food Utama')
            ->call('saveSupplier')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('suppliers', [
            'id' => $supplier->id,
            'name' => 'PT Sumber Frozen Food Utama',
        ]);

        // 3. Detail Supplier
        Livewire::actingAs($admin)
            ->test(SupplierIndex::class)
            ->call('openDetail', $supplier->id)
            ->assertSet('showDetailModal', true)
            ->assertSee('PT Sumber Frozen Food Utama')
            ->assertSee('Pak Rudi Sales');

        // 4. Delete Supplier (without purchases)
        Livewire::actingAs($admin)
            ->test(SupplierIndex::class)
            ->call('confirmDelete', $supplier->id)
            ->call('deleteSupplier');

        $this->assertDatabaseMissing('suppliers', ['id' => $supplier->id]);
    }

    public function test_admin_can_access_purchase_create_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $response = $this->actingAs($admin)->get(route('admin.purchases.create'));
        $response->assertStatus(200);
        $response->assertSeeLivewire(PurchaseCreate::class);
    }

    public function test_admin_can_create_inbound_purchase_and_update_stock_and_cost(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $supplier = Supplier::create([
            'name' => 'CV Fiesta Jaya',
            'code' => 'SUP-FIESTA',
            'phone' => '08987654321',
            'payment_term' => 'cash',
        ]);

        $product = Product::create([
            'name' => 'Fiesta Nugget 500g',
            'slug' => 'fiesta-nugget-500g',
            'cost_price' => 35000,
            'selling_price' => 45000,
            'stock' => 10,
            'min_stock_alert' => 5,
            'unit' => 'pack',
            'is_active' => true,
        ]);

        // Create Purchase via standalone PurchaseCreate component
        Livewire::actingAs($admin)
            ->test(PurchaseCreate::class)
            ->set('invoice_number', 'PO-20260917-0001')
            ->set('supplier_invoice_number', 'INV-SUP-889')
            ->set('supplier_id', $supplier->id)
            ->set('purchase_date', '2026-09-17')
            ->set('payment_type', 'cash')
            ->set('paid_amount', 720000)
            ->set('items', [
                [
                    'product_id' => $product->id,
                    'quantity' => 20,
                    'unit_cost' => 36000,
                    'update_cost_price' => true,
                ]
            ])
            ->call('savePurchase')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.purchases.index'));

        // Verify Database
        $this->assertDatabaseHas('purchases', [
            'invoice_number' => 'PO-20260917-0001',
            'supplier_id' => $supplier->id,
            'total_amount' => 720000,
            'paid_amount' => 720000,
            'payment_status' => 'paid',
        ]);

        // Verify Stock Increased (10 + 20 = 30)
        $product->refresh();
        $this->assertEquals(30, $product->stock);
        // Verify Cost Price Updated (35.000 -> 36.000)
        $this->assertEquals(36000, (float) $product->cost_price);
        // Verify Supplier linked
        $this->assertEquals($supplier->id, $product->supplier_id);

        // Verify Stock Mutation Recorded
        $this->assertDatabaseHas('stock_mutations', [
            'product_id' => $product->id,
            'supplier_id' => $supplier->id,
            'type' => 'in',
            'quantity' => 20,
            'stock_before' => 10,
            'stock_after' => 30,
        ]);
    }

    public function test_admin_can_handle_tempo_purchases_and_record_payments(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $supplier = Supplier::create([
            'name' => 'Distributor Champ',
            'code' => 'SUP-CHAMP',
            'payment_term' => 'tempo_14',
        ]);

        $product = Product::create([
            'name' => 'Champ Sosis 375g',
            'slug' => 'champ-sosis-375g',
            'cost_price' => 18000,
            'selling_price' => 24000,
            'stock' => 5,
            'min_stock_alert' => 3,
            'unit' => 'pack',
            'is_active' => true,
        ]);

        // Restock 10 packs @ Rp 18.000 = Rp 180.000 with Tempo (DP Rp 50.000)
        Livewire::actingAs($admin)
            ->test(PurchaseCreate::class)
            ->set('invoice_number', 'PO-20260917-0002')
            ->set('supplier_id', $supplier->id)
            ->set('purchase_date', '2026-09-17')
            ->set('payment_type', 'tempo')
            ->set('due_date', '2026-10-01')
            ->set('paid_amount', 50000)
            ->set('items', [
                [
                    'product_id' => $product->id,
                    'quantity' => 10,
                    'unit_cost' => 18000,
                    'update_cost_price' => false,
                ]
            ])
            ->call('savePurchase')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.purchases.index'));

        $purchase = Purchase::where('invoice_number', 'PO-20260917-0002')->first();
        $this->assertEquals('partial', $purchase->payment_status);
        $this->assertEquals(130000, $purchase->remaining_amount);

        // Record installment of remaining Rp 130.000 in PurchaseIndex
        Livewire::actingAs($admin)
            ->test(PurchaseIndex::class)
            ->call('openDetail', $purchase->id)
            ->set('additionalPayment', 130000)
            ->call('recordPayment')
            ->assertHasNoErrors();

        $purchase->refresh();
        $this->assertEquals('paid', $purchase->payment_status);
        $this->assertEquals(180000, (float) $purchase->paid_amount);
        $this->assertEquals(0, $purchase->remaining_amount);
    }

    public function test_admin_can_cancel_purchase_and_rollback_stock(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $supplier = Supplier::create(['name' => 'Agen Frozen', 'payment_term' => 'cash']);

        $product = Product::create([
            'name' => 'Belfoods Bakso 500g',
            'slug' => 'belfoods-bakso-500g',
            'cost_price' => 20000,
            'selling_price' => 28000,
            'stock' => 5,
            'min_stock_alert' => 2,
            'unit' => 'pack',
            'is_active' => true,
        ]);

        // 1. Create Purchase of 15 packs (Stock becomes 5 + 15 = 20)
        Livewire::actingAs($admin)
            ->test(PurchaseCreate::class)
            ->set('invoice_number', 'PO-20260917-0003')
            ->set('supplier_id', $supplier->id)
            ->set('purchase_date', '2026-09-17')
            ->set('payment_type', 'cash')
            ->set('paid_amount', 300000)
            ->set('items', [
                [
                    'product_id' => $product->id,
                    'quantity' => 15,
                    'unit_cost' => 20000,
                    'update_cost_price' => false,
                ]
            ])
            ->call('savePurchase');

        $product->refresh();
        $this->assertEquals(20, $product->stock);

        $purchase = Purchase::where('invoice_number', 'PO-20260917-0003')->first();

        // 2. Cancel Purchase (Stock rolls back from 20 -> 5)
        Livewire::actingAs($admin)
            ->test(PurchaseIndex::class)
            ->call('confirmDelete', $purchase->id)
            ->call('deletePurchase');

        $this->assertDatabaseMissing('purchases', ['id' => $purchase->id]);

        $product->refresh();
        $this->assertEquals(5, $product->stock);

        // Verify rollback stock mutation exists
        $this->assertDatabaseHas('stock_mutations', [
            'product_id' => $product->id,
            'type' => 'out',
            'quantity' => 15,
            'stock_before' => 20,
            'stock_after' => 5,
        ]);
    }

    public function test_admin_can_add_and_manage_items_in_purchase_cart(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $supplier = Supplier::create([
            'name' => 'Distributor So Good',
            'code' => 'SUP-SOGOOD',
            'payment_term' => 'cash',
        ]);

        $product1 = Product::create([
            'name' => 'So Good Nugget 400g',
            'slug' => 'so-good-nugget-400g',
            'cost_price' => 30000,
            'selling_price' => 40000,
            'stock' => 10,
            'min_stock_alert' => 5,
            'unit' => 'pack',
            'is_active' => true,
        ]);

        $product2 = Product::create([
            'name' => 'So Good Sayap Pedas 500g',
            'slug' => 'so-good-sayap-pedas-500g',
            'cost_price' => 45000,
            'selling_price' => 55000,
            'stock' => 5,
            'min_stock_alert' => 3,
            'unit' => 'pack',
            'is_active' => true,
        ]);

        Livewire::actingAs($admin)
            ->test(PurchaseCreate::class)
            ->set('supplier_id', $supplier->id)
            // 1. Add Product 1 to Cart
            ->set('selectedProductId', $product1->id)
            ->assertSet('inputUnitCost', 30000)
            ->set('inputQuantity', 4)
            ->set('inputUnitCost', 31000)
            ->call('addToCart')
            ->assertHasNoErrors()
            ->assertCount('items', 1)
            ->assertSet('items.0.quantity', 4)
            ->assertSet('items.0.unit_cost', 31000)
            // 2. Add Product 2 to Cart
            ->set('selectedProductId', $product2->id)
            ->assertSet('inputUnitCost', 45000)
            ->set('inputQuantity', 2)
            ->call('addToCart')
            ->assertCount('items', 2)
            // 3. Test increment / decrement
            ->call('incrementQuantity', 1)
            ->assertSet('items.1.quantity', 3)
            ->call('decrementQuantity', 1)
            ->assertSet('items.1.quantity', 2)
            // 4. Test remove item from cart
            ->call('removeFromCart', 0)
            ->assertCount('items', 1)
            ->assertSet('items.0.product_id', $product2->id);
    }

    public function test_admin_can_purchase_using_box_wholesale_mode(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $supplier = Supplier::create([
            'name' => 'Distributor Fiesta Prima',
            'code' => 'SUP-PRIMA',
            'payment_term' => 'cash',
        ]);

        $product = Product::create([
            'name' => 'Fiesta Spicy Wing 500g',
            'slug' => 'fiesta-spicy-wing-500g',
            'cost_price' => 40000,
            'selling_price' => 52000,
            'stock' => 5,
            'min_stock_alert' => 5,
            'unit' => 'pack',
            'is_active' => true,
        ]);

        // Purchase 2 Dus, where 1 Dus = 24 Pack, 1 Dus Price = Rp 720.000 (meaning Rp 30.000 / pack)
        Livewire::actingAs($admin)
            ->test(PurchaseCreate::class)
            ->set('supplier_id', $supplier->id)
            ->set('purchaseMode', 'box')
            ->set('selectedProductId', $product->id)
            ->set('boxCount', 2)
            ->set('boxPieces', 24)
            ->set('boxPrice', 720000)
            ->assertSet('inputQuantity', 48) // 2 * 24 = 48
            ->assertSet('inputUnitCost', 30000.0) // 720000 / 24 = 30000
            ->call('addToCart')
            ->assertHasNoErrors()
            ->assertCount('items', 1)
            ->assertSet('items.0.quantity', 48)
            ->assertSet('items.0.unit_cost', 30000.0)
            ->assertSet('items.0.subtotal', 1440000.0)
            ->call('savePurchase')
            ->assertHasNoErrors();

        // Product stock must now be 5 + 48 = 53 pack, and cost_price updated to 30000
        $product->refresh();
        $this->assertEquals(53, $product->stock);
        $this->assertEquals(30000, (int) $product->cost_price);

        // Check purchase in DB
        $this->assertDatabaseHas('purchases', [
            'supplier_id' => $supplier->id,
            'total_amount' => 1440000,
        ]);

        // Check StockMutation created for +48
        $mutation = StockMutation::where('product_id', $product->id)->latest('id')->first();
        $this->assertNotNull($mutation);
        $this->assertEquals(48, $mutation->quantity);
        $this->assertEquals('in', $mutation->type);
        $this->assertEquals(5, $mutation->stock_before);
        $this->assertEquals(53, $mutation->stock_after);
    }
}

