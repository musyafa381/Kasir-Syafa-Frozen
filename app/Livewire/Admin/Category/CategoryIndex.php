<?php

namespace App\Livewire\Admin\Category;

use App\Models\Category;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Kategori Produk - Syafa Frozen Food')]
class CategoryIndex extends Component
{
    use WithPagination;

    public string $search = '';

    // Modal State
    public bool $showModal = false;
    public ?int $editingCategoryId = null;

    // Form fields
    public string $name = '';
    public string $description = '';

    // Delete confirmation modal
    public bool $showDeleteModal = false;
    public ?int $deletingCategoryId = null;
    public string $deletingCategoryName = '';
    public int $deletingCategoryProductCount = 0;
    public string $deleteMode = 'detach'; // 'detach' (pindah ke Tanpa Kategori) or 'delete_products' (hapus produk juga)

    protected $rules = [
        'name' => 'required|min:2|max:100',
        'description' => 'nullable|max:255',
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->reset(['name', 'description', 'editingCategoryId']);
        $this->resetValidation();
        $this->showModal = true;
    }

    public function openEditModal(int $id): void
    {
        $this->resetValidation();
        $category = Category::findOrFail($id);
        $this->editingCategoryId = $category->id;
        $this->name = $category->name;
        $this->description = $category->description ?? '';
        $this->showModal = true;
    }

    public function saveCategory(): void
    {
        $this->validate();

        $slug = Str::slug($this->name);

        if ($this->editingCategoryId) {
            $category = Category::findOrFail($this->editingCategoryId);
            $category->update([
                'name' => $this->name,
                'slug' => $slug,
                'description' => $this->description,
            ]);
            session()->flash('success', 'Kategori berhasil diperbarui.');
        } else {
            Category::create([
                'name' => $this->name,
                'slug' => $slug,
                'description' => $this->description,
            ]);
            session()->flash('success', 'Kategori baru berhasil ditambahkan.');
        }

        $this->showModal = false;
        $this->reset(['name', 'description', 'editingCategoryId']);
    }

    public function confirmDelete(int $id): void
    {
        $category = Category::withCount('products')->findOrFail($id);
        $this->deletingCategoryId = $category->id;
        $this->deletingCategoryName = $category->name;
        $this->deletingCategoryProductCount = $category->products_count;
        $this->deleteMode = 'detach';
        $this->showDeleteModal = true;
    }

    public function deleteCategory(): void
    {
        if (!$this->deletingCategoryId) {
            return;
        }

        $category = Category::findOrFail($this->deletingCategoryId);
        $productCount = $category->products()->count();

        if ($this->deleteMode === 'delete_products') {
            $deletedCount = 0;
            $deactivatedCount = 0;
            foreach ($category->products as $product) {
                $purchaseCount = $product->purchaseItems()->count();
                $txCount = $product->transactionItems()->count();
                if ($purchaseCount > 0 || $txCount > 0) {
                    $product->update(['category_id' => null, 'is_active' => false]);
                    $deactivatedCount++;
                } else {
                    $product->delete();
                    $deletedCount++;
                }
            }
            $category->delete();
            $msg = "Kategori '{$this->deletingCategoryName}' berhasil dihapus.";
            if ($deactivatedCount > 0) {
                $msg .= " ({$deletedCount} produk terhapus, {$deactivatedCount} produk memiliki riwayat transaksi sehingga dinonaktifkan & dilepas kategorinya).";
            } else {
                $msg .= " ({$productCount} produk di dalamnya berhasil dihapus).";
            }
            session()->flash('success', $msg);
        } else {
            // Detach: ubah category_id menjadi null
            $category->products()->update(['category_id' => null]);
            $category->delete();
            session()->flash('success', "Kategori '{$this->deletingCategoryName}' berhasil dihapus. {$productCount} produk kini berstatus 'Tanpa Kategori'.");
        }

        $this->showDeleteModal = false;
        $this->reset(['deletingCategoryId', 'deletingCategoryName', 'deletingCategoryProductCount', 'deleteMode']);
    }

    public function render()
    {
        $categories = Category::query()
            ->withCount('products')
            ->when($this->search, function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.admin.category.category-index', [
            'categories' => $categories,
        ]);
    }
}
