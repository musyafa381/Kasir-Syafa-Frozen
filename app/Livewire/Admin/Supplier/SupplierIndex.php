<?php

namespace App\Livewire\Admin\Supplier;

use App\Models\Supplier;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Pemasok (Supplier) - Syafa Frozen Food')]
class SupplierIndex extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true)]
    public string $statusFilter = ''; // '', '1', '0'

    // Form Modal State
    public bool $showFormModal = false;
    public ?int $editingId = null;
    public string $name = '';
    public string $code = '';
    public string $contact_person = '';
    public string $phone = '';
    public string $email = '';
    public string $address = '';
    public string $payment_term = 'cash'; // cash, tempo_7, tempo_14, tempo_30, custom
    public string $notes = '';
    public bool $is_active = true;

    // Detail Modal State
    public bool $showDetailModal = false;
    public ?Supplier $selectedSupplier = null;

    // Delete Modal State
    public bool $showDeleteModal = false;
    public ?int $deletingSupplierId = null;
    public string $deletingSupplierName = '';
    public int $deletingSupplierPurchaseCount = 0;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->resetValidation();
        $this->reset(['editingId', 'name', 'code', 'contact_person', 'phone', 'email', 'address', 'notes']);
        $this->payment_term = 'cash';
        $this->is_active = true;

        // Auto-generate next code e.g. SUP-001
        $lastSupplier = Supplier::orderBy('id', 'desc')->first();
        $nextNum = $lastSupplier ? ($lastSupplier->id + 1) : 1;
        $this->code = 'SUP-' . str_pad((string) $nextNum, 3, '0', STR_PAD_LEFT);

        $this->showFormModal = true;
    }

    public function openEditModal(int $id): void
    {
        $this->resetValidation();
        $supplier = Supplier::findOrFail($id);

        $this->editingId = $supplier->id;
        $this->name = $supplier->name;
        $this->code = $supplier->code ?? '';
        $this->contact_person = $supplier->contact_person ?? '';
        $this->phone = $supplier->phone ?? '';
        $this->email = $supplier->email ?? '';
        $this->address = $supplier->address ?? '';
        $this->payment_term = $supplier->payment_term ?? 'cash';
        $this->notes = $supplier->notes ?? '';
        $this->is_active = (bool) $supplier->is_active;

        $this->showFormModal = true;
    }

    public function saveSupplier(): void
    {
        $rules = [
            'name' => 'required|string|max:150',
            'code' => 'nullable|string|max:50|unique:suppliers,code,' . ($this->editingId ?? 'NULL') . ',id',
            'contact_person' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string',
            'payment_term' => 'required|string',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ];

        $validated = $this->validate($rules);

        if ($this->editingId) {
            $supplier = Supplier::findOrFail($this->editingId);
            $supplier->update($validated);
            $msg = "Data supplier {$supplier->name} berhasil diperbarui.";
            session()->flash('success', $msg);
            $this->dispatch('toast', ['message' => $msg, 'type' => 'success']);
        } else {
            $supplier = Supplier::create($validated);
            $msg = "Supplier baru {$supplier->name} berhasil ditambahkan.";
            session()->flash('success', $msg);
            $this->dispatch('toast', ['message' => $msg, 'type' => 'success']);
        }

        $this->showFormModal = false;
        $this->reset(['editingId', 'name', 'code', 'contact_person', 'phone', 'email', 'address', 'notes']);
    }

    public function openDetail(int $id): void
    {
        $this->selectedSupplier = Supplier::with(['products', 'purchases' => function ($q) {
            $q->latest()->take(10);
        }])->findOrFail($id);

        $this->showDetailModal = true;
    }

    public function closeDetail(): void
    {
        $this->showDetailModal = false;
        $this->selectedSupplier = null;
    }

    public function confirmDelete(int $id): void
    {
        $supplier = Supplier::withCount(['purchases', 'products'])->findOrFail($id);
        $this->deletingSupplierId = $supplier->id;
        $this->deletingSupplierName = $supplier->name;
        $this->deletingSupplierPurchaseCount = $supplier->purchases_count;
        $this->showDeleteModal = true;
    }

    public function deleteSupplier(): void
    {
        if (!$this->deletingSupplierId) return;

        $supplier = Supplier::withCount('purchases')->findOrFail($this->deletingSupplierId);

        if ($supplier->purchases_count > 0) {
            $err = "Supplier {$supplier->name} tidak dapat dihapus karena memiliki riwayat faktur pembelian.";
            session()->flash('error', $err);
            $this->dispatch('toast', ['message' => $err, 'type' => 'error']);
            $this->showDeleteModal = false;
            return;
        }

        // Unlink products if any
        $supplier->products()->update(['supplier_id' => null]);
        $supplier->delete();

        $msg = "Supplier {$this->deletingSupplierName} berhasil dihapus.";
        session()->flash('success', $msg);
        $this->dispatch('toast', ['message' => $msg, 'type' => 'success']);
        $this->showDeleteModal = false;
        $this->reset(['deletingSupplierId', 'deletingSupplierName', 'deletingSupplierPurchaseCount']);
    }

    public function render()
    {
        $totalSuppliers = Supplier::count();
        $activeSuppliers = Supplier::where('is_active', true)->count();
        $tempoSuppliers = Supplier::where('payment_term', '!=', 'cash')->count();

        $suppliers = Supplier::query()
            ->withCount(['products', 'purchases'])
            ->when($this->search, function ($q) {
                $term = '%' . $this->search . '%';
                $q->where(function ($sub) use ($term) {
                    $sub->where('name', 'like', $term)
                        ->orWhere('code', 'like', $term)
                        ->orWhere('contact_person', 'like', $term)
                        ->orWhere('phone', 'like', $term);
                });
            })
            ->when($this->statusFilter !== '', function ($q) {
                $q->where('is_active', (bool) $this->statusFilter);
            })
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.admin.supplier.supplier-index', [
            'suppliers' => $suppliers,
            'totalSuppliers' => $totalSuppliers,
            'activeSuppliers' => $activeSuppliers,
            'tempoSuppliers' => $tempoSuppliers,
        ]);
    }
}
