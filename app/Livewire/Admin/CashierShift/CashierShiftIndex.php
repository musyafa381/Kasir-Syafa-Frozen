<?php

namespace App\Livewire\Admin\CashierShift;

use App\Models\CashierShift;
use App\Models\Outlet;
use App\Models\ReceiptSetting;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Shift & Kas Kasir - Syafa Frozen Food')]
class CashierShiftIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = 'all'; // all, open, closed
    public ?int $outletFilter = null;
    public ?int $userFilter = null;
    public string $startDate = '';
    public string $endDate = '';

    // Modal Detail & Rekap Shift
    public bool $showDetailModal = false;
    public ?int $selectedShiftId = null;

    public function mount(): void
    {
        $this->startDate = Carbon::today()->subDays(30)->format('Y-m-d');
        $this->endDate = Carbon::today()->format('Y-m-d');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingOutletFilter(): void
    {
        $this->resetPage();
    }

    public function updatingUserFilter(): void
    {
        $this->resetPage();
    }

    public function openDetailModal(int $shiftId): void
    {
        $this->selectedShiftId = $shiftId;
        $this->showDetailModal = true;
    }

    public function closeDetailModal(): void
    {
        $this->showDetailModal = false;
        $this->selectedShiftId = null;
    }

    public function render()
    {
        $query = CashierShift::query()
            ->with(['user', 'outlet', 'transactions'])
            ->when($this->statusFilter !== 'all', fn($q) => $q->where('status', $this->statusFilter))
            ->when($this->outletFilter, fn($q) => $q->where('outlet_id', $this->outletFilter))
            ->when($this->userFilter, fn($q) => $q->where('user_id', $this->userFilter))
            ->when($this->startDate, fn($q) => $q->whereDate('opened_at', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('opened_at', '<=', $this->endDate))
            ->when($this->search, function ($q) {
                $q->whereHas('user', fn($u) => $u->where('name', 'like', '%' . $this->search . '%'))
                  ->orWhereHas('outlet', fn($o) => $o->where('name', 'like', '%' . $this->search . '%'));
            })
            ->latest('opened_at');

        $shifts = $query->paginate(12);

        // Summary KPI Metrics
        $kpiQuery = CashierShift::query()
            ->when($this->startDate, fn($q) => $q->whereDate('opened_at', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('opened_at', '<=', $this->endDate));

        $totalShifts = (clone $kpiQuery)->count();
        $totalCashSales = (clone $kpiQuery)->sum('total_cash_sales');
        $totalNonCashSales = (clone $kpiQuery)->sum('total_qris_sales') + (clone $kpiQuery)->sum('total_transfer_sales');
        $totalOverShort = (clone $kpiQuery)->whereNotNull('cash_difference')->sum('cash_difference');

        $outlets = Outlet::where('is_active', true)->orderBy('name')->get();
        $cashiers = User::orderBy('name')->get();
        $selectedShift = $this->selectedShiftId ? CashierShift::with(['user', 'outlet', 'transactions.items'])->find($this->selectedShiftId) : null;
        $receiptSetting = ReceiptSetting::getSettings();

        return view('livewire.admin.cashier-shift.cashier-shift-index', [
            'shifts' => $shifts,
            'totalShifts' => $totalShifts,
            'totalCashSales' => $totalCashSales,
            'totalNonCashSales' => $totalNonCashSales,
            'totalOverShort' => $totalOverShort,
            'outlets' => $outlets,
            'cashiers' => $cashiers,
            'selectedShift' => $selectedShift,
            'receiptSetting' => $receiptSetting,
        ]);
    }
}
