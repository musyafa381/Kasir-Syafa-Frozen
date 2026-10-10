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
#[Title('Detail Karyawan & Riwayat Payroll - Syafa Frozen Food')]
class EmployeeDetail extends Component
{
    public Employee $employee;
    public string $activeTab = 'payroll'; // payroll, loans

    // ─── Modal Create Loan (Kasbon Baru) ─────────────────────
    public bool $showLoanModal = false;
    public float|int|string $loan_amount = '';
    public string $loan_date = '';
    public string $loan_reason = '';
    public string $loan_notes = '';

    // ─── Modal Edit Loan (Edit Kasbon) ───────────────────────
    public bool $showEditLoanModal = false;
    public ?int $editingLoanId = null;
    public float|int|string $edit_loan_amount = '';
    public string $edit_loan_date = '';
    public string $edit_loan_reason = '';
    public string $edit_loan_notes = '';
    public float $edit_loan_paid = 0;

    // ─── Modal Delete Loan ───────────────────────────────────
    public bool $showDeleteLoanModal = false;
    public ?int $deletingLoanId = null;
    public string $deletingLoanNumber = '';

    // ─── Modal Pay Loan Cash State ──────────────────────────
    public bool $showPayLoanModal = false;
    public ?int $payingLoanId = null;
    public float|int|string $pay_loan_amount = '';
    public string $pay_loan_date = '';
    public string $pay_loan_notes = '';

    // ─── Modal Delete Payroll State ─────────────────────────
    public bool $showDeletePayrollModal = false;
    public ?int $deletingPayrollId = null;
    public string $deletingPayrollNumber = '';

    public function mount(int $id): void
    {
        $this->employee = Employee::with(['user', 'activeLoans'])->findOrFail($id);
        $this->loan_date = Carbon::today()->format('Y-m-d');
        $this->pay_loan_date = Carbon::today()->format('Y-m-d');
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    // ─── PAYROLL OPERATIONS ─────────────────────────────────
    public function confirmDeletePayroll(int $id): void
    {
        $payroll = Payroll::findOrFail($id);
        $this->deletingPayrollId = $payroll->id;
        $this->deletingPayrollNumber = $payroll->payroll_number;
        $this->showDeletePayrollModal = true;
    }

    public function deletePayroll(): void
    {
        if (!$this->deletingPayrollId) return;

        DB::transaction(function () {
            $payroll = Payroll::with(['loanPayments.loan', 'expense'])->findOrFail($this->deletingPayrollId);

            // Revert loan payments
            foreach ($payroll->loanPayments as $payment) {
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

            // Delete linked OPEX expense
            if ($payroll->expense) {
                $payroll->expense->delete();
            }

            $payroll->delete();
        });

        $this->employee->refresh();
        $msg = "Catatan penggajian {$this->deletingPayrollNumber} dan beban OPEX terkait berhasil dibatalkan.";
        session()->flash('success', $msg);
        $this->dispatch('toast', ['message' => $msg, 'type' => 'success']);
        $this->showDeletePayrollModal = false;
        $this->deletingPayrollId = null;
    }

    // ─── LOAN (KASBON) OPERATIONS ───────────────────────────
    public function openLoanModal(): void
    {
        $this->loan_amount = '';
        $this->loan_date = Carbon::today()->format('Y-m-d');
        $this->loan_reason = '';
        $this->loan_notes = '';
        $this->resetValidation();
        $this->showLoanModal = true;
    }

    public function saveLoan(): void
    {
        $this->validate([
            'loan_amount' => 'required|numeric|min:1000',
            'loan_date' => 'required|date',
            'loan_reason' => 'required|string|max:255',
            'loan_notes' => 'nullable|string|max:500',
        ]);

        EmployeeLoan::create([
            'employee_id' => $this->employee->id,
            'loan_number' => EmployeeLoan::generateLoanNumber(),
            'loan_date' => $this->loan_date,
            'amount' => (float) $this->loan_amount,
            'paid_amount' => 0,
            'remaining_amount' => (float) $this->loan_amount,
            'status' => 'unpaid',
            'reason' => $this->loan_reason,
            'created_by' => Auth::id(),
            'notes' => $this->loan_notes ?: null,
        ]);

        $this->employee->refresh();
        $msg = "Kasbon sebesar Rp " . number_format((float) $this->loan_amount, 0, ',', '.') . " untuk {$this->employee->name} berhasil dicatat.";
        session()->flash('success', $msg);
        $this->dispatch('toast', ['message' => $msg, 'type' => 'success']);
        $this->showLoanModal = false;
    }

    public function openEditLoanModal(int $loanId): void
    {
        $loan = EmployeeLoan::where('employee_id', $this->employee->id)->findOrFail($loanId);
        $this->editingLoanId = $loan->id;
        $this->edit_loan_amount = (int) $loan->amount;
        $this->edit_loan_paid = (float) $loan->paid_amount;
        $this->edit_loan_date = $loan->loan_date->format('Y-m-d');
        $this->edit_loan_reason = $loan->reason;
        $this->edit_loan_notes = $loan->notes ?? '';
        $this->resetValidation();
        $this->showEditLoanModal = true;
    }

    public function saveEditLoan(): void
    {
        $loan = EmployeeLoan::where('employee_id', $this->employee->id)->findOrFail($this->editingLoanId);

        $this->validate([
            'edit_loan_amount' => "required|numeric|min:{$loan->paid_amount}",
            'edit_loan_date' => 'required|date',
            'edit_loan_reason' => 'required|string|max:255',
            'edit_loan_notes' => 'nullable|string|max:500',
        ]);

        $newAmount = (float) $this->edit_loan_amount;
        $newRemaining = max(0, $newAmount - (float) $loan->paid_amount);
        $newStatus = $newRemaining <= 0 ? 'paid' : ($loan->paid_amount > 0 ? 'partial' : 'unpaid');

        $loan->update([
            'amount' => $newAmount,
            'remaining_amount' => $newRemaining,
            'status' => $newStatus,
            'loan_date' => $this->edit_loan_date,
            'reason' => $this->edit_loan_reason,
            'notes' => $this->edit_loan_notes ?: null,
        ]);

        $this->employee->refresh();
        $msg = "Data kasbon {$loan->loan_number} berhasil diperbarui.";
        session()->flash('success', $msg);
        $this->dispatch('toast', ['message' => $msg, 'type' => 'success']);
        $this->showEditLoanModal = false;
        $this->editingLoanId = null;
    }

    public function confirmDeleteLoan(int $loanId): void
    {
        $loan = EmployeeLoan::where('employee_id', $this->employee->id)->findOrFail($loanId);
        $this->deletingLoanId = $loan->id;
        $this->deletingLoanNumber = $loan->loan_number;
        $this->showDeleteLoanModal = true;
    }

    public function deleteLoan(): void
    {
        if (!$this->deletingLoanId) return;

        $loan = EmployeeLoan::where('employee_id', $this->employee->id)->findOrFail($this->deletingLoanId);

        if ($loan->paid_amount > 0) {
            $err = "Kasbon {$loan->loan_number} tidak dapat dihapus karena sudah memiliki riwayat cicilan/pembayaran.";
            session()->flash('error', $err);
            $this->dispatch('toast', ['message' => $err, 'type' => 'error']);
            $this->showDeleteLoanModal = false;
            return;
        }

        $loan->delete();
        $this->employee->refresh();
        $msg = "Data kasbon {$this->deletingLoanNumber} berhasil dihapus.";
        session()->flash('success', $msg);
        $this->dispatch('toast', ['message' => $msg, 'type' => 'success']);
        $this->showDeleteLoanModal = false;
        $this->deletingLoanId = null;
    }

    public function openPayLoanModal(int $loanId): void
    {
        $loan = EmployeeLoan::findOrFail($loanId);
        $this->payingLoanId = $loan->id;
        $this->pay_loan_amount = (int) $loan->remaining_amount;
        $this->pay_loan_date = Carbon::today()->format('Y-m-d');
        $this->pay_loan_notes = 'Pelunasan tunai langsung oleh karyawan';
        $this->resetValidation();
        $this->showPayLoanModal = true;
    }

    public function savePayLoan(): void
    {
        $loan = EmployeeLoan::findOrFail($this->payingLoanId);

        $this->validate([
            'pay_loan_amount' => "required|numeric|min:1000|max:{$loan->remaining_amount}",
            'pay_loan_date' => 'required|date',
            'pay_loan_notes' => 'nullable|string|max:255',
        ]);

        $amount = (float) $this->pay_loan_amount;

        DB::transaction(function () use ($loan, $amount) {
            $newPaid = (float) $loan->paid_amount + $amount;
            $newRemaining = max(0, (float) $loan->amount - $newPaid);
            $newStatus = $newRemaining <= 0 ? 'paid' : 'partial';

            $loan->update([
                'paid_amount' => $newPaid,
                'remaining_amount' => $newRemaining,
                'status' => $newStatus,
            ]);

            EmployeeLoanPayment::create([
                'employee_loan_id' => $loan->id,
                'payroll_id' => null,
                'payment_date' => $this->pay_loan_date,
                'amount' => $amount,
                'payment_type' => 'cash_payment',
                'notes' => $this->pay_loan_notes ?: 'Pembayaran tunai',
            ]);
        });

        $this->employee->refresh();
        $msg = "Pembayaran kasbon tunai sebesar Rp " . number_format($amount, 0, ',', '.') . " berhasil dicatat.";
        session()->flash('success', $msg);
        $this->dispatch('toast', ['message' => $msg, 'type' => 'success']);
        $this->showPayLoanModal = false;
        $this->payingLoanId = null;
    }

    public function render()
    {
        $payrolls = Payroll::where('employee_id', $this->employee->id)
            ->with(['expense', 'creator', 'loanPayments'])
            ->latest('payment_date')
            ->latest('id')
            ->get();

        $loans = EmployeeLoan::where('employee_id', $this->employee->id)
            ->with(['payments', 'creator'])
            ->latest('loan_date')
            ->latest('id')
            ->get();

        return view('livewire.admin.employee.employee-detail', [
            'payrolls' => $payrolls,
            'loans' => $loans,
        ]);
    }
}
