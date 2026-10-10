<?php

namespace App\Livewire\Admin\Customer;

use App\Models\Customer;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Pelanggan & Member Setia - Syafa Frozen Food')]
class CustomerIndex extends Component
{
    use WithPagination;

    public string $search = '';

    // Modal Create/Edit
    public bool $showModal = false;
    public ?int $editingCustomerId = null;

    // Form fields
    public string $name = '';
    public string $phone = '';
    public string $member_code = '';
    public string $address = '';
    public string $notes = '';
    public bool $is_active = true;

    // Modal Delete
    public bool $showDeleteModal = false;
    public ?int $deletingCustomerId = null;
    public string $deletingCustomerName = '';

    protected function rules(): array
    {
        return [
            'name' => 'required|min:2|max:100',
            'phone' => 'nullable|max:25',
            'member_code' => 'required|max:50|unique:customers,member_code,' . $this->editingCustomerId,
            'address' => 'nullable|max:255',
            'notes' => 'nullable|max:255',
            'is_active' => 'boolean',
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function generateCode(): void
    {
        $this->member_code = Customer::generateMemberCode();
    }

    public function openCreateModal(): void
    {
        $this->reset(['editingCustomerId', 'name', 'phone', 'address', 'notes']);
        $this->is_active = true;
        $this->generateCode();
        $this->resetValidation();
        $this->showModal = true;
    }

    public function openEditModal(int $id): void
    {
        $this->resetValidation();
        $customer = Customer::findOrFail($id);
        $this->editingCustomerId = $customer->id;
        $this->name = $customer->name;
        $this->phone = $customer->phone ?? '';
        $this->member_code = $customer->member_code;
        $this->address = $customer->address ?? '';
        $this->notes = $customer->notes ?? '';
        $this->is_active = $customer->is_active;
        $this->showModal = true;
    }

    public function saveCustomer(): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'phone' => $this->phone ?: null,
            'member_code' => $this->member_code,
            'address' => $this->address ?: null,
            'notes' => $this->notes ?: null,
            'is_active' => $this->is_active,
        ];

        if ($this->editingCustomerId) {
            $customer = Customer::findOrFail($this->editingCustomerId);
            $customer->update($data);
            $msg = "Data pelanggan '{$customer->name}' berhasil diperbarui.";
            session()->flash('success', $msg);
            $this->dispatch('toast', ['message' => $msg, 'type' => 'success']);
        } else {
            Customer::create($data);
            $msg = "Pelanggan setia baru '{$this->name}' berhasil didaftarkan.";
            session()->flash('success', $msg);
            $this->dispatch('toast', ['message' => $msg, 'type' => 'success']);
        }

        $this->showModal = false;
        $this->reset(['editingCustomerId', 'name', 'phone', 'member_code', 'address', 'notes']);
    }

    public function toggleStatus(int $id): void
    {
        $customer = Customer::findOrFail($id);
        $customer->is_active = !$customer->is_active;
        $customer->save();
        $msg = "Status member '{$customer->name}' diperbarui.";
        session()->flash('success', $msg);
        $this->dispatch('toast', ['message' => $msg, 'type' => 'success']);
    }

    public function confirmDelete(int $id): void
    {
        $customer = Customer::findOrFail($id);
        $this->deletingCustomerId = $customer->id;
        $this->deletingCustomerName = $customer->name;
        $this->showDeleteModal = true;
    }

    public function deleteCustomer(): void
    {
        if (!$this->deletingCustomerId) {
            return;
        }

        $customer = Customer::findOrFail($this->deletingCustomerId);
        $customer->delete();

        $msg = "Pelanggan '{$this->deletingCustomerName}' berhasil dihapus.";
        session()->flash('success', $msg);
        $this->dispatch('toast', ['message' => $msg, 'type' => 'success']);
        $this->showDeleteModal = false;
        $this->reset(['deletingCustomerId', 'deletingCustomerName']);
    }

    public function render()
    {
        $totalCustomers = Customer::count();
        $totalActive = Customer::where('is_active', true)->count();
        $totalMemberSales = (float) \App\Models\Transaction::whereNotNull('customer_id')->where('status', 'completed')->sum('grand_total');
        $totalMemberOrders = (int) \App\Models\Transaction::whereNotNull('customer_id')->where('status', 'completed')->count();

        $customers = Customer::query()
            ->withCount('transactions')
            ->withSum(['transactions' => function ($q) {
                $q->where('status', 'completed');
            }], 'grand_total')
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%')
                        ->orWhere('member_code', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.customer.customer-index', [
            'customers' => $customers,
            'totalCustomers' => $totalCustomers,
            'totalActive' => $totalActive,
            'totalMemberSales' => $totalMemberSales,
            'totalMemberOrders' => $totalMemberOrders,
        ]);
    }
}
