<?php

namespace App\Livewire\Admin\Finance;

use App\Models\Asset;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Inventaris Aset & Freezer Toko - Syafa Frozen Food')]
class AssetIndex extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true)]
    public string $categoryFilter = '';

    #[Url(history: true)]
    public string $conditionFilter = '';

    // Modal Form State (Tambah & Edit)
    public bool $showFormModal = false;
    public ?int $editingAssetId = null;
    public string $assetName = '';
    public string $assetCategory = 'freezer_refrigeration';
    public string $assetBrand = '';
    public string $assetSpecs = '';
    public string $assetLocation = '';
    public string $assetPurchaseDate = '';
    public float|int|string $assetPurchaseCost = '';
    public string $assetCondition = 'good';
    public string $assetNotes = '';

    // Modal Delete State
    public bool $showDeleteModal = false;
    public ?int $deletingAssetId = null;
    public string $deletingAssetName = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedCategoryFilter(): void
    {
        $this->resetPage();
    }

    public function updatedConditionFilter(): void
    {
        $this->resetPage();
    }

    // ================= FORM OPERATIONS =================
    public function openCreateModal(): void
    {
        $this->reset([
            'editingAssetId', 'assetName', 'assetBrand', 'assetSpecs', 
            'assetLocation', 'assetPurchaseCost', 'assetNotes'
        ]);
        $this->assetCategory = 'freezer_refrigeration';
        $this->assetCondition = 'good';
        $this->assetPurchaseDate = Carbon::today()->format('Y-m-d');
        $this->resetValidation();
        $this->showFormModal = true;
    }

    public function openEditModal(int $id): void
    {
        $asset = Asset::findOrFail($id);
        $this->editingAssetId = $asset->id;
        $this->assetName = $asset->name;
        $this->assetCategory = $asset->category;
        $this->assetBrand = $asset->brand ?? '';
        $this->assetSpecs = $asset->specs ?? '';
        $this->assetLocation = $asset->location ?? '';
        $this->assetPurchaseDate = $asset->purchase_date ? $asset->purchase_date->format('Y-m-d') : '';
        $this->assetPurchaseCost = (int) $asset->purchase_cost;
        $this->assetCondition = $asset->condition;
        $this->assetNotes = $asset->notes ?? '';
        $this->resetValidation();
        $this->showFormModal = true;
    }

    public function closeFormModal(): void
    {
        $this->showFormModal = false;
        $this->reset([
            'editingAssetId', 'assetName', 'assetBrand', 'assetSpecs', 
            'assetLocation', 'assetPurchaseCost', 'assetNotes'
        ]);
    }

    public function saveAsset(): void
    {
        $this->validate([
            'assetName' => 'required|string|max:150',
            'assetCategory' => 'required|in:freezer_refrigeration,pos_hardware,operational_equipment,furniture_facility,other',
            'assetBrand' => 'nullable|string|max:100',
            'assetSpecs' => 'nullable|string|max:200',
            'assetLocation' => 'nullable|string|max:100',
            'assetPurchaseDate' => 'nullable|date',
            'assetPurchaseCost' => 'required|numeric|min:0',
            'assetCondition' => 'required|in:good,fair,maintenance,broken',
            'assetNotes' => 'nullable|string|max:500',
        ]);

        if ($this->editingAssetId) {
            $asset = Asset::findOrFail($this->editingAssetId);
            $asset->update([
                'name' => $this->assetName,
                'category' => $this->assetCategory,
                'brand' => $this->assetBrand ?: null,
                'specs' => $this->assetSpecs ?: null,
                'location' => $this->assetLocation ?: null,
                'purchase_date' => $this->assetPurchaseDate ?: null,
                'purchase_cost' => $this->assetPurchaseCost,
                'condition' => $this->assetCondition,
                'notes' => $this->assetNotes ?: null,
            ]);

            session()->flash('success', "Data aset '{$this->assetName}' berhasil diperbarui.");
        } else {
            Asset::create([
                'asset_code' => Asset::generateAssetCode(),
                'user_id' => Auth::id(),
                'name' => $this->assetName,
                'category' => $this->assetCategory,
                'brand' => $this->assetBrand ?: null,
                'specs' => $this->assetSpecs ?: null,
                'location' => $this->assetLocation ?: null,
                'purchase_date' => $this->assetPurchaseDate ?: null,
                'purchase_cost' => $this->assetPurchaseCost,
                'condition' => $this->assetCondition,
                'notes' => $this->assetNotes ?: null,
            ]);

            session()->flash('success', "Aset baru '{$this->assetName}' berhasil ditambahkan ke inventaris.");
        }

        $this->closeFormModal();
    }

    public function confirmDelete(int $id): void
    {
        $asset = Asset::findOrFail($id);
        $this->deletingAssetId = $asset->id;
        $this->deletingAssetName = $asset->name;
        $this->showDeleteModal = true;
    }

    public function deleteAsset(): void
    {
        if (!$this->deletingAssetId) return;

        $asset = Asset::findOrFail($this->deletingAssetId);
        $name = $asset->name;
        $asset->delete();

        session()->flash('success', "Aset '{$name}' berhasil dihapus dari sistem.");
        $this->showDeleteModal = false;
        $this->deletingAssetId = null;
    }

    public function render()
    {
        // 1. Calculate KPI Metrics across all assets
        $allAssets = Asset::all();
        $totalAssetValuation = $allAssets->sum('purchase_cost');
        $totalFreezerUnits = $allAssets->where('category', 'freezer_refrigeration')->count();
        $totalGoodConditionUnits = $allAssets->whereIn('condition', ['good', 'fair'])->count();
        $totalMaintenanceUnits = $allAssets->whereIn('condition', ['maintenance', 'broken'])->count();

        // 2. Query Asset records with filters
        $assetsQuery = Asset::query()
            ->with('user')
            ->when($this->categoryFilter, fn($q) => $q->where('category', $this->categoryFilter))
            ->when($this->conditionFilter, fn($q) => $q->where('condition', $this->conditionFilter))
            ->when($this->search, function ($q) {
                $term = '%' . $this->search . '%';
                $q->where(function ($sub) use ($term) {
                    $sub->where('name', 'like', $term)
                        ->orWhere('asset_code', 'like', $term)
                        ->orWhere('brand', 'like', $term)
                        ->orWhere('specs', 'like', $term)
                        ->orWhere('location', 'like', $term);
                });
            })
            ->latest('id');

        $assets = $assetsQuery->paginate(12);

        return view('livewire.admin.finance.asset-index', [
            'assets' => $assets,
            'totalAssetValuation' => $totalAssetValuation,
            'totalFreezerUnits' => $totalFreezerUnits,
            'totalGoodConditionUnits' => $totalGoodConditionUnits,
            'totalMaintenanceUnits' => $totalMaintenanceUnits,
        ]);
    }
}
