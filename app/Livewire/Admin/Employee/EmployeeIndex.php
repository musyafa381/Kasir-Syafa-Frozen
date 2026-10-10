<?php

namespace App\Livewire\Admin\Employee;

use App\Models\Employee;
use App\Models\EmployeeLoan;
use App\Models\Payroll;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Kelola Data Karyawan & Staf - Syafa Frozen Food')]
class EmployeeIndex extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true)]
    public string $statusFilter = 'all'; // all, active, inactive

    #[Url(history: true)]
    public string $positionFilter = '';

    // Modal Delete
    public bool $showDeleteModal = false;
    public ?int $deletingEmployeeId = null;
    public string $deletingEmployeeName = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedPositionFilter(): void
    {
        $this->resetPage();
    }

    public function confirmDelete(int $id): void
    {
        $employee = Employee::findOrFail($id);
        $this->deletingEmployeeId = $employee->id;
        $this->deletingEmployeeName = $employee->name;
        $this->showDeleteModal = true;
    }

    public function deleteEmployee(): void
    {
        if (!$this->deletingEmployeeId) return;

        $employee = Employee::findOrFail($this->deletingEmployeeId);
        $name = $employee->name;
        $employee->delete();

        $msg = "Data karyawan '{$name}' berhasil dihapus.";
        session()->flash('success', $msg);
        $this->dispatch('toast', ['message' => $msg, 'type' => 'success']);
        $this->showDeleteModal = false;
        $this->deletingEmployeeId = null;
    }

    public function render()
    {
        // 1. KPI Metrics
        $totalActiveEmployees = Employee::where('is_active', true)->count();
        $totalMonthlyPayroll = (float) Payroll::where('payment_status', 'paid')
            ->whereYear('payment_date', Carbon::now()->year)
            ->whereMonth('payment_date', Carbon::now()->month)
            ->sum('net_salary');
        $totalOutstandingLoans = (float) EmployeeLoan::whereIn('status', ['unpaid', 'partial'])->sum('remaining_amount');

        // 2. Query Employees
        $employees = Employee::query()
            ->with(['activeLoans', 'user'])
            ->withCount(['payrolls', 'loans'])
            ->when($this->search, function ($q) {
                $term = '%' . $this->search . '%';
                $q->where(function ($sub) use ($term) {
                    $sub->where('name', 'like', $term)
                        ->orWhere('employee_code', 'like', $term)
                        ->orWhere('phone', 'like', $term)
                        ->orWhere('position', 'like', $term);
                });
            })
            ->when($this->statusFilter === 'active', fn($q) => $q->where('is_active', true))
            ->when($this->statusFilter === 'inactive', fn($q) => $q->where('is_active', false))
            ->when($this->positionFilter, fn($q) => $q->where('position', $this->positionFilter))
            ->latest('id')
            ->paginate(10);

        // Get unique positions for filter
        $positions = Employee::select('position')->distinct()->pluck('position');

        return view('livewire.admin.employee.employee-index', [
            'employees' => $employees,
            'totalActiveEmployees' => $totalActiveEmployees,
            'totalMonthlyPayroll' => $totalMonthlyPayroll,
            'totalOutstandingLoans' => $totalOutstandingLoans,
            'positions' => $positions,
        ]);
    }
}
