<?php

namespace App\Livewire\Admin\Category;

use App\Models\Category;
use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Detail Kategori & Daftar Produk - Syafa Frozen Food')]
class CategoryDetail extends Component
{
    use WithPagination;

    public Category $category;
    public string $search = '';
    public string $filterStock = ''; // '', 'low', 'out'

    // Detach confirmation modal
    public bool $showDetachModal = false;
    public ?int $detachingProductId = null;
    public string $detachingProductName = '';

    public function mount(Category $category): void
    {
        $this->category = $category;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStock(): void
    {
        $this->resetPage();
    }

    public function confirmDetach(int $productId): void
    {
        $product = Product::where('id', $productId)
            ->where('category_id', $this->category->id)
            ->firstOrFail();

        $this->detachingProductId = $product->id;
        $this->detachingProductName = $product->name;
        $this->showDetachModal = true;
    }

    public function detachProduct(?int $productId = null): void
    {
        $id = $productId ?: $this->detachingProductId;
        if (!$id) {
            return;
        }

        $product = Product::where('id', $id)
            ->where('category_id', $this->category->id)
            ->first();

        if ($product) {
            $product->update(['category_id' => null]);
            session()->flash('success', "Produk '{$product->name}' berhasil dilepaskan dari kategori ini dan kini berstatus 'Tanpa Kategori'.");
        }

        $this->showDetachModal = false;
        $this->reset(['detachingProductId', 'detachingProductName']);
    }

    public function render()
    {
        $productsQuery = Product::query()
            ->where('category_id', $this->category->id)
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('barcode', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterStock === 'low', function ($q) {
                $q->whereColumn('stock', '<=', 'min_stock_alert')->where('stock', '>', 0);
            })
            ->when($this->filterStock === 'out', function ($q) {
                $q->where('stock', '<=', 0);
            });

        // Summary Calculations
        $allCategoryProducts = Product::where('category_id', $this->category->id)->get();
        $totalProductsCount = $allCategoryProducts->count();
        $totalPhysicalStock = $allCategoryProducts->sum('stock');
        
        $totalAssetValue = 0;
        $totalPotentialRevenue = 0;
        foreach ($allCategoryProducts as $p) {
            $totalAssetValue += ($p->stock * $p->cost_price);
            $totalPotentialRevenue += ($p->stock * $p->selling_price);
        }

        $products = $productsQuery->latest()->paginate(10);

        return view('livewire.admin.category.category-detail', [
            'products' => $products,
            'totalProductsCount' => $totalProductsCount,
            'totalPhysicalStock' => $totalPhysicalStock,
            'totalAssetValue' => $totalAssetValue,
            'totalPotentialRevenue' => $totalPotentialRevenue,
        ]);
    }
}
