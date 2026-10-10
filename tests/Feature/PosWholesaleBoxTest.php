<?php

namespace Tests\Feature;

use App\Livewire\Admin\Product\ProductIndex;
use App\Livewire\Pos\PosTerminal;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use App\Services\ProductImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PosWholesaleBoxTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_product_with_wholesale_box_fields(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $category = Category::create(['name' => 'Nugget', 'slug' => 'nugget']);

        Livewire::actingAs($admin)
            ->test(ProductIndex::class)
            ->set('name', 'Fiesta Nugget 500g')
            ->set('category_id', $category->id)
            ->set('unit', 'pack')
            ->set('cost_price', 35000)
            ->set('selling_price', 45000)
            ->set('loyalty_price', 42000)
            ->set('stock', 100)
            ->set('min_stock_alert', 20)
            ->set('pieces_per_box', 24)
            ->set('box_selling_price', 1000000)
            ->set('box_loyalty_price', 980000)
            ->set('box_barcode', '899123456DUS')
            ->call('saveProduct')
            ->assertHasNoErrors();

        $product = Product::where('name', 'Fiesta Nugget 500g')->first();
        $this->assertNotNull($product);
        $this->assertEquals(24, $product->pieces_per_box);
        $this->assertEquals(1000000, (float) $product->box_selling_price);
        $this->assertEquals(980000, (float) $product->box_loyalty_price);
        $this->assertEquals('899123456DUS', $product->box_barcode);
        $this->assertTrue($product->hasBoxOption());
    }

    public function test_pos_terminal_can_add_box_and_unit_and_accurately_deduct_stock(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier', 'is_active' => true]);
        $category = Category::create(['name' => 'Sosis', 'slug' => 'sosis']);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Kanzler Singles 65g',
            'barcode' => '89999990001',
            'cost_price' => 7000,
            'selling_price' => 9000,
            'loyalty_price' => 8500,
            'pieces_per_box' => 24,
            'box_selling_price' => 200000,
            'box_loyalty_price' => 195000,
            'box_barcode' => '89999990001BOX',
            'stock' => 100, // Total 100 pcs in warehouse
            'unit' => 'pcs',
            'is_active' => true,
        ]);

        // Customer buys 2 Dus (48 pcs) and 3 Pcs (3 pcs) = total 51 pcs deducted
        Livewire::actingAs($cashier)
            ->test(PosTerminal::class)
            ->call('addToCart', $product->id, 'box')
            ->assertSet('cart.' . $product->id . ':box.qty', 1)
            ->assertSet('cart.' . $product->id . ':box.price', 200000.0)
            ->call('incrementQty', $product->id . ':box')
            ->assertSet('cart.' . $product->id . ':box.qty', 2)
            ->assertSet('cart.' . $product->id . ':box.subtotal', 400000.0)
            // Also add 3 loose pcs
            ->call('addToCart', $product->id, 'unit')
            ->assertSet('cart.' . $product->id . ':unit.qty', 1)
            ->call('incrementQty', $product->id . ':unit')
            ->call('incrementQty', $product->id . ':unit')
            ->assertSet('cart.' . $product->id . ':unit.qty', 3)
            ->assertSet('cart.' . $product->id . ':unit.subtotal', 27000.0)
            // Grand total = 400.000 + 27.000 = 427.000
            ->assertSet('grandTotal', 427000.0)
            ->set('paidAmount', 450000.0)
            ->set('paymentMethod', 'cash')
            ->call('processPayment')
            ->assertSet('showReceiptModal', true);

        // Verify product stock deduction: 100 - (2*24 + 3) = 100 - 51 = 49 pcs
        $product->refresh();
        $this->assertEquals(49, $product->stock);

        // Verify Transaction Items
        $transaction = Transaction::latest()->first();
        $this->assertEquals(427000.0, (float) $transaction->grand_total);
        $this->assertCount(2, $transaction->items);

        $boxItem = $transaction->items()->where('unit_type', 'box')->first();
        $this->assertNotNull($boxItem);
        $this->assertEquals(2, $boxItem->quantity);
        $this->assertEquals(24, $boxItem->pieces_per_unit);
        $this->assertEquals(400000.0, (float) $boxItem->subtotal);

        $unitItem = $transaction->items()->where('unit_type', 'unit')->first();
        $this->assertNotNull($unitItem);
        $this->assertEquals(3, $unitItem->quantity);
        $this->assertEquals(1, $unitItem->pieces_per_unit);
        $this->assertEquals(27000.0, (float) $unitItem->subtotal);
    }

    public function test_pos_scans_box_barcode_directly(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier', 'is_active' => true]);
        $product = Product::create([
            'name' => 'Dimsum Siap Kukus',
            'barcode' => '111222333',
            'cost_price' => 20000,
            'selling_price' => 30000,
            'pieces_per_box' => 10,
            'box_selling_price' => 280000,
            'box_barcode' => 'DUS-111222333',
            'stock' => 50,
            'unit' => 'pack',
            'is_active' => true,
        ]);

        Livewire::actingAs($cashier)
            ->test(PosTerminal::class)
            ->set('search', 'DUS-111222333')
            ->call('submitBarcodeOrSearch')
            ->assertSet('cart.' . $product->id . ':box.qty', 1)
            ->assertSet('cart.' . $product->id . ':box.price', 280000.0)
            ->assertSet('grandTotal', 280000.0);
    }

    public function test_pos_wholesale_box_loyalty_member_pricing(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier', 'is_active' => true]);
        $customer = Customer::create([
            'name' => 'Mitra Frozen Mart',
            'member_code' => 'MBR001',
            'is_active' => true,
        ]);

        $product = Product::create([
            'name' => 'Cedea Fish Dumpling Cheese',
            'barcode' => '777888999',
            'cost_price' => 22000,
            'selling_price' => 30000,
            'loyalty_price' => 28000,
            'pieces_per_box' => 20,
            'box_selling_price' => 580000,
            'box_loyalty_price' => 550000,
            'stock' => 60,
            'unit' => 'pack',
            'is_active' => true,
        ]);

        Livewire::actingAs($cashier)
            ->test(PosTerminal::class)
            ->call('addToCart', $product->id, 'box')
            ->assertSet('grandTotal', 580000.0) // Regular price
            ->call('selectCustomer', $customer->id)
            ->assertSet('isLoyalty', true)
            ->assertSet('cart.' . $product->id . ':box.price', 550000.0)
            ->assertSet('grandTotal', 550000.0); // Loyalty box price
    }

    public function test_product_import_service_with_box_fields(): void
    {
        $importService = new ProductImportService();

        $csv = "sep=;\n" .
            "barcode;nama_produk;kategori;satuan;harga_beli;harga_jual;harga_jual_member;isi_per_dus;harga_jual_dus;harga_jual_dus_member;barcode_dus;stok_awal;stok_minimum\n" .
            "999888111;Nugget Premium Super;Nugget;pack;30000;40000;38000;20;750000;730000;999888111BOX;100;10\n";

        $tempFile = tempnam(sys_get_temp_dir(), 'import_test_') . '.csv';
        file_put_contents($tempFile, $csv);

        $parsed = $importService->parseAndValidate($tempFile);
        @unlink($tempFile);

        $this->assertTrue($parsed['success']);
        $this->assertCount(1, $parsed['rows']);

        $row = $parsed['rows'][0];
        $this->assertEquals('Nugget Premium Super', $row['name']);
        $this->assertEquals(20, $row['pieces_per_box']);
        $this->assertEquals(750000, $row['box_selling_price']);
        $this->assertEquals(730000, $row['box_loyalty_price']);
        $this->assertEquals('999888111BOX', $row['box_barcode']);

        $result = $importService->executeImport($parsed['rows']);
        $this->assertTrue($result['success']);

        $product = Product::where('barcode', '999888111')->first();
        $this->assertNotNull($product);
        $this->assertEquals(20, $product->pieces_per_box);
        $this->assertEquals(750000, (float) $product->box_selling_price);
        $this->assertEquals('999888111BOX', $product->box_barcode);
    }
}
