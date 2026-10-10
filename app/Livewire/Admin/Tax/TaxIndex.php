<?php

namespace App\Livewire\Admin\Tax;

use App\Models\Tax;
use App\Models\Transaction;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Kelola Pajak - Syafa Frozen Food')]
class TaxIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterStatus = 'all'; // all, active, inactive

    // Modal Create / Edit
    public bool $showModal = false;
    public ?int $editingTaxId = null;

    // Form fields
    public string $name = '';
    public ?string $code = '';
    public $rate = 11.00;
    public bool $is_active = true;
    public bool $is_default = false;
    public ?string $description = '';

    // Modal Delete
    public bool $showDeleteModal = false;
    public ?int $deletingTaxId = null;
    public string $deletingTaxName = '';

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:50|unique:taxes,code,' . $this->editingTaxId,
            'rate' => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'description' => 'nullable|string|max:255',
        ];
    }

    protected $messages = [
        'name.required' => 'Nama pajak wajib diisi.',
        'code.unique' => 'Kode pajak ini sudah digunakan.',
        'rate.required' => 'Tarif pajak (%) wajib diisi.',
        'rate.min' => 'Tarif pajak minimal 0%.',
        'rate.max' => 'Tarif pajak maksimal 100%.',
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->resetValidation();
        $this->reset(['editingTaxId', 'name', 'code', 'rate', 'is_active', 'is_default', 'description']);
        $this->rate = 11.00;
        $this->is_active = true;
        
        // If there are no default taxes, suggest default
        if (Tax::where('is_default', true)->count() === 0) {
            $this->is_default = true;
        }

        $this->showModal = true;
    }

    public function openEditModal(int $id): void
    {
        $this->resetValidation();
        $tax = Tax::findOrFail($id);

        $this->editingTaxId = $tax->id;
        $this->name = $tax->name;
        $this->code = $tax->code;
        $this->rate = (float) $tax->rate;
        $this->is_active = (bool) $tax->is_active;
        $this->is_default = (bool) $tax->is_default;
        $this->description = $tax->description;

        $this->showModal = true;
    }

    public function saveTax(): void
    {
        $validated = $this->validate();

        // Normalisasi
        $validated['code'] = !empty(trim($this->code ?? '')) ? strtoupper(trim($this->code)) : null;
        $validated['description'] = !empty(trim($this->description ?? '')) ? trim($this->description) : null;
        $validated['rate'] = (float) $this->rate;
        $validated['is_active'] = (bool) $this->is_active;
        $validated['is_default'] = (bool) $this->is_default;

        if ($validated['is_default']) {
            Tax::where('is_default', true)->update(['is_default' => false]);
        }

        if ($this->editingTaxId) {
            $tax = Tax::findOrFail($this->editingTaxId);
            $tax->update($validated);
            $msg = "Master Pajak '{$tax->name}' berhasil diperbarui.";
            session()->flash('success', $msg);
            $this->dispatch('toast', ['message' => $msg, 'type' => 'success']);
        } else {
            $tax = Tax::create($validated);
            $msg = "Master Pajak '{$tax->name}' berhasil ditambahkan.";
            session()->flash('success', $msg);
            $this->dispatch('toast', ['message' => $msg, 'type' => 'success']);
        }

        $this->showModal = false;
        $this->reset(['editingTaxId', 'name', 'code', 'rate', 'is_active', 'is_default', 'description']);
    }

    public function toggleStatus(int $id): void
    {
        $tax = Tax::findOrFail($id);
        $tax->is_active = !$tax->is_active;
        if (!$tax->is_active && $tax->is_default) {
            $tax->is_default = false;
        }
        $tax->save();

        $msg = "Status pajak '{$tax->name}' diperbarui menjadi " . ($tax->is_active ? 'Aktif' : 'Nonaktif') . ".";
        session()->flash('success', $msg);
        $this->dispatch('toast', ['message' => $msg, 'type' => 'success']);
    }

    public function setDefault(int $id): void
    {
        Tax::where('is_default', true)->update(['is_default' => false]);
        
        $tax = Tax::findOrFail($id);
        $tax->is_default = true;
        $tax->is_active = true; // Default tax must be active
        $tax->save();

        $msg = "'{$tax->name}' sekarang ditetapkan sebagai Pajak Utama Kasir.";
        session()->flash('success', $msg);
        $this->dispatch('toast', ['message' => $msg, 'type' => 'success']);
    }

    public function confirmDelete(int $id): void
    {
        $tax = Tax::findOrFail($id);
        $this->deletingTaxId = $tax->id;
        $this->deletingTaxName = $tax->name;
        $this->showDeleteModal = true;
    }

    public function deleteTax(): void
    {
        if (!$this->deletingTaxId) {
            return;
        }

        $tax = Tax::findOrFail($this->deletingTaxId);
        $tax->delete();

        $msg = "Pajak '{$this->deletingTaxName}' berhasil dihapus.";
        session()->flash('success', $msg);
        $this->dispatch('toast', ['message' => $msg, 'type' => 'success']);
        $this->showDeleteModal = false;
        $this->reset(['deletingTaxId', 'deletingTaxName']);
    }

    public function render()
    {
        // KPIs
        $totalActive = Tax::active()->count();
        $defaultTax = Tax::where('is_default', true)->first();
        $totalTransactionsWithTax = Transaction::completed()->where('tax_amount', '>', 0)->count();
        $totalTaxCollected = (float) Transaction::completed()->sum('tax_amount');

        $taxes = Tax::query()
            ->withCount('transactions')
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('code', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterStatus === 'active', function ($q) {
                $q->active();
            })
            ->when($this->filterStatus === 'inactive', function ($q) {
                $q->where('is_active', false);
            })
            ->orderByDesc('is_default')
            ->latest()
            ->paginate(10);

        return view('livewire.admin.tax.tax-index', [
            'taxes' => $taxes,
            'totalActive' => $totalActive,
            'defaultTax' => $defaultTax,
            'totalTransactionsWithTax' => $totalTransactionsWithTax,
            'totalTaxCollected' => $totalTaxCollected,
        ]);
    }
}
