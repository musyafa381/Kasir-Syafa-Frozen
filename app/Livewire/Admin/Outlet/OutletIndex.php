<?php

namespace App\Livewire\Admin\Outlet;

use App\Models\Outlet;
use App\Models\Product;
use App\Models\OutletProduct;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Kelola Cabang Toko - Syafa Frozen Food')]
class OutletIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterStatus = 'all';

    // Form Modal State
    public bool $showModal = false;
    public ?int $editingOutletId = null;
    public string $code = '';
    public string $name = '';
    public string $address = '';
    public string $phone = '';
    public string $receipt_header = '';
    public string $receipt_footer = '';
    public bool $is_main = false;
    public bool $is_active = true;

    // Delete Confirmation
    public bool $showDeleteModal = false;
    public ?int $deletingOutletId = null;
    public string $deletingOutletName = '';

    protected function rules(): array
    {
        return [
            'code' => 'required|string|max:50|unique:outlets,code,' . $this->editingOutletId,
            'name' => 'required|string|max:150',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:30',
            'receipt_header' => 'nullable|string|max:500',
            'receipt_footer' => 'nullable|string|max:500',
            'is_main' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function generateNextCode(): string
    {
        $count = Outlet::count() + 1;
        $code = 'CB-' . str_pad((string) $count, 3, '0', STR_PAD_LEFT);
        while (Outlet::where('code', $code)->exists()) {
            $count++;
            $code = 'CB-' . str_pad((string) $count, 3, '0', STR_PAD_LEFT);
        }
        return $code;
    }

    public function openCreateModal(): void
    {
        $this->resetValidation();
        $this->editingOutletId = null;
        $this->code = $this->generateNextCode();
        $this->name = '';
        $this->address = '';
        $this->phone = '';
        $this->receipt_header = '';
        $this->receipt_footer = 'Terima Kasih Atas Kunjungan Anda!';
        $this->is_main = Outlet::count() === 0;
        $this->is_active = true;
        $this->showModal = true;
    }

    public function openEditModal(int $id): void
    {
        $this->resetValidation();
        $outlet = Outlet::findOrFail($id);
        $this->editingOutletId = $outlet->id;
        $this->code = $outlet->code;
        $this->name = $outlet->name;
        $this->address = $outlet->address ?? '';
        $this->phone = $outlet->phone ?? '';
        $this->receipt_header = $outlet->receipt_header ?? '';
        $this->receipt_footer = $outlet->receipt_footer ?? '';
        $this->is_main = (bool) $outlet->is_main;
        $this->is_active = (bool) $outlet->is_active;
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate();

        // Jika diset sebagai cabang utama, un-set cabang utama lainnya
        if ($this->is_main) {
            Outlet::where('is_main', true)
                ->where('id', '!=', $this->editingOutletId)
                ->update(['is_main' => false]);
        }

        if ($this->editingOutletId) {
            $outlet = Outlet::findOrFail($this->editingOutletId);
            $outlet->update($validated);

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Data cabang ' . $outlet->name . ' berhasil diperbarui!'
            ]);
        } else {
            $outlet = Outlet::create($validated);

            // Inisialisasi outlet_products untuk cabang baru dari seluruh produk aktif
            $allProducts = Product::all();
            foreach ($allProducts as $p) {
                OutletProduct::create([
                    'outlet_id' => $outlet->id,
                    'product_id' => $p->id,
                    'selling_price' => $p->selling_price,
                    'loyalty_price' => $p->loyalty_price ?? $p->selling_price,
                    'box_selling_price' => $p->box_selling_price,
                    'box_loyalty_price' => $p->box_loyalty_price,
                    'cost_price' => $p->cost_price,
                    'stock' => 0, // Stok awal 0, diisi via mutasi/transfer stok
                    'min_stock_alert' => $p->min_stock_alert,
                ]);
            }

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Cabang baru ' . $outlet->name . ' berhasil ditambahkan!'
            ]);
        }

        $this->showModal = false;
    }

    public function confirmDelete(int $id): void
    {
        $outlet = Outlet::findOrFail($id);
        if ($outlet->is_main) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Cabang Utama (Pusat) tidak boleh dihapus!'
            ]);
            return;
        }

        if ($outlet->transactions()->count() > 0) {
            $this->dispatch('toast', [
                'type' => 'warning',
                'message' => 'Cabang ini memiliki riwayat transaksi dan tidak dapat dihapus. Anda dapat menonaktifkannya.'
            ]);
            return;
        }

        $this->deletingOutletId = $outlet->id;
        $this->deletingOutletName = $outlet->name;
        $this->showDeleteModal = true;
    }

    public function deleteOutlet(): void
    {
        if ($this->deletingOutletId) {
            $outlet = Outlet::findOrFail($this->deletingOutletId);
            if (!$outlet->is_main && $outlet->transactions()->count() === 0) {
                $outlet->delete();
                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Cabang berhasil dihapus!'
                ]);
            }
        }
        $this->showDeleteModal = false;
        $this->deletingOutletId = null;
    }

    public function toggleStatus(int $id): void
    {
        $outlet = Outlet::findOrFail($id);
        if ($outlet->is_main && $outlet->is_active) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Cabang Utama (Pusat) harus selalu aktif!'
            ]);
            return;
        }

        $outlet->is_active = !$outlet->is_active;
        $outlet->save();

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Status cabang ' . $outlet->name . ' diubah menjadi ' . ($outlet->is_active ? 'Aktif' : 'Non-Aktif') . '.'
        ]);
    }

    public function setAsMain(int $id): void
    {
        Outlet::where('is_main', true)->update(['is_main' => false]);
        $outlet = Outlet::findOrFail($id);
        $outlet->is_main = true;
        $outlet->is_active = true;
        $outlet->save();

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => $outlet->name . ' berhasil dijadikan sebagai Cabang Utama (Pusat)!'
        ]);
    }

    public function render()
    {
        $query = Outlet::query()
            ->withCount(['users', 'transactions', 'outletProducts']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('code', 'like', '%' . $this->search . '%')
                  ->orWhere('address', 'like', '%' . $this->search . '%')
                  ->orWhere('phone', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterStatus === 'active') {
            $query->where('is_active', true);
        } elseif ($this->filterStatus === 'inactive') {
            $query->where('is_active', false);
        }

        $outlets = $query->orderByDesc('is_main')->orderBy('name')->paginate(10);

        $totalOutlets = Outlet::count();
        $activeOutlets = Outlet::where('is_active', true)->count();
        $totalProductsCount = Product::count();

        return view('livewire.admin.outlet.outlet-index', [
            'outlets' => $outlets,
            'totalOutlets' => $totalOutlets,
            'activeOutlets' => $activeOutlets,
            'totalProductsCount' => $totalProductsCount,
        ]);
    }
}
