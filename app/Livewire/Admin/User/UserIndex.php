<?php

namespace App\Livewire\Admin\User;

use App\Models\Outlet;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Kelola Kasir & Pengguna - Syafa Frozen Food')]
class UserIndex extends Component
{
    use WithPagination;

    public string $search = '';

    // Modal Create / Edit
    public bool $showModal = false;
    public ?int $editingUserId = null;

    // Form fields
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $role = 'cashier'; // admin, cashier
    public ?int $outlet_id = null;
    public string $password = '';
    public bool $is_active = true;

    // Modal Delete
    public bool $showDeleteModal = false;
    public ?int $deletingUserId = null;
    public string $deletingUserName = '';

    protected function rules(): array
    {
        $rules = [
            'name' => 'required|min:2|max:100',
            'email' => 'required|email|max:150|unique:users,email,' . $this->editingUserId,
            'phone' => 'nullable|max:25',
            'role' => 'required|in:admin,cashier',
            'outlet_id' => 'nullable|exists:outlets,id',
            'is_active' => 'boolean',
        ];

        if (!$this->editingUserId) {
            $rules['password'] = 'required|min:6';
        } else {
            $rules['password'] = 'nullable|min:6';
        }

        return $rules;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->reset(['editingUserId', 'name', 'email', 'phone', 'password']);
        $this->role = 'cashier';
        $this->outlet_id = Outlet::where('is_main', true)->value('id') ?? Outlet::first()?->id;
        $this->is_active = true;
        $this->resetValidation();
        $this->showModal = true;
    }

    public function openEditModal(int $id): void
    {
        $this->resetValidation();
        $user = User::findOrFail($id);
        $this->editingUserId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone ?? '';
        $this->role = $user->role;
        $this->outlet_id = $user->outlet_id;
        $this->password = '';
        $this->is_active = (bool) $user->is_active;
        $this->showModal = true;
    }

    public function saveUser(): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone ?: null,
            'role' => $this->role,
            'outlet_id' => $this->outlet_id ?: null,
            'is_active' => $this->is_active,
        ];

        if (!empty($this->password)) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->editingUserId) {
            $user = User::findOrFail($this->editingUserId);
            $user->update($data);
            $msg = "Akun pengguna '{$user->name}' berhasil diperbarui.";
            session()->flash('success', $msg);
            $this->dispatch('toast', ['message' => $msg, 'type' => 'success']);
        } else {
            User::create($data);
            $msg = "Akun pengguna baru '{$this->name}' berhasil dibuat.";
            session()->flash('success', $msg);
            $this->dispatch('toast', ['message' => $msg, 'type' => 'success']);
        }

        $this->showModal = false;
        $this->reset(['editingUserId', 'name', 'email', 'phone', 'password', 'outlet_id']);
    }

    public function toggleStatus(int $id): void
    {
        if (Auth::id() === $id) {
            $err = 'Anda tidak dapat menonaktifkan akun yang sedang aktif digunakan.';
            session()->flash('error', $err);
            $this->dispatch('toast', ['message' => $err, 'type' => 'error']);
            return;
        }

        $user = User::findOrFail($id);
        $user->is_active = !$user->is_active;
        $user->save();

        $msg = "Status akun '{$user->name}' berhasil diperbarui.";
        session()->flash('success', $msg);
        $this->dispatch('toast', ['message' => $msg, 'type' => 'success']);
    }

    public function confirmDelete(int $id): void
    {
        if (Auth::id() === $id) {
            $err = 'Anda tidak dapat menghapus akun Anda sendiri.';
            session()->flash('error', $err);
            $this->dispatch('toast', ['message' => $err, 'type' => 'error']);
            return;
        }

        $user = User::findOrFail($id);
        $this->deletingUserId = $user->id;
        $this->deletingUserName = $user->name;
        $this->showDeleteModal = true;
    }

    public function deleteUser(): void
    {
        if (!$this->deletingUserId || Auth::id() === $this->deletingUserId) {
            return;
        }

        $user = User::findOrFail($this->deletingUserId);
        $user->delete();

        $msg = "Akun '{$this->deletingUserName}' berhasil dihapus.";
        session()->flash('success', $msg);
        $this->dispatch('toast', ['message' => $msg, 'type' => 'success']);
        $this->showDeleteModal = false;
        $this->reset(['deletingUserId', 'deletingUserName']);
    }

    public function render()
    {
        $users = User::query()
            ->with(['outlet'])
            ->withCount('transactions')
            ->withSum('transactions as total_sales_handled', 'grand_total')
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate(10);

        $outlets = Outlet::where('is_active', true)->orderBy('is_main', 'desc')->orderBy('name')->get();

        return view('livewire.admin.user.user-index', [
            'users' => $users,
            'outlets' => $outlets,
        ]);
    }
}
