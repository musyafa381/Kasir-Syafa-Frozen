<?php

namespace App\Livewire\Admin\Report;

use App\Models\Purchase;
use App\Models\Supplier;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Buku Hutang Supplier (Accounts Payable) - Syafa Frozen Food')]
class AccountsPayableReport extends Component
{
    use WithPagination;
    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true)]
    public string $statusTab = 'unpaid'; // unpaid, overdue, due_soon, paid, all

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusTab(): void
    {
        $this->resetPage();
    }

    // Pay installment modal state
    public bool $showPaymentModal = false;
    public ?Purchase $selectedPurchase = null;
    public float|int|string $paymentAmount = 0;

    public function openPaymentModal(int $purchaseId): void
    {
        $this->selectedPurchase = Purchase::with(['supplier', 'items'])->findOrFail($purchaseId);
        $this->paymentAmount = $this->selectedPurchase->remaining_amount;
        $this->showPaymentModal = true;
    }

    public function closePaymentModal(): void
    {
        $this->showPaymentModal = false;
        $this->selectedPurchase = null;
        $this->paymentAmount = 0;
    }

    public function recordPayment(): void
    {
        if (!$this->selectedPurchase) return;

        $this->validate([
            'paymentAmount' => 'required|numeric|min:1',
        ]);

        $amount = (float) $this->paymentAmount;
        $remaining = $this->selectedPurchase->remaining_amount;

        if ($amount > $remaining) {
            $amount = $remaining;
        }

        $newPaidAmount = (float) $this->selectedPurchase->paid_amount + $amount;
        $newStatus = $newPaidAmount >= (float) $this->selectedPurchase->total_amount ? 'paid' : 'partial';

        $this->selectedPurchase->update([
            'paid_amount' => $newPaidAmount,
            'payment_status' => $newStatus,
        ]);

        $msg = "Pembayaran hutang faktur {$this->selectedPurchase->invoice_number} sebesar Rp " . number_format($amount, 0, ',', '.') . " berhasil dicatat.";
        session()->flash('success', $msg);
        $this->dispatch('toast', ['message' => $msg, 'type' => 'success']);
        $this->closePaymentModal();
    }

    public function render()
    {
        $today = Carbon::today();
        $in7Days = Carbon::today()->addDays(7);

        // 1. Calculate KPI Metrics across all purchases
        $allPurchases = Purchase::with('supplier')->get();

        $totalRemainingDebt = 0;
        $totalOverdueDebt = 0;
        $totalDueSoonDebt = 0;
        $suppliersWithActiveDebt = [];

        foreach ($allPurchases as $po) {
            $rem = $po->remaining_amount;
            if ($rem > 0) {
                $totalRemainingDebt += $rem;
                $suppliersWithActiveDebt[$po->supplier_id] = true;

                if ($po->due_date && $po->due_date->lt($today)) {
                    $totalOverdueDebt += $rem;
                } elseif ($po->due_date && $po->due_date->lte($in7Days)) {
                    $totalDueSoonDebt += $rem;
                }
            }
        }

        $totalSuppliersWithDebtCount = count($suppliersWithActiveDebt);

        // 2. Query Purchases List based on Status Filter & Search
        $purchasesQuery = Purchase::query()
            ->with(['supplier', 'items'])
            ->when($this->search, function ($q) {
                $term = '%' . $this->search . '%';
                $q->where(function ($sub) use ($term) {
                    $sub->where('invoice_number', 'like', $term)
                        ->orWhere('supplier_invoice_number', 'like', $term)
                        ->orWhereHas('supplier', function ($sup) use ($term) {
                            $sup->where('name', 'like', $term);
                        });
                });
            });

        switch ($this->statusTab) {
            case 'overdue':
                $purchasesQuery->whereIn('payment_status', ['unpaid', 'partial'])
                    ->whereDate('due_date', '<', $today);
                break;
            case 'due_soon':
                $purchasesQuery->whereIn('payment_status', ['unpaid', 'partial'])
                    ->whereDate('due_date', '>=', $today)
                    ->whereDate('due_date', '<=', $in7Days);
                break;
            case 'unpaid':
                $purchasesQuery->whereIn('payment_status', ['unpaid', 'partial']);
                break;
            case 'paid':
                $purchasesQuery->where('payment_status', 'paid');
                break;
            case 'all':
            default:
                break;
        }

        $purchases = $purchasesQuery->latest('due_date')->latest('id')->paginate(10);

        return view('livewire.admin.report.accounts-payable-report', [
            'purchases' => $purchases,
            'totalRemainingDebt' => $totalRemainingDebt,
            'totalOverdueDebt' => $totalOverdueDebt,
            'totalDueSoonDebt' => $totalDueSoonDebt,
            'totalSuppliersWithDebtCount' => $totalSuppliersWithDebtCount,
        ]);
    }
}
