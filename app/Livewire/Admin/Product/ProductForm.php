<?php

namespace App\Livewire\Admin\Product;

use App\Models\Category;
use App\Models\Outlet;
use App\Models\OutletProduct;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Form Produk - Syafa Frozen Food')]
class ProductForm extends Component
{
    public ?int $productId = null;
    public bool $isEditing = false;

    // Form fields (Satuan Eceran)
    public ?int $category_id = null;
    public ?int $supplier_id = null;
    public string $name = '';
    public string $barcode = '';
    public string $description = '';
    public $cost_price = 0;
    public $selling_price = 0;
    public $loyalty_price = 0;
    public int $stock = 0;
    public int $min_stock_alert = 5;
    public string $unit = 'pack';
    public bool $is_active = true;

    // Form fields (Pengaturan Grosir / Kardus)
    public string $box_barcode = '';
    public ?int $pieces_per_box = null;
    public $box_selling_price = 0;
    public $box_loyalty_price = 0;

    // Multi-Outlet Branch Pricing & Stock
    public array $branchPrices = [];

    protected function rules(): array
    {
        return [
            'category_id' => 'nullable|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'name' => 'required|min:2|max:150',
            'barcode' => 'nullable|max:50|unique:products,barcode,' . $this->productId,
            'box_barcode' => 'nullable|max:50|unique:products,box_barcode,' . $this->productId,
            'description' => 'nullable|max:500',
            'cost_price' => 'required',
            'selling_price' => 'required',
            'loyalty_price' => 'nullable',
            'pieces_per_box' => 'nullable|integer|min:2',
            'box_selling_price' => 'nullable',
            'box_loyalty_price' => 'nullable',
            'stock' => 'required|integer|min:0',
            'min_stock_alert' => 'required|integer|min:0',
            'unit' => 'required|string|max:20',
            'is_active' => 'boolean',
        ];
    }

    public function mount(?int $id = null): void
    {
        $outlets = Outlet::where('is_active', true)->orderBy('is_main', 'desc')->orderBy('name')->get();

        if ($id) {
            $this->isEditing = true;
            $this->productId = $id;
            $product = Product::with('outletProducts')->findOrFail($id);

            $this->category_id = $product->category_id;
            $this->supplier_id = $product->supplier_id;
            $this->name = $product->name;
            $this->barcode = $product->barcode ?? '';
            $this->box_barcode = $product->box_barcode ?? '';
            $this->description = $product->description ?? '';
            $this->cost_price = (int) $product->cost_price;
            $this->selling_price = (int) $product->selling_price;
            $this->loyalty_price = $product->loyalty_price ? (int) $product->loyalty_price : 0;
            $this->pieces_per_box = $product->pieces_per_box;
            $this->box_selling_price = $product->box_selling_price ? (int) $product->box_selling_price : 0;
            $this->box_loyalty_price = $product->box_loyalty_price ? (int) $product->box_loyalty_price : 0;
            $this->stock = $product->stock;
            $this->min_stock_alert = $product->min_stock_alert;
            $this->unit = $product->unit;
            $this->is_active = (bool) $product->is_active;

            $this->branchPrices = [];
            foreach ($outlets as $outlet) {
                $op = $product->outletProducts->firstWhere('outlet_id', $outlet->id);
                $this->branchPrices[$outlet->id] = [
                    'outlet_id' => $outlet->id,
                    'outlet_name' => $outlet->name,
                    'outlet_code' => $outlet->code,
                    'is_main' => (bool) $outlet->is_main,
                    'selling_price' => $op && $op->selling_price !== null ? (int)$op->selling_price : (int)$product->selling_price,
                    'loyalty_price' => $op && $op->loyalty_price !== null ? (int)$op->loyalty_price : ($product->loyalty_price ? (int)$product->loyalty_price : 0),
                    'box_selling_price' => $op && $op->box_selling_price !== null ? (int)$op->box_selling_price : ($product->box_selling_price ? (int)$product->box_selling_price : 0),
                    'cost_price' => $op && $op->cost_price !== null ? (int)$op->cost_price : (int)$product->cost_price,
                    'stock' => $op ? (int)$op->stock : ($outlet->is_main ? (int)$product->stock : 0),
                    'min_stock_alert' => $op && $op->min_stock_alert !== null ? (int)$op->min_stock_alert : (int)$product->min_stock_alert,
                ];
            }
        } else {
            $this->isEditing = false;
            $this->unit = 'pack';
            $this->min_stock_alert = 5;
            $this->is_active = true;
            $this->generateBarcode();

            $this->branchPrices = [];
            foreach ($outlets as $outlet) {
                $this->branchPrices[$outlet->id] = [
                    'outlet_id' => $outlet->id,
                    'outlet_name' => $outlet->name,
                    'outlet_code' => $outlet->code,
                    'is_main' => (bool) $outlet->is_main,
                    'selling_price' => 0,
                    'loyalty_price' => 0,
                    'box_selling_price' => 0,
                    'cost_price' => 0,
                    'stock' => 0,
                    'min_stock_alert' => 5,
                ];
            }
        }
    }

    public function updatedStock($val): void
    {
        $mainOutlet = Outlet::where('is_main', true)->first();
        if ($mainOutlet && isset($this->branchPrices[$mainOutlet->id])) {
            $this->branchPrices[$mainOutlet->id]['stock'] = (int) $val;
        }
    }

    public function generateBarcode(): void
    {
        $this->barcode = '899' . str_pad((string) random_int(1000000000, 9999999999), 10, '0', STR_PAD_LEFT);
    }

    public function generateBoxBarcode(): void
    {
        $this->box_barcode = '898' . str_pad((string) random_int(1000000000, 9999999999), 10, '0', STR_PAD_LEFT);
    }

    private function parseCurrency($val): float
    {
        if (is_int($val) || is_float($val)) {
            return (float) $val;
        }
        $cleaned = preg_replace('/[^0-9]/', '', (string)$val);
        return (float) ($cleaned ?: 0);
    }

    public function save(): void
    {
        $this->validate();

        $numericCost = $this->parseCurrency($this->cost_price);
        $numericSelling = $this->parseCurrency($this->selling_price);
        $numericLoyalty = $this->parseCurrency($this->loyalty_price);
        $numericBoxSelling = $this->parseCurrency($this->box_selling_price);
        $numericBoxLoyalty = $this->parseCurrency($this->box_loyalty_price);

        $slug = Str::slug($this->name);

        $data = [
            'category_id' => $this->category_id ? (int)$this->category_id : null,
            'supplier_id' => $this->supplier_id ? (int)$this->supplier_id : null,
            'name' => $this->name,
            'slug' => $slug,
            'barcode' => $this->barcode ?: null,
            'box_barcode' => $this->box_barcode ?: null,
            'description' => $this->description ?: null,
            'cost_price' => $numericCost,
            'selling_price' => $numericSelling,
            'loyalty_price' => $numericLoyalty > 0 ? $numericLoyalty : null,
            'pieces_per_box' => $this->pieces_per_box ? (int)$this->pieces_per_box : null,
            'box_selling_price' => $numericBoxSelling > 0 ? $numericBoxSelling : null,
            'box_loyalty_price' => $numericBoxLoyalty > 0 ? $numericBoxLoyalty : null,
            'stock' => $this->stock,
            'min_stock_alert' => $this->min_stock_alert,
            'unit' => $this->unit,
            'is_active' => $this->is_active,
        ];

        $outlets = Outlet::where('is_active', true)->get();

        if ($this->productId) {
            $product = Product::findOrFail($this->productId);
            $product->update($data);

            foreach ($outlets as $outlet) {
                $bData = $this->branchPrices[$outlet->id] ?? null;
                $bSelling = $bData ? $this->parseCurrency($bData['selling_price'] ?? 0) : 0;
                $bLoyalty = $bData ? $this->parseCurrency($bData['loyalty_price'] ?? 0) : 0;
                $bBoxSelling = $bData ? $this->parseCurrency($bData['box_selling_price'] ?? 0) : 0;
                $bCost = $bData ? $this->parseCurrency($bData['cost_price'] ?? 0) : 0;

                if ($outlet->is_main) {
                    $bStock = (int) $this->stock;
                    $product->update(['stock' => $bStock]);
                } else {
                    $bStock = ($bData && isset($bData['stock']) && $bData['stock'] !== '') ? (int)$bData['stock'] : 0;
                }

                $bMinStock = ($bData && isset($bData['min_stock_alert']) && $bData['min_stock_alert'] !== '') ? (int)$bData['min_stock_alert'] : $this->min_stock_alert;

                OutletProduct::updateOrCreate(
                    ['outlet_id' => $outlet->id, 'product_id' => $product->id],
                    [
                        'selling_price' => $bSelling > 0 ? $bSelling : $numericSelling,
                        'loyalty_price' => $bLoyalty > 0 ? $bLoyalty : ($numericLoyalty > 0 ? $numericLoyalty : null),
                        'box_selling_price' => $bBoxSelling > 0 ? $bBoxSelling : ($numericBoxSelling > 0 ? $numericBoxSelling : null),
                        'cost_price' => $bCost > 0 ? $bCost : $numericCost,
                        'stock' => $bStock,
                        'min_stock_alert' => $bMinStock,
                    ]
                );
            }

            session()->flash('success', "Produk '{$product->name}' berhasil diperbarui.");
        } else {
            $product = Product::create($data);

            foreach ($outlets as $outlet) {
                $bData = $this->branchPrices[$outlet->id] ?? null;
                $bSelling = $bData ? $this->parseCurrency($bData['selling_price'] ?? 0) : 0;
                $bLoyalty = $bData ? $this->parseCurrency($bData['loyalty_price'] ?? 0) : 0;
                $bBoxSelling = $bData ? $this->parseCurrency($bData['box_selling_price'] ?? 0) : 0;
                $bCost = $bData ? $this->parseCurrency($bData['cost_price'] ?? 0) : 0;

                if ($outlet->is_main) {
                    $bStock = (int) $this->stock;
                    $product->update(['stock' => $bStock]);
                } else {
                    $bStock = ($bData && isset($bData['stock']) && $bData['stock'] !== '') ? (int)$bData['stock'] : 0;
                }

                $bMinStock = ($bData && isset($bData['min_stock_alert']) && $bData['min_stock_alert'] !== '') ? (int)$bData['min_stock_alert'] : $this->min_stock_alert;

                OutletProduct::create([
                    'outlet_id' => $outlet->id,
                    'product_id' => $product->id,
                    'selling_price' => $bSelling > 0 ? $bSelling : $numericSelling,
                    'loyalty_price' => $bLoyalty > 0 ? $bLoyalty : ($numericLoyalty > 0 ? $numericLoyalty : null),
                    'box_selling_price' => $bBoxSelling > 0 ? $bBoxSelling : ($numericBoxSelling > 0 ? $numericBoxSelling : null),
                    'cost_price' => $bCost > 0 ? $bCost : $numericCost,
                    'stock' => $bStock,
                    'min_stock_alert' => $bMinStock,
                ]);
            }

            session()->flash('success', "Produk baru '{$this->name}' berhasil ditambahkan ke katalog & cabang.");
        }

        $this->redirectRoute('admin.products.index', navigate: true);
    }

    public function render()
    {
        $categories = Category::orderBy('name')->get();
        $suppliers = Supplier::active()->orderBy('name')->get();
        $outlets = Outlet::where('is_active', true)->orderBy('is_main', 'desc')->orderBy('name')->get();

        return view('livewire.admin.product.product-form', [
            'categories' => $categories,
            'suppliers' => $suppliers,
            'outlets' => $outlets,
        ]);
    }
}
