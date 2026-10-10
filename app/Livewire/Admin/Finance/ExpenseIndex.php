<?php

namespace App\Livewire\Admin\Finance;

use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Buku Pengeluaran Operasional (OPEX) - Syafa Frozen Food')]
class ExpenseIndex extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true)]
    public string $period = 'this_month'; // today, this_week, this_month, last_month, this_year, custom

    #[Url(history: true)]
    public string $startDate = '';

    #[Url(history: true)]
    public string $endDate = '';

    #[Url(history: true)]
    public string $categoryFilter = '';

    // Modal Form State (Tambah & Edit)
    public bool $showFormModal = false;
    public ?int $editingExpenseId = null;
    public string $expenseCategory = 'electricity_utility';
    public string $expenseTitle = '';
    public float|int|string $expenseAmount = '';
    public string $expenseDate = '';
    public string $expensePaymentMethod = 'cash';
    public string $expenseNotes = '';

    // Modal Delete State
    public bool $showDeleteModal = false;
    public ?int $deletingExpenseId = null;
    public string $deletingExpenseTitle = '';

    public function mount(): void
    {
        $this->applyPeriodPreset($this->period);
        $this->expenseDate = Carbon::today()->format('Y-m-d');
    }

    public function setPeriod(string $preset): void
    {
        $this->period = $preset;
        $this->applyPeriodPreset($preset);
        $this->resetPage();
    }

    public function updatedStartDate(): void
    {
        $this->period = 'custom';
        $this->resetPage();
    }

    public function updatedEndDate(): void
    {
        $this->period = 'custom';
        $this->resetPage();
    }

    public function updatedCategoryFilter(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    private function applyPeriodPreset(string $preset): void
    {
        $now = Carbon::now();

        switch ($preset) {
            case 'today':
                $this->startDate = $now->format('Y-m-d');
                $this->endDate = $now->format('Y-m-d');
                break;
            case 'this_week':
                $this->startDate = $now->copy()->startOfWeek()->format('Y-m-d');
                $this->endDate = $now->copy()->endOfWeek()->format('Y-m-d');
                break;
            case 'this_month':
                $this->startDate = $now->copy()->startOfMonth()->format('Y-m-d');
                $this->endDate = $now->copy()->endOfMonth()->format('Y-m-d');
                break;
            case 'last_month':
                $this->startDate = $now->copy()->subMonth()->startOfMonth()->format('Y-m-d');
                $this->endDate = $now->copy()->subMonth()->endOfMonth()->format('Y-m-d');
                break;
            case 'this_year':
                $this->startDate = $now->copy()->startOfYear()->format('Y-m-d');
                $this->endDate = $now->copy()->endOfYear()->format('Y-m-d');
                break;
            case 'custom':
                break;
        }
    }

    // ================= FORM OPERATIONS =================
    public function openCreateModal(): void
    {
        $this->reset(['editingExpenseId', 'expenseTitle', 'expenseAmount', 'expenseNotes']);
        $this->expenseCategory = 'electricity_utility';
        $this->expensePaymentMethod = 'cash';
        $this->expenseDate = Carbon::today()->format('Y-m-d');
        $this->resetValidation();
        $this->showFormModal = true;
    }

    public function openEditModal(int $id): void
    {
        $expense = Expense::with('payroll')->findOrFail($id);

        if ($expense->payroll) {
            $this->redirect(route('admin.employees.payroll.edit', [
                'employeeId' => $expense->payroll->employee_id,
                'payrollId' => $expense->payroll->id,
            ]), navigate: true);
            return;
        }

        $this->editingExpenseId = $expense->id;
        $this->expenseCategory = $expense->category;
        $this->expenseTitle = $expense->title;
        $this->expenseAmount = (int) $expense->amount;
        $this->expenseDate = $expense->expense_date->format('Y-m-d');
        $this->expensePaymentMethod = $expense->payment_method;
        $this->expenseNotes = $expense->notes ?? '';
        $this->resetValidation();
        $this->showFormModal = true;
    }

    public function closeFormModal(): void
    {
        $this->showFormModal = false;
        $this->reset(['editingExpenseId', 'expenseTitle', 'expenseAmount', 'expenseNotes']);
    }

    public function saveExpense(): void
    {
        $this->validate([
            'expenseCategory' => 'required|in:electricity_utility,salary_labor,packaging_supplies,maintenance_repair,rent_premises,other_operational',
            'expenseTitle' => 'required|string|max:150',
            'expenseAmount' => 'required|numeric|min:1',
            'expenseDate' => 'required|date',
            'expensePaymentMethod' => 'required|in:cash,transfer,qris',
            'expenseNotes' => 'nullable|string|max:500',
        ]);

        if ($this->editingExpenseId) {
            $expense = Expense::findOrFail($this->editingExpenseId);
            $expense->update([
                'category' => $this->expenseCategory,
                'title' => $this->expenseTitle,
                'amount' => $this->expenseAmount,
                'expense_date' => $this->expenseDate,
                'payment_method' => $this->expensePaymentMethod,
                'notes' => $this->expenseNotes ?: null,
            ]);

            $msg = "Pengeluaran '{$this->expenseTitle}' berhasil diperbarui.";
            session()->flash('success', $msg);
            $this->dispatch('toast', ['message' => $msg, 'type' => 'success']);
        } else {
            Expense::create([
                'expense_number' => Expense::generateExpenseNumber(),
                'user_id' => Auth::id(),
                'category' => $this->expenseCategory,
                'title' => $this->expenseTitle,
                'amount' => $this->expenseAmount,
                'expense_date' => $this->expenseDate,
                'payment_method' => $this->expensePaymentMethod,
                'notes' => $this->expenseNotes ?: null,
            ]);

            $msg = "Pengeluaran operasional '{$this->expenseTitle}' sebesar Rp " . number_format((float)$this->expenseAmount, 0, ',', '.') . " berhasil dicatat.";
            session()->flash('success', $msg);
            $this->dispatch('toast', ['message' => $msg, 'type' => 'success']);
        }

        $this->closeFormModal();
    }

    public function confirmDelete(int $id): void
    {
        $expense = Expense::with('payroll')->findOrFail($id);

        if ($expense->payroll) {
            $err = "Pengeluaran gaji karyawan terintegrasi dengan modul penggajian. Pembatalan/penghapusan hanya dapat dilakukan 1 pintu dari menu Detail Karyawan.";
            session()->flash('error', $err);
            $this->dispatch('toast', ['message' => $err, 'type' => 'error']);
            return;
        }

        $this->deletingExpenseId = $expense->id;
        $this->deletingExpenseTitle = $expense->title;
        $this->showDeleteModal = true;
    }

    public function deleteExpense(): void
    {
        if (!$this->deletingExpenseId) return;

        $expense = Expense::with('payroll')->findOrFail($this->deletingExpenseId);

        if ($expense->payroll) {
            $err = "Beban gaji karyawan tidak dapat dihapus dari Buku OPEX. Silakan batalkan penggajian dari menu Detail Karyawan.";
            session()->flash('error', $err);
            $this->dispatch('toast', ['message' => $err, 'type' => 'error']);
            $this->showDeleteModal = false;
            $this->deletingExpenseId = null;
            return;
        }

        $title = $expense->title;
        $expense->delete();

        $msg = "Catatan pengeluaran '{$title}' berhasil dihapus.";
        session()->flash('success', $msg);
        $this->dispatch('toast', ['message' => $msg, 'type' => 'success']);
        $this->showDeleteModal = false;
        $this->deletingExpenseId = null;
    }

    public function render()
    {
        // 1. Calculate KPI Metrics for the filtered period
        $baseMetricsQuery = Expense::query()
            ->when($this->startDate, fn($q) => $q->whereDate('expense_date', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('expense_date', '<=', $this->endDate));

        $totalFilteredExpenses = (clone $baseMetricsQuery)->sum('amount');
        $electricityExpenses = (clone $baseMetricsQuery)->where('category', 'electricity_utility')->sum('amount');
        $salaryExpenses = (clone $baseMetricsQuery)->where('category', 'salary_labor')->sum('amount');
        $maintenanceExpenses = (clone $baseMetricsQuery)->where('category', 'maintenance_repair')->sum('amount');

        // 2. Query Expense Records for Table
        $expensesQuery = Expense::query()
            ->with(['user', 'payroll.employee'])
            ->when($this->startDate, fn($q) => $q->whereDate('expense_date', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('expense_date', '<=', $this->endDate))
            ->when($this->categoryFilter, fn($q) => $q->where('category', $this->categoryFilter))
            ->when($this->search, function ($q) {
                $term = '%' . $this->search . '%';
                $q->where(function ($sub) use ($term) {
                    $sub->where('title', 'like', $term)
                        ->orWhere('expense_number', 'like', $term)
                        ->orWhere('notes', 'like', $term);
                });
            })
            ->latest('expense_date')
            ->latest('id');

        $expenses = $expensesQuery->paginate(12);

        return view('livewire.admin.finance.expense-index', [
            'expenses' => $expenses,
            'totalFilteredExpenses' => $totalFilteredExpenses,
            'electricityExpenses' => $electricityExpenses,
            'salaryExpenses' => $salaryExpenses,
            'maintenanceExpenses' => $maintenanceExpenses,
        ]);
    }
}
