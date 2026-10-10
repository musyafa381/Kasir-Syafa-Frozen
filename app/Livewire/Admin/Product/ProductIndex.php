<?php

namespace App\Livewire\Admin\Product;

use App\Models\Category;
use App\Models\Outlet;
use App\Models\OutletProduct;
use App\Models\Product;
use App\Models\Supplier;
use App\Services\ProductImportService;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Katalog Produk - Syafa Frozen Food')]
class ProductIndex extends Component
{
    use WithPagination, WithFileUploads;

    // Filters
    public string $search = '';
    public ?int $filterCategory = null;
    public ?int $filterSupplier = null;
    public string $filterStock = ''; // '', 'low', 'out'

    // Modal Create / Edit
    public bool $showModal = false;
    public ?int $editingProductId = null;

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

    // Branch Pricing Modal
    public bool $showBranchPricingModal = false;
    public ?int $branchProductId = null;
    public string $branchProductName = '';
    public array $branchPrices = [];

    // Delete Modal
    public bool $showDeleteModal = false;
    public ?int $deletingProductId = null;
    public string $deletingProductName = '';
    public bool $deletingProductHasHistory = false;
    public int $deletingProductHistoryCount = 0;

    // Import Modal & States
    public bool $showImportModal = false;
    public $importFile = null;
    public array $previewRows = [];
    public array $importStats = ['total' => 0, 'valid' => 0, 'update' => 0, 'invalid' => 0];
    public bool $updateExisting = true;
    public string $importErrorMessage = '';

    protected function rules(): array
    {
        return [
            'category_id' => 'nullable|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'name' => 'required|min:2|max:150',
            'barcode' => 'nullable|max:50|unique:products,barcode,' . $this->editingProductId,
            'box_barcode' => 'nullable|max:50|unique:products,box_barcode,' . $this->editingProductId,
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

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterCategory(): void
    {
        $this->resetPage();
    }

    public function updatingFilterSupplier(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStock(): void
    {
        $this->resetPage();
    }

    public function generateBarcode(): void
    {
        $this->barcode = '899' . str_pad((string) random_int(1000000000, 9999999999), 10, '0', STR_PAD_LEFT);
    }

    public function generateBoxBarcode(): void
    {
        $this->box_barcode = '898' . str_pad((string) random_int(1000000000, 9999999999), 10, '0', STR_PAD_LEFT);
    }

    public function openCreateModal(): void
    {
        $this->reset([
            'editingProductId',
            'category_id',
            'supplier_id',
            'name',
            'barcode',
            'box_barcode',
            'description',
            'cost_price',
            'selling_price',
            'loyalty_price',
            'pieces_per_box',
            'box_selling_price',
            'box_loyalty_price',
            'stock',
            'min_stock_alert',
            'unit',
            'is_active',
        ]);
        $this->category_id = null;
        $this->supplier_id = null;
        $this->unit = 'pack';
        $this->pieces_per_box = null;
        $this->min_stock_alert = 5;
        $this->is_active = true;
        $this->generateBarcode();
        $this->resetValidation();
        $this->showModal = true;
    }

    public function openEditModal(int $id): void
    {
        $this->resetValidation();
        $product = Product::findOrFail($id);
        $this->editingProductId = $product->id;
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
        $this->is_active = $product->is_active;
        $this->showModal = true;
    }

    public function openBranchPricingModal(int $productId): void
    {
        $product = Product::with('outletProducts')->findOrFail($productId);
        $this->branchProductId = $product->id;
        $this->branchProductName = $product->name;
        
        $outlets = Outlet::where('is_active', true)->orderBy('is_main', 'desc')->orderBy('name')->get();
        
        $this->branchPrices = [];
        foreach ($outlets as $outlet) {
            $op = $product->outletProducts->firstWhere('outlet_id', $outlet->id);
            $this->branchPrices[$outlet->id] = [
                'outlet_name' => $outlet->name,
                'outlet_code' => $outlet->code,
                'is_main' => (bool) $outlet->is_main,
                'selling_price' => $op && $op->selling_price !== null ? (int)$op->selling_price : (int)$product->selling_price,
                'loyalty_price' => $op && $op->loyalty_price !== null ? (int)$op->loyalty_price : ($product->loyalty_price ? (int)$product->loyalty_price : 0),
                'box_selling_price' => $op && $op->box_selling_price !== null ? (int)$op->box_selling_price : ($product->box_selling_price ? (int)$product->box_selling_price : 0),
                'cost_price' => $op && $op->cost_price !== null ? (int)$op->cost_price : (int)$product->cost_price,
                'stock' => $outlet->is_main ? (int)$product->stock : ($op ? (int)$op->stock : 0),
                'min_stock_alert' => $op && $op->min_stock_alert !== null ? (int)$op->min_stock_alert : (int)$product->min_stock_alert,
                'is_custom' => $op ? true : false,
            ];
        }

        $this->showBranchPricingModal = true;
    }

    public function saveBranchPricing(): void
    {
        if (!$this->branchProductId) {
            return;
        }

        $product = Product::findOrFail($this->branchProductId);

        foreach ($this->branchPrices as $outletId => $bData) {
            $sPrice = $this->parseCurrency($bData['selling_price'] ?? 0);
            $lPrice = $this->parseCurrency($bData['loyalty_price'] ?? 0);
            $boxPrice = $this->parseCurrency($bData['box_selling_price'] ?? 0);
            $cPrice = $this->parseCurrency($bData['cost_price'] ?? 0);
            $stock = (int) ($bData['stock'] ?? 0);
            $minStock = (int) ($bData['min_stock_alert'] ?? $product->min_stock_alert);

            OutletProduct::updateOrCreate(
                [
                    'outlet_id' => $outletId,
                    'product_id' => $product->id,
                ],
                [
                    'selling_price' => $sPrice > 0 ? $sPrice : $product->selling_price,
                    'loyalty_price' => $lPrice > 0 ? $lPrice : null,
                    'box_selling_price' => $boxPrice > 0 ? $boxPrice : null,
                    'cost_price' => $cPrice > 0 ? $cPrice : $product->cost_price,
                    'stock' => $stock,
                    'min_stock_alert' => $minStock,
                ]
            );

            // If this is the main outlet, update master stock for backward compatibility
            $outlet = Outlet::find($outletId);
            if ($outlet && $outlet->is_main) {
                $product->update(['stock' => $stock]);
            }
        }

        $msg = "Pengaturan harga & stok per cabang untuk '{$product->name}' berhasil disimpan.";
        session()->flash('success', $msg);
        $this->dispatch('toast', ['message' => $msg, 'type' => 'success']);
        $this->showBranchPricingModal = false;
        $this->reset(['branchProductId', 'branchProductName', 'branchPrices']);
    }

    private function parseCurrency($val): float
    {
        if (is_int($val) || is_float($val)) {
            return (float) $val;
        }
        $cleaned = preg_replace('/[^0-9]/', '', (string)$val);
        return (float) ($cleaned ?: 0);
    }

    public function saveProduct(): void
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

        if ($this->editingProductId) {
            $product = Product::findOrFail($this->editingProductId);
            $product->update($data);
            
            // Sync main outlet product
            $mainOutlet = Outlet::where('is_main', true)->first();
            if ($mainOutlet) {
                OutletProduct::updateOrCreate(
                    ['outlet_id' => $mainOutlet->id, 'product_id' => $product->id],
                    [
                        'selling_price' => $numericSelling,
                        'loyalty_price' => $numericLoyalty > 0 ? $numericLoyalty : null,
                        'box_selling_price' => $numericBoxSelling > 0 ? $numericBoxSelling : null,
                        'cost_price' => $numericCost,
                        'stock' => $this->stock,
                        'min_stock_alert' => $this->min_stock_alert,
                    ]
                );
            }

            $msg = "Produk '{$product->name}' berhasil diperbarui.";
            session()->flash('success', $msg);
            $this->dispatch('toast', ['message' => $msg, 'type' => 'success']);
        } else {
            $product = Product::create($data);

            // Auto-assign to all active outlets
            $outlets = Outlet::where('is_active', true)->get();
            foreach ($outlets as $outlet) {
                OutletProduct::create([
                    'outlet_id' => $outlet->id,
                    'product_id' => $product->id,
                    'selling_price' => $numericSelling,
                    'loyalty_price' => $numericLoyalty > 0 ? $numericLoyalty : null,
                    'box_selling_price' => $numericBoxSelling > 0 ? $numericBoxSelling : null,
                    'cost_price' => $numericCost,
                    'stock' => $outlet->is_main ? $this->stock : 0,
                    'min_stock_alert' => $this->min_stock_alert,
                ]);
            }

            $msg = "Produk baru '{$this->name}' berhasil ditambahkan ke katalog & cabang.";
            session()->flash('success', $msg);
            $this->dispatch('toast', ['message' => $msg, 'type' => 'success']);
        }

        $this->showModal = false;
        $this->reset([
            'editingProductId',
            'name',
            'barcode',
            'box_barcode',
            'description',
            'cost_price',
            'selling_price',
            'loyalty_price',
            'pieces_per_box',
            'box_selling_price',
            'box_loyalty_price',
            'stock',
            'supplier_id',
        ]);
    }

    public function toggleStatus(int $id): void
    {
        $product = Product::findOrFail($id);
        $product->is_active = !$product->is_active;
        $product->save();
        $msg = "Status produk '{$product->name}' diperbarui.";
        session()->flash('success', $msg);
        $this->dispatch('toast', ['message' => $msg, 'type' => 'success']);
    }

    public function confirmDelete(int $id): void
    {
        $product = Product::findOrFail($id);
        $this->deletingProductId = $product->id;
        $this->deletingProductName = $product->name;

        $purchaseCount = $product->purchaseItems()->count();
        $txCount = $product->transactionItems()->count();
        $restockCount = \App\Models\RestockPlanItem::where('product_id', $product->id)->count();

        $this->deletingProductHistoryCount = $purchaseCount + $txCount + $restockCount;
        $this->deletingProductHasHistory = $this->deletingProductHistoryCount > 0;

        $this->showDeleteModal = true;
    }

    public function deactivateProduct(): void
    {
        if (!$this->deletingProductId) {
            return;
        }

        $product = Product::findOrFail($this->deletingProductId);
        $product->is_active = false;
        $product->save();

        $msg = "Produk '{$product->name}' berhasil dinonaktifkan (disembunyikan dari POS/Kasir).";
        session()->flash('success', $msg);
        $this->dispatch('toast', ['message' => $msg, 'type' => 'success']);
        $this->showDeleteModal = false;
        $this->reset(['deletingProductId', 'deletingProductName', 'deletingProductHasHistory', 'deletingProductHistoryCount']);
    }

    public function deleteProduct(): void
    {
        if (!$this->deletingProductId) {
            return;
        }

        $product = Product::findOrFail($this->deletingProductId);

        $purchaseCount = $product->purchaseItems()->count();
        $txCount = $product->transactionItems()->count();
        $restockCount = \App\Models\RestockPlanItem::where('product_id', $product->id)->count();

        if ($purchaseCount > 0 || $txCount > 0 || $restockCount > 0) {
            $msg = "Produk '{$product->name}' tidak dapat dihapus permanen karena tercatat dalam riwayat pembelian/transaksi. Silakan nonaktifkan produk.";
            session()->flash('error', $msg);
            $this->dispatch('toast', ['message' => $msg, 'type' => 'error']);
            $this->showDeleteModal = false;
            $this->reset(['deletingProductId', 'deletingProductName', 'deletingProductHasHistory', 'deletingProductHistoryCount']);
            return;
        }

        try {
            $product->delete();
            $msg = "Produk '{$this->deletingProductName}' berhasil dihapus.";
            session()->flash('success', $msg);
            $this->dispatch('toast', ['message' => $msg, 'type' => 'success']);
        } catch (\Exception $e) {
            $msg = "Gagal menghapus produk: data masih terikat dalam database.";
            session()->flash('error', $msg);
            $this->dispatch('toast', ['message' => $msg, 'type' => 'error']);
        }

        $this->showDeleteModal = false;
        $this->reset(['deletingProductId', 'deletingProductName', 'deletingProductHasHistory', 'deletingProductHistoryCount']);
    }

    // =========================================================================
    // IMPORT PRODUCT METHODS
    // =========================================================================
    public function openImportModal(): void
    {
        $this->reset(['importFile', 'previewRows', 'importErrorMessage']);
        $this->importStats = ['total' => 0, 'valid' => 0, 'update' => 0, 'invalid' => 0];
        $this->updateExisting = true;
        $this->showImportModal = true;
    }

    public function closeImportModal(): void
    {
        $this->reset(['importFile', 'previewRows', 'importErrorMessage']);
        $this->importStats = ['total' => 0, 'valid' => 0, 'update' => 0, 'invalid' => 0];
        $this->showImportModal = false;
    }

    public function updatedImportFile(): void
    {
        $this->validate([
            'importFile' => 'required|file|max:10240',
        ], [
            'importFile.required' => 'Silakan pilih file terlebih dahulu.',
            'importFile.max' => 'Ukuran file maksimal 10MB.',
        ]);

        $importService = app(ProductImportService::class);
        $result = $importService->parseAndValidate($this->importFile->getRealPath());

        if (!$result['success']) {
            $this->importErrorMessage = $result['message'];
            $this->previewRows = [];
            $this->importStats = ['total' => 0, 'valid' => 0, 'update' => 0, 'invalid' => 0];
            return;
        }

        $this->importErrorMessage = '';
        $this->previewRows = $result['rows'];
        $this->importStats = $result['stats'];
    }

    public function executeImport(): void
    {
        if (empty($this->previewRows)) {
            $this->importErrorMessage = 'Tidak ada baris data untuk diimpor. Silakan upload file terlebih dahulu.';
            return;
        }

        $importService = app(ProductImportService::class);
        $result = $importService->executeImport($this->previewRows, $this->updateExisting, auth()->id());

        if ($result['success']) {
            session()->flash('success', $result['message']);
            $this->dispatch('toast', ['message' => $result['message'], 'type' => 'success']);
            $this->closeImportModal();
            $this->resetPage();
        } else {
            $this->importErrorMessage = $result['message'];
        }
    }

    public function render()
    {
        $categories = Category::orderBy('name')->get();
        $suppliers = Supplier::active()->orderBy('name')->get();

        $products = Product::query()
            ->with(['category', 'supplier'])
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('barcode', 'like', '%' . $this->search . '%')
                        ->orWhere('box_barcode', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterCategory, function ($q) {
                $q->where('category_id', $this->filterCategory);
            })
            ->when($this->filterSupplier, function ($q) {
                $q->where('supplier_id', $this->filterSupplier);
            })
            ->when($this->filterStock === 'low', function ($q) {
                $q->whereColumn('stock', '<=', 'min_stock_alert')->where('stock', '>', 0);
            })
            ->when($this->filterStock === 'out', function ($q) {
                $q->where('stock', '<=', 0);
            })
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.admin.product.product-index', [
            'products' => $products,
            'categories' => $categories,
            'suppliers' => $suppliers,
        ]);
    }
}
