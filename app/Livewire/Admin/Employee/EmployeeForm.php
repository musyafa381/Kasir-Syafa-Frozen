<?php

namespace App\Livewire\Admin\Employee;

use App\Models\Employee;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Form Karyawan - Syafa Frozen Food')]
class EmployeeForm extends Component
{
    public ?int $employeeId = null;
    public bool $isEditing = false;

    // Form fields
    public string $employee_code = '';
    public ?int $user_id = null;
    public string $name = '';
    public string $phone = '';
    public string $position = 'Kasir';
    public string $employment_status = 'tetap';
    public float|int|string $base_salary = '';
    public float|int|string $daily_allowance = '';
    public string $join_date = '';
    public string $bank_name = '';
    public string $bank_account_number = '';
    public string $bank_account_holder = '';
    public string $address = '';
    public string $notes = '';
    public bool $is_active = true;

    public function mount(?int $id = null): void
    {
        if ($id) {
            $this->isEditing = true;
            $this->employeeId = $id;
            $employee = Employee::findOrFail($id);

            $this->employee_code = $employee->employee_code;
            $this->user_id = $employee->user_id;
            $this->name = $employee->name;
            $this->phone = $employee->phone ?? '';
            $this->position = $employee->position;
            $this->employment_status = $employee->employment_status;
            $this->base_salary = (int) $employee->base_salary;
            $this->daily_allowance = (int) $employee->daily_allowance;
            $this->join_date = $employee->join_date ? $employee->join_date->format('Y-m-d') : '';
            $this->bank_name = $employee->bank_name ?? '';
            $this->bank_account_number = $employee->bank_account_number ?? '';
            $this->bank_account_holder = $employee->bank_account_holder ?? '';
            $this->address = $employee->address ?? '';
            $this->notes = $employee->notes ?? '';
            $this->is_active = $employee->is_active;
        } else {
            $this->isEditing = false;
            $this->employee_code = Employee::generateEmployeeCode();
            $this->position = 'Kasir';
            $this->employment_status = 'tetap';
            $this->base_salary = '';
            $this->daily_allowance = '';
            $this->join_date = Carbon::today()->format('Y-m-d');
            $this->is_active = true;
        }
    }

    public function save(): void
    {
        $this->validate([
            'employee_code' => 'required|max:30|unique:employees,employee_code,' . $this->employeeId,
            'name' => 'required|string|min:2|max:100',
            'phone' => 'nullable|string|max:25',
            'position' => 'required|string|max:50',
            'employment_status' => 'required|in:tetap,kontrak,harian,part_time',
            'base_salary' => 'required|numeric|min:0',
            'daily_allowance' => 'nullable|numeric|min:0',
            'join_date' => 'nullable|date',
            'bank_name' => 'nullable|string|max:50',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_account_holder' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $data = [
            'employee_code' => $this->employee_code,
            'user_id' => $this->user_id ?: null,
            'name' => $this->name,
            'phone' => $this->phone ?: null,
            'position' => $this->position,
            'employment_status' => $this->employment_status,
            'base_salary' => (float) ($this->base_salary ?: 0),
            'daily_allowance' => (float) ($this->daily_allowance ?: 0),
            'join_date' => $this->join_date ?: null,
            'bank_name' => $this->bank_name ?: null,
            'bank_account_number' => $this->bank_account_number ?: null,
            'bank_account_holder' => $this->bank_account_holder ?: null,
            'address' => $this->address ?: null,
            'notes' => $this->notes ?: null,
            'is_active' => $this->is_active,
        ];

        if ($this->isEditing) {
            $employee = Employee::findOrFail($this->employeeId);
            $employee->update($data);
            session()->flash('success', "Data karyawan '{$this->name}' berhasil diperbarui.");
        } else {
            Employee::create($data);
            session()->flash('success', "Karyawan baru '{$this->name}' berhasil ditambahkan.");
        }

        $this->redirectRoute('admin.employees.index', navigate: true);
    }

    public function render()
    {
        $availableUsers = User::where('is_active', true)->get(['id', 'name', 'email', 'role']);

        $userOptions = $availableUsers->map(function ($u) {
            return [
                'id' => $u->id,
                'name' => $u->name,
                'sublabel' => $u->email . ' • ' . strtoupper($u->role),
            ];
        });

        return view('livewire.admin.employee.employee-form', [
            'userOptions' => $userOptions,
        ]);
    }
}
