<?php

namespace App\Livewire\Admin\Report;

use App\Models\Category;
use App\Models\Outlet;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Laporan Valuasi Aset Stok - Syafa Frozen Food')]
class InventoryValuationReport extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true)]
    public ?int $outletId = null;

    #[Url(history: true)]
    public string $categoryId = '';

    #[Url(history: true)]
    public string $stockStatus = 'all'; // all, safe, low, out

    #[Url(history: true)]
    public string $sortBy = 'asset_value_desc'; // asset_value_desc, stock_desc, name_asc, profit_desc

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedOutletId(): void
    {
        $this->resetPage();
    }

    public function updatedCategoryId(): void
    {
        $this->resetPage();
    }

    public function updatedStockStatus(): void
    {
        $this->resetPage();
    }

    public function updatedSortBy(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $categories = Category::withCount('products')->orderBy('name')->get();
        $outlets = Outlet::where('is_active', true)->orderByDesc('is_main')->orderBy('name')->get();

        // 1. Calculate Grand Metrics (All active products with multi-branch awareness)
        $allProducts = Product::active()->with('outletProducts')->get();

        $totalAssetCostValue = 0;       // Total Uang Modal Tersimpan (Stock * HPP)
        $totalPotentialRevenue = 0;     // Potensi Omzet (Stock * Selling Price)
        $totalStockUnits = 0;           // Total fisik unit beku
        $totalProductsCount = $allProducts->count();
        $totalLowStockCount = 0;
        $totalOutOfStockCount = 0;

        foreach ($allProducts as $p) {
            if ($this->outletId) {
                $op = $p->getOutletProduct($this->outletId);
                $stock = max(0, (int) ($op?->stock ?? 0));
                $cost = max(0, (float) ($op?->cost_price ?: $p->cost_price));
                $sell = max(0, (float) ($op?->selling_price ?: $p->selling_price));
                $minAlert = (int) ($op?->min_stock_alert ?: $p->min_stock_alert);
            } else {
                $hasOps = $p->outletProducts->isNotEmpty();
                $stock = $hasOps ? max(0, (int) $p->outletProducts->sum('stock')) : max(0, (int) $p->stock);
                
                // Effective cost price (fallback ke master jika kosong)
                $firstOpCost = $p->outletProducts->firstWhere('cost_price', '>', 0)?->cost_price;
                $cost = max(0, (float) ($p->cost_price ?: ($firstOpCost ?? 0)));
                
                // Effective selling price (fallback ke master jika kosong)
                $firstOpSell = $p->outletProducts->firstWhere('selling_price', '>', 0)?->selling_price;
                $sell = max(0, (float) ($p->selling_price ?: ($firstOpSell ?? 0)));
                
                $minAlert = (int) $p->min_stock_alert;
            }

            $totalAssetCostValue += ($stock * $cost);
            $totalPotentialRevenue += ($stock * $sell);
            $totalStockUnits += $stock;

            if ($stock === 0) {
                $totalOutOfStockCount++;
            } elseif ($stock <= $minAlert) {
                $totalLowStockCount++;
            }
        }

        $totalPotentialProfit = max(0, $totalPotentialRevenue - $totalAssetCostValue);
        $potentialMarginPercent = $totalPotentialRevenue > 0 ? round(($totalPotentialProfit / $totalPotentialRevenue) * 100, 1) : 0;

        // 2. Query Products for Table with Filters & Sorting
        $query = Product::query()
            ->with(['category', 'supplier', 'outletProducts'])
            ->active()
            ->when($this->search, function ($q) {
                $term = '%' . $this->search . '%';
                $q->where(function ($sub) use ($term) {
                    $sub->where('name', 'like', $term)
                        ->orWhere('barcode', 'like', $term)
                        ->orWhere('sku', 'like', $term);
                });
            })
            ->when($this->categoryId, function ($q) {
                $q->where('category_id', $this->categoryId);
            });

        // Filter stock status per outlet or global
        if ($this->outletId) {
            $selectedOutletId = $this->outletId;
            if ($this->stockStatus === 'out') {
                $query->whereHas('outletProducts', fn($q) => $q->where('outlet_id', $selectedOutletId)->where('stock', '<=', 0));
            } elseif ($this->stockStatus === 'low') {
                $query->whereHas('outletProducts', fn($q) => $q->where('outlet_id', $selectedOutletId)->where('stock', '>', 0)->whereColumn('stock', '<=', 'min_stock_alert'));
            } elseif ($this->stockStatus === 'safe') {
                $query->whereHas('outletProducts', fn($q) => $q->where('outlet_id', $selectedOutletId)->whereColumn('stock', '>', 'min_stock_alert'));
            }
        } else {
            if ($this->stockStatus === 'out') {
                $query->where(function ($q) {
                    $q->whereHas('outletProducts', fn($sq) => $sq->selectRaw('SUM(stock)')->havingRaw('SUM(stock) <= 0'))
                      ->orWhere(fn($sq2) => $sq2->doesntHave('outletProducts')->where('stock', '<=', 0));
                });
            } elseif ($this->stockStatus === 'low') {
                $query->where(function ($q) {
                    $q->whereHas('outletProducts', fn($sq) => $sq->where('stock', '>', 0)->whereColumn('stock', '<=', 'min_stock_alert'))
                      ->orWhere(fn($sq2) => $sq2->doesntHave('outletProducts')->where('stock', '>', 0)->whereColumn('stock', '<=', 'min_stock_alert'));
                });
            } elseif ($this->stockStatus === 'safe') {
                $query->where(function ($q) {
                    $q->whereHas('outletProducts', fn($sq) => $sq->whereColumn('stock', '>', 'min_stock_alert'))
                      ->orWhere(fn($sq2) => $sq2->doesntHave('outletProducts')->whereColumn('stock', '>', 'min_stock_alert'));
                });
            }
        }

        // Apply dynamic sorting
        switch ($this->sortBy) {
            case 'stock_desc':
                $query->orderByDesc('stock');
                break;
            case 'profit_desc':
                $query->orderByRaw('(stock * (selling_price - cost_price)) DESC');
                break;
            case 'asset_value_desc':
                $query->orderByRaw('(stock * cost_price) DESC');
                break;
            case 'name_asc':
            default:
                $query->orderBy('name', 'asc');
                break;
        }

        $products = $query->paginate(15);

        return view('livewire.admin.report.inventory-valuation-report', [
            'categories' => $categories,
            'outlets' => $outlets,
            'products' => $products,
            'totalAssetCostValue' => $totalAssetCostValue,
            'totalPotentialRevenue' => $totalPotentialRevenue,
            'totalPotentialProfit' => $totalPotentialProfit,
            'potentialMarginPercent' => $potentialMarginPercent,
            'totalStockUnits' => $totalStockUnits,
            'totalProductsCount' => $totalProductsCount,
            'totalLowStockCount' => $totalLowStockCount,
            'totalOutOfStockCount' => $totalOutOfStockCount,
        ]);
    }
}
