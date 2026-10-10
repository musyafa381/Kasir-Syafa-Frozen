<?php

namespace App\Livewire\Admin\Employee;

use App\Models\Employee;
use App\Models\EmployeeLoan;
use App\Models\EmployeeLoanPayment;
use App\Models\Expense;
use App\Models\Payroll;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Proses Penggajian Karyawan - Syafa Frozen Food')]
class EmployeePayrollForm extends Component
{
    public Employee $employee;
    public ?Payroll $payroll = null;
    public bool $isEditing = false;

    // Form fields
    public int $period_month = 9;
    public int $period_year = 2026;
    public string $payment_date = '';
    public float|int|string $payroll_base_salary = 0;
    public float|int|string $allowance_amount = 0;
    public string $allowance_notes = '';
    public float|int|string $loan_deduction = 0;
    public float|int|string $other_deduction = 0;
    public string $deduction_notes = '';
    public string $payroll_payment_method = 'transfer';
    public string $payroll_notes = '';

    public function mount(int $employeeId, ?int $payrollId = null): void
    {
        $this->employee = Employee::with(['activeLoans'])->findOrFail($employeeId);

        if ($payrollId) {
            $this->isEditing = true;
            $this->payroll = Payroll::where('employee_id', $employeeId)->findOrFail($payrollId);

            $this->period_month = (int) $this->payroll->period_month;
            $this->period_year = (int) $this->payroll->period_year;
            $this->payment_date = $this->payroll->payment_date->format('Y-m-d');
            $this->payroll_base_salary = (int) $this->payroll->base_salary;
            $this->allowance_amount = (int) $this->payroll->allowance_amount;
            $this->allowance_notes = (string) ($this->payroll->allowance_notes ?? '');
            $this->loan_deduction = (int) $this->payroll->loan_deduction;
            $this->other_deduction = (int) $this->payroll->other_deduction;
            $this->deduction_notes = (string) ($this->payroll->deduction_notes ?? '');
            $this->payroll_payment_method = (string) $this->payroll->payment_method;
            $this->payroll_notes = (string) ($this->payroll->notes ?? '');
        } else {
            $this->isEditing = false;
            $this->period_month = Carbon::now()->month;
            $this->period_year = Carbon::now()->year;
            $this->payment_date = Carbon::today()->format('Y-m-d');
            $this->payroll_base_salary = (int) $this->employee->base_salary;
            $this->allowance_amount = 0;
            $this->allowance_notes = '';
            $this->loan_deduction = 0;
            $this->other_deduction = 0;
            $this->deduction_notes = '';
            $this->payroll_payment_method = $this->employee->bank_account_number ? 'transfer' : 'cash';
            $this->payroll_notes = '';
        }
    }

    public function getTotalIncomeProperty(): float
    {
        $base = (float) ($this->payroll_base_salary ?: 0);
        $allowance = (float) ($this->allowance_amount ?: 0);
        return $base + $allowance;
    }

    public function getTotalDeductionProperty(): float
    {
        $loanDed = (float) ($this->loan_deduction ?: 0);
        $otherDed = (float) ($this->other_deduction ?: 0);
        return $loanDed + $otherDed;
    }

    public function getCalculatedNetSalaryProperty(): float
    {
        return max(0, $this->total_income - $this->total_deduction);
    }

    public function getMaxAvailableLoanDeductionProperty(): float
    {
        if ($this->isEditing && $this->payroll) {
            // When editing, available loan is current remaining + previous loan deduction of this payroll
            return (float) $this->employee->total_remaining_loan + (float) $this->payroll->loan_deduction;
        }

        return (float) $this->employee->total_remaining_loan;
    }

    public function applyMaxLoanDeduction(): void
    {
        $this->loan_deduction = (int) $this->max_available_loan_deduction;
    }

    public function save()
    {
        $maxLoan = $this->max_available_loan_deduction;

        $this->validate([
            'period_month' => 'required|integer|between:1,12',
            'period_year' => 'required|integer|min:2020|max:2099',
            'payment_date' => 'required|date',
            'payroll_base_salary' => 'required|numeric|min:0',
            'allowance_amount' => 'nullable|numeric|min:0',
            'allowance_notes' => 'nullable|string|max:255',
            'loan_deduction' => "nullable|numeric|min:0|max:{$maxLoan}",
            'other_deduction' => 'nullable|numeric|min:0',
            'deduction_notes' => 'nullable|string|max:255',
            'payroll_payment_method' => 'required|in:cash,transfer',
            'payroll_notes' => 'nullable|string|max:500',
        ]);

        $base = (float) $this->payroll_base_salary;
        $allowance = (float) ($this->allowance_amount ?: 0);
        $loanDed = (float) ($this->loan_deduction ?: 0);
        $otherDed = (float) ($this->other_deduction ?: 0);
        $netSalary = max(0, ($base + $allowance) - ($loanDed + $otherDed));

        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $periodLabel = ($months[$this->period_month] ?? '') . ' ' . $this->period_year;

        DB::transaction(function () use ($base, $allowance, $loanDed, $otherDed, $netSalary, $periodLabel) {
            if ($this->isEditing && $this->payroll) {
                // 1. Revert previous loan payments for this payroll
                foreach ($this->payroll->loanPayments as $payment) {
                    $loan = $payment->loan;
                    if ($loan) {
                        $newPaid = max(0, (float) $loan->paid_amount - (float) $payment->amount);
                        $newRemaining = max(0, (float) $loan->amount - $newPaid);
                        $newStatus = $newPaid <= 0 ? 'unpaid' : 'partial';

                        $loan->update([
                            'paid_amount' => $newPaid,
                            'remaining_amount' => $newRemaining,
                            'status' => $newStatus,
                        ]);
                    }
                    $payment->delete();
                }

                // 2. Update linked OPEX expense
                if ($this->payroll->expense) {
                    $this->payroll->expense->update([
                        'title' => "Gaji {$this->employee->name} - {$periodLabel}",
                        'amount' => $netSalary,
                        'expense_date' => $this->payment_date,
                        'payment_method' => $this->payroll_payment_method,
                        'notes' => "Penggajian staf {$this->employee->name} ({$this->employee->employee_code}) periode {$periodLabel}. No Slip: {$this->payroll->payroll_number}",
                    ]);
                }

                // 3. Update Payroll
                $this->payroll->update([
                    'period_month' => $this->period_month,
                    'period_year' => $this->period_year,
                    'payment_date' => $this->payment_date,
                    'base_salary' => $base,
                    'allowance_amount' => $allowance,
                    'allowance_notes' => $this->allowance_notes ?: null,
                    'loan_deduction' => $loanDed,
                    'other_deduction' => $otherDed,
                    'deduction_notes' => $this->deduction_notes ?: null,
                    'net_salary' => $netSalary,
                    'payment_method' => $this->payroll_payment_method,
                    'notes' => $this->payroll_notes ?: null,
                ]);

                $payrollRecord = $this->payroll;
            } else {
                // Create Mode
                $payrollNumber = Payroll::generatePayrollNumber();

                // 1. Expense OPEX
                $expense = Expense::create([
                    'expense_number' => Expense::generateExpenseNumber(),
                    'user_id' => Auth::id(),
                    'category' => 'salary_labor',
                    'title' => "Gaji {$this->employee->name} - {$periodLabel}",
                    'amount' => $netSalary,
                    'expense_date' => $this->payment_date,
                    'payment_method' => $this->payroll_payment_method,
                    'notes' => "Penggajian staf {$this->employee->name} ({$this->employee->employee_code}) periode {$periodLabel}. No Slip: {$payrollNumber}",
                ]);

                // 2. Payroll
                $payrollRecord = Payroll::create([
                    'payroll_number' => $payrollNumber,
                    'employee_id' => $this->employee->id,
                    'expense_id' => $expense->id,
                    'period_month' => $this->period_month,
                    'period_year' => $this->period_year,
                    'payment_date' => $this->payment_date,
                    'base_salary' => $base,
                    'allowance_amount' => $allowance,
                    'allowance_notes' => $this->allowance_notes ?: null,
                    'loan_deduction' => $loanDed,
                    'other_deduction' => $otherDed,
                    'deduction_notes' => $this->deduction_notes ?: null,
                    'net_salary' => $netSalary,
                    'payment_method' => $this->payroll_payment_method,
                    'payment_status' => 'paid',
                    'notes' => $this->payroll_notes ?: null,
                    'created_by' => Auth::id(),
                ]);
            }

            // Apply loan deduction
            if ($loanDed > 0) {
                $deductionLeft = $loanDed;
                $activeLoans = EmployeeLoan::where('employee_id', $this->employee->id)
                    ->whereIn('status', ['unpaid', 'partial'])
                    ->orderBy('id', 'asc')
                    ->get();

                foreach ($activeLoans as $loan) {
                    if ($deductionLeft <= 0) break;

                    $deductFromThisLoan = min($deductionLeft, (float) $loan->remaining_amount);
                    $newPaid = (float) $loan->paid_amount + $deductFromThisLoan;
                    $newRemaining = max(0, (float) $loan->amount - $newPaid);
                    $newStatus = $newRemaining <= 0 ? 'paid' : 'partial';

                    $loan->update([
                        'paid_amount' => $newPaid,
                        'remaining_amount' => $newRemaining,
                        'status' => $newStatus,
                    ]);

                    EmployeeLoanPayment::create([
                        'employee_loan_id' => $loan->id,
                        'payroll_id' => $payrollRecord->id,
                        'payment_date' => $this->payment_date,
                        'amount' => $deductFromThisLoan,
                        'payment_type' => 'payroll_deduction',
                        'notes' => "Potongan gaji periode {$periodLabel} ({$payrollRecord->payroll_number})",
                    ]);

                    $deductionLeft -= $deductFromThisLoan;
                }
            }
        });

        session()->flash('success', $this->isEditing 
            ? "Slip gaji {$this->payroll->payroll_number} untuk {$this->employee->name} berhasil diperbarui!" 
            : "Penggajian untuk {$this->employee->name} berhasil diproses dan otomatis tercatat ke Beban OPEX!");

        return redirect()->route('admin.employees.show', $this->employee->id);
    }

    public function render()
    {
        return view('livewire.admin.employee.employee-payroll-form');
    }
}
