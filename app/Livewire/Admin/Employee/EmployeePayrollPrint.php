<?php

namespace App\Livewire\Admin\Employee;

use App\Models\Employee;
use App\Models\Payroll;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Cetak Slip Gaji - Syafa Frozen Food')]
class EmployeePayrollPrint extends Component
{
    public Employee $employee;
    public Payroll $payroll;

    public function mount(int $employeeId, int $payrollId): void
    {
        $this->employee = Employee::findOrFail($employeeId);
        $this->payroll = Payroll::with(['employee', 'creator', 'loanPayments'])
            ->where('employee_id', $employeeId)
            ->findOrFail($payrollId);
    }

    public function render()
    {
        return view('livewire.admin.employee.employee-payroll-print');
    }
}
