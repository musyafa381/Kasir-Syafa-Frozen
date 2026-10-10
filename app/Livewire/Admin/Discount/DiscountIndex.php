<?php

namespace App\Livewire\Admin\Discount;

use App\Models\Discount;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Kelola Diskon & Promo - Syafa Frozen Food')]
class DiscountIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterType = 'all'; // all, fixed, percentage
    public string $filterStatus = 'all'; // all, active, inactive

    // Modal Create / Edit
    public bool $showModal = false;
    public ?int $editingDiscountId = null;

    // Form fields
    public string $name = '';
    public ?string $code = '';
    public string $type = 'fixed'; // fixed | percentage
    public $value = 0;
    public $min_purchase = 0;
    public $max_discount_amount = null;
    public ?string $start_date = null;
    public ?string $end_date = null;
    public bool $is_active = true;
    public ?string $notes = '';

    // Modal Delete
    public bool $showDeleteModal = false;
    public ?int $deletingDiscountId = null;
    public string $deletingDiscountName = '';

    protected function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:50|unique:discounts,code,' . $this->editingDiscountId,
            'type' => 'required|in:fixed,percentage',
            'value' => 'required|numeric|min:0.01',
            'min_purchase' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'is_active' => 'boolean',
            'notes' => 'nullable|string|max:255',
        ];

        if ($this->type === 'percentage') {
            $rules['value'] = 'required|numeric|min:0.01|max:100';
        }

        if (!empty($this->start_date) && !empty($this->end_date)) {
            $rules['end_date'] = 'nullable|date|after_or_equal:start_date';
        }

        return $rules;
    }

    protected $messages = [
        'name.required' => 'Nama diskon/promo wajib diisi.',
        'code.unique' => 'Kode promo ini sudah digunakan oleh promo lain.',
        'value.required' => 'Nilai diskon wajib diisi.',
        'value.min' => 'Nilai diskon harus lebih dari 0.',
        'value.max' => 'Nilai persentase maksimal 100%.',
        'end_date.after_or_equal' => 'Tanggal berakhir harus sama atau setelah tanggal mulai.',
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterType(): void
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
        $this->editingDiscountId = null;
        $this->name = '';
        $this->code = '';
        $this->type = 'fixed';
        $this->value = 0;
        $this->min_purchase = 0;
        $this->max_discount_amount = null;
        $this->start_date = null;
        $this->end_date = null;
        $this->is_active = true;
        $this->notes = '';
        $this->showModal = true;
    }

    public function openEditModal(int $id): void
    {
        $this->resetValidation();
        $discount = Discount::findOrFail($id);
        $this->editingDiscountId = $discount->id;
        $this->name = $discount->name;
        $this->code = $discount->code ?? '';
        $this->type = $discount->type;
        $this->value = (float) $discount->value;
        $this->min_purchase = (float) $discount->min_purchase;
        $this->max_discount_amount = $discount->max_discount_amount !== null ? (float) $discount->max_discount_amount : null;
        $this->start_date = $discount->start_date ? $discount->start_date->format('Y-m-d') : null;
        $this->end_date = $discount->end_date ? $discount->end_date->format('Y-m-d') : null;
        $this->is_active = (bool) $discount->is_active;
        $this->notes = $discount->notes ?? '';
        $this->showModal = true;
    }

    public function saveDiscount(): void
    {
        // Sanitize & normalize fields
        $this->name = trim($this->name);
        $this->code = !empty($this->code) ? strtoupper(trim($this->code)) : null;
        $this->start_date = !empty($this->start_date) ? $this->start_date : null;
        $this->end_date = !empty($this->end_date) ? $this->end_date : null;
        $this->notes = !empty($this->notes) ? trim($this->notes) : null;

        // Clean numeric fields
        $numericValue = (float) (is_numeric($this->value) ? $this->value : preg_replace('/[^0-9.]/', '', (string)$this->value));
        $numericMinPurchase = !empty($this->min_purchase) ? (float) (is_numeric($this->min_purchase) ? $this->min_purchase : preg_replace('/[^0-9.]/', '', (string)$this->min_purchase)) : 0;
        $numericMaxDiscount = ($this->type === 'percentage' && !empty($this->max_discount_amount)) 
            ? (float) (is_numeric($this->max_discount_amount) ? $this->max_discount_amount : preg_replace('/[^0-9.]/', '', (string)$this->max_discount_amount))
            : null;

        $this->value = $numericValue;
        $this->min_purchase = $numericMinPurchase;
        $this->max_discount_amount = $numericMaxDiscount;

        $this->validate();

        $data = [
            'name' => $this->name,
            'code' => $this->code,
            'type' => $this->type,
            'value' => $numericValue,
            'min_purchase' => $numericMinPurchase,
            'max_discount_amount' => $numericMaxDiscount,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'is_active' => $this->is_active,
            'notes' => $this->notes,
        ];

        if ($this->editingDiscountId) {
            $discount = Discount::findOrFail($this->editingDiscountId);
            $discount->update($data);
            $msg = "Diskon/promo '{$discount->name}' berhasil diperbarui.";
            session()->flash('success', $msg);
            $this->dispatch('toast', ['message' => $msg, 'type' => 'success']);
        } else {
            Discount::create($data);
            $msg = "Diskon/promo baru '{$this->name}' berhasil ditambahkan.";
            session()->flash('success', $msg);
            $this->dispatch('toast', ['message' => $msg, 'type' => 'success']);
        }

        $this->showModal = false;
        $this->reset([
            'editingDiscountId',
            'name',
            'code',
            'value',
            'min_purchase',
            'max_discount_amount',
            'start_date',
            'end_date',
            'notes',
        ]);
    }

    public function toggleStatus(int $id): void
    {
        $discount = Discount::findOrFail($id);
        $discount->is_active = !$discount->is_active;
        $discount->save();
        $msg = "Status promo '{$discount->name}' diperbarui menjadi " . ($discount->is_active ? 'Aktif' : 'Nonaktif') . ".";
        session()->flash('success', $msg);
        $this->dispatch('toast', ['message' => $msg, 'type' => 'success']);
    }

    public function confirmDelete(int $id): void
    {
        $discount = Discount::findOrFail($id);
        $this->deletingDiscountId = $discount->id;
        $this->deletingDiscountName = $discount->name;
        $this->showDeleteModal = true;
    }

    public function deleteDiscount(): void
    {
        if (!$this->deletingDiscountId) {
            return;
        }

        $discount = Discount::findOrFail($this->deletingDiscountId);
        $discount->delete();

        $msg = "Diskon/promo '{$this->deletingDiscountName}' berhasil dihapus.";
        session()->flash('success', $msg);
        $this->dispatch('toast', ['message' => $msg, 'type' => 'success']);
        $this->showDeleteModal = false;
        $this->reset(['deletingDiscountId', 'deletingDiscountName']);
    }

    public function render()
    {
        // KPIs
        $totalActive = Discount::active()->count();
        $totalPercentage = Discount::where('type', 'percentage')->where('is_active', true)->count();
        $totalFixed = Discount::where('type', 'fixed')->where('is_active', true)->count();
        $totalUsage = (int) Discount::withCount('transactions')->get()->sum('transactions_count');

        $discounts = Discount::query()
            ->withCount('transactions')
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('code', 'like', '%' . $this->search . '%')
                        ->orWhere('notes', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterType !== 'all', function ($q) {
                $q->where('type', $this->filterType);
            })
            ->when($this->filterStatus === 'active', function ($q) {
                $q->active();
            })
            ->when($this->filterStatus === 'inactive', function ($q) {
                $q->where('is_active', false);
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.discount.discount-index', [
            'discounts' => $discounts,
            'totalActive' => $totalActive,
            'totalPercentage' => $totalPercentage,
            'totalFixed' => $totalFixed,
            'totalUsage' => $totalUsage,
        ]);
    }
}
