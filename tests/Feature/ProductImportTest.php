<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMutation;
use App\Models\User;
use App\Services\ProductImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Tests\TestCase;

class ProductImportTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adminUser = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);
    }

    public function test_template_download_generates_valid_csv_with_sep_directive_and_categories(): void
    {
        Category::create(['name' => 'Nugget & Sosis', 'slug' => 'nugget-sosis']);
        Category::create(['name' => 'Dimsum & Siomay', 'slug' => 'dimsum-siomay']);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.print.products.import-template'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        
        $content = $response->getContent();
        
        // Assert UTF-8 BOM and sep=; directive
        $this->assertStringStartsWith("\xEF\xBB\xBF", $content);
        $this->assertStringContainsString("sep=;\n", $content);
        $this->assertStringContainsString('barcode;nama_produk;', $content);
        $this->assertStringContainsString('harga_jual_member;', $content);
        
        // Assert registered categories are listed in the comments/notes
        $this->assertStringContainsString('Nugget & Sosis', $content);
        $this->assertStringContainsString('Dimsum & Siomay', $content);
    }

    public function test_import_service_parses_comma_and_semicolon_delimited_csv(): void
    {
        Category::create(['name' => 'Bakso', 'slug' => 'bakso']);

        $csvContent = "\xEF\xBB\xBF" . "sep=;\n" . "barcode;nama_produk;kategori;satuan;harga_beli_hpp;harga_jual_reguler;harga_jual_member;stok_awal;stok_minimum;keterangan\n" .
            "899123456701;Bakso Urat Super 500g;Bakso;pack;Rp 30.000;Rp 38.000;Rp 36.000;25;5;Bakso sapi isi 25\n" .
            "899123456702;Nugget Ayam Crispy;Olahan Ayam;pack;22000;28000;26500;15;3;Crispy nugget\n";

        $tempFile = tempnam(sys_get_temp_dir(), 'csv_test_');
        file_put_contents($tempFile, $csvContent);

        $service = new ProductImportService();
        $result = $service->parseAndValidate($tempFile);

        @unlink($tempFile);

        $this->assertTrue($result['success']);
        $this->assertEquals(2, $result['stats']['total']);
        $this->assertEquals(2, $result['stats']['valid']);
        $this->assertEquals(0, $result['stats']['invalid']);
        
        $firstRow = $result['rows'][0];
        $this->assertEquals('899123456701', $firstRow['barcode']);
        $this->assertEquals('Bakso Urat Super 500g', $firstRow['name']);
        $this->assertEquals(30000.0, $firstRow['cost_price']);
        $this->assertEquals(38000.0, $firstRow['selling_price']);
        $this->assertEquals(36000.0, $firstRow['loyalty_price']);
        $this->assertEquals(25, $firstRow['stock']);
    }

    public function test_import_service_executes_import_and_creates_records_with_stock_mutations(): void
    {
        $service = new ProductImportService();

        $rows = [
            [
                'row_number' => 2,
                'barcode' => '8999990001',
                'name' => 'Kentang Shoestring 1kg',
                'category_name' => 'Kentang Beku',
                'unit' => 'pack',
                'cost_price' => 32000.0,
                'selling_price' => 40000.0,
                'loyalty_price' => 38000.0,
                'stock' => 50,
                'min_stock_alert' => 5,
                'description' => 'Imported test product',
                'is_valid' => true,
                'is_update' => false,
                'errors' => [],
            ]
        ];

        $result = $service->executeImport($rows, true, $this->adminUser->id);

        $this->assertTrue($result['success']);
        $this->assertEquals(1, $result['created_count']);

        // Check product in DB
        $product = Product::where('barcode', '8999990001')->first();
        $this->assertNotNull($product);
        $this->assertEquals('Kentang Shoestring 1kg', $product->name);
        $this->assertEquals(32000, (int) $product->cost_price);
        $this->assertEquals(40000, (int) $product->selling_price);
        $this->assertEquals(38000, (int) $product->loyalty_price);
        $this->assertEquals(50, $product->stock);

        // Check category was auto-created
        $category = Category::where('name', 'Kentang Beku')->first();
        $this->assertNotNull($category);
        $this->assertEquals($category->id, $product->category_id);

        // Check StockMutation created
        $mutation = StockMutation::where('product_id', $product->id)->first();
        $this->assertNotNull($mutation);
        $this->assertEquals(50, $mutation->quantity);
        $this->assertEquals('in', $mutation->type);
    }

    public function test_import_service_updates_existing_product_when_enabled(): void
    {
        $category = Category::create(['name' => 'Sosis', 'slug' => 'sosis']);
        $existing = Product::create([
            'category_id' => $category->id,
            'name' => 'Sosis Sapi Jumbo',
            'barcode' => '8991112223',
            'cost_price' => 40000,
            'selling_price' => 50000,
            'loyalty_price' => 48000,
            'stock' => 10,
            'min_stock_alert' => 5,
            'unit' => 'pack',
            'is_active' => true,
        ]);

        $service = new ProductImportService();

        $rows = [
            [
                'row_number' => 2,
                'barcode' => '8991112223',
                'name' => 'Sosis Sapi Jumbo (Updated)',
                'category_name' => 'Sosis',
                'unit' => 'pack',
                'cost_price' => 42000.0,
                'selling_price' => 55000.0,
                'loyalty_price' => 52000.0,
                'stock' => 30, // New stock
                'min_stock_alert' => 8,
                'description' => 'Updated via Excel',
                'is_valid' => true,
                'is_update' => true,
                'existing_id' => $existing->id,
                'errors' => [],
            ]
        ];

        $result = $service->executeImport($rows, true, $this->adminUser->id);

        $this->assertTrue($result['success']);
        $this->assertEquals(1, $result['updated_count']);

        $existing->refresh();
        $this->assertEquals('Sosis Sapi Jumbo (Updated)', $existing->name);
        $this->assertEquals(55000, (int) $existing->selling_price);
        $this->assertEquals(52000, (int) $existing->loyalty_price);
        $this->assertEquals(30, $existing->stock);

        // Check adjustment mutation
        $mutation = StockMutation::where('product_id', $existing->id)->latest('id')->first();
        $this->assertNotNull($mutation);
        $this->assertEquals(20, $mutation->quantity); // +20 difference
    }

    public function test_livewire_product_index_import_flow(): void
    {
        $service = new ProductImportService();
        $csvData = $service->generateTemplate();

        $file = UploadedFile::fake()->createWithContent('import_test.csv', $csvData);

        Livewire::actingAs($this->adminUser)
            ->test(\App\Livewire\Admin\Product\ProductIndex::class)
            ->call('openImportModal')
            ->assertSet('showImportModal', true)
            ->set('importFile', $file)
            ->assertCount('previewRows', 5)
            ->call('executeImport')
            ->assertSet('showImportModal', false)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('products', [
            'barcode' => '899123456001',
            'name' => 'Fiesta Chicken Nugget Crispy 500g',
            'loyalty_price' => 42000,
        ]);
    }
}
