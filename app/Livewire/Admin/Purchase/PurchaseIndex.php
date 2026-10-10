<?php

namespace App\Livewire\Admin\Purchase;

use App\Models\Outlet;
use App\Models\OutletProduct;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\StockMutation;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Pembelian & Restock Barang - Syafa Frozen Food')]
class PurchaseIndex extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true)]
    public string $outletFilter = '';

    #[Url(history: true)]
    public string $supplierFilter = '';

    #[Url(history: true)]
    public string $paymentStatusFilter = ''; // '', 'paid', 'unpaid', 'partial', 'overdue'

    #[Url(history: true)]
    public string $period = 'this_month';
    public string $startDate = '';
    public string $endDate = '';

    // Detail Modal State
    public bool $showDetailModal = false;
    public ?Purchase $selectedPurchase = null;
    public float|int|string $additionalPayment = 0;

    // Delete Modal State
    public bool $showDeleteModal = false;
    public ?int $deletingPurchaseId = null;
    public string $deletingPurchaseInvoice = '';

    public function mount(): void
    {
        $this->applyPeriodPreset('this_month');
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

    public function updatedOutletFilter(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSupplierFilter(): void
    {
        $this->resetPage();
    }

    public function updatedPaymentStatusFilter(): void
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
            case 'yesterday':
                $this->startDate = $now->copy()->subDay()->format('Y-m-d');
                $this->endDate = $now->copy()->subDay()->format('Y-m-d');
                break;
            case 'last_7_days':
                $this->startDate = $now->copy()->subDays(6)->format('Y-m-d');
                $this->endDate = $now->format('Y-m-d');
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

    public function openDetail(int $id): void
    {
        $this->selectedPurchase = Purchase::with(['supplier', 'user', 'outlet', 'items.product'])->findOrFail($id);
        $this->additionalPayment = $this->selectedPurchase->remaining_amount;
        $this->showDetailModal = true;
    }

    public function closeDetail(): void
    {
        $this->showDetailModal = false;
        $this->selectedPurchase = null;
        $this->additionalPayment = 0;
    }

    public function recordPayment(): void
    {
        if (!$this->selectedPurchase) return;

        $this->validate([
            'additionalPayment' => 'required|numeric|min:1',
        ]);

        $payment = (float) $this->additionalPayment;
        $remaining = $this->selectedPurchase->remaining_amount;

        if ($payment > $remaining) {
            $payment = $remaining;
        }

        $newPaidAmount = (float) $this->selectedPurchase->paid_amount + $payment;
        $newStatus = $newPaidAmount >= (float) $this->selectedPurchase->total_amount ? 'paid' : 'partial';

        $this->selectedPurchase->update([
            'paid_amount' => $newPaidAmount,
            'payment_status' => $newStatus,
        ]);

        $msg = "Pembayaran sebesar Rp " . number_format($payment, 0, ',', '.') . " berhasil dicatat.";
        session()->flash('success', $msg);
        $this->dispatch('toast', ['message' => $msg, 'type' => 'success']);
        $this->selectedPurchase->refresh();
        $this->additionalPayment = $this->selectedPurchase->remaining_amount;
    }

    public function confirmDelete(int $id): void
    {
        $purchase = Purchase::with('items')->findOrFail($id);
        $this->deletingPurchaseId = $purchase->id;
        $this->deletingPurchaseInvoice = $purchase->invoice_number;
        $this->showDeleteModal = true;
    }

    public function deletePurchase(): void
    {
        if (!$this->deletingPurchaseId) return;

        DB::transaction(function () {
            $purchase = Purchase::with('items.product')->findOrFail($this->deletingPurchaseId);
            $targetOutletId = $purchase->outlet_id;
            $targetOutlet = $targetOutletId ? Outlet::find($targetOutletId) : null;
            $isMainOutlet = $targetOutlet ? (bool) $targetOutlet->is_main : true;

            // Rollback stock for each item
            foreach ($purchase->items as $item) {
                if ($item->product) {
                    $product = Product::lockForUpdate()->find($item->product_id);
                    if ($product) {
                        $oldStock = $product->stock;
                        $newStock = $oldStock;

                        // Rollback per-outlet stock
                        if ($targetOutletId) {
                            $outletProduct = OutletProduct::where('outlet_id', $targetOutletId)
                                ->where('product_id', $product->id)
                                ->lockForUpdate()
                                ->first();

                            if ($outletProduct) {
                                $opOld = $outletProduct->stock;
                                $opNew = max(0, $opOld - $item->quantity);
                                $outletProduct->update(['stock' => $opNew]);
                            }
                        }

                        // Rollback master stock if main branch or unassigned
                        if ($isMainOutlet || !$targetOutletId) {
                            $newStock = max(0, $oldStock - $item->quantity);
                            $product->update(['stock' => $newStock]);
                        }

                        StockMutation::create([
                            'product_id' => $product->id,
                            'user_id' => Auth::id() ?? 1,
                            'outlet_id' => $targetOutletId,
                            'supplier_id' => $purchase->supplier_id,
                            'purchase_id' => null,
                            'type' => 'out',
                            'quantity' => $item->quantity,
                            'stock_before' => $oldStock,
                            'stock_after' => $newStock,
                            'reference' => 'BATAL-' . $purchase->invoice_number,
                            'description' => "Pembatalan faktur restock {$purchase->invoice_number}" . ($targetOutlet ? " (Cabang: {$targetOutlet->name})" : ''),
                        ]);
                    }
                }
            }

            $purchase->delete();
        });

        $msg = "Faktur pembelian {$this->deletingPurchaseInvoice} berhasil dibatalkan dan stok telah di-rollback.";
        session()->flash('success', $msg);
        $this->dispatch('toast', ['message' => $msg, 'type' => 'success']);
        $this->showDeleteModal = false;
        $this->reset(['deletingPurchaseId', 'deletingPurchaseInvoice']);
    }

    public function render()
    {
        $suppliers = Supplier::active()->orderBy('name')->get();
        $outlets = Outlet::active()->orderBy('is_main', 'desc')->orderBy('name')->get();

        // Financial KPIs for Purchases
        $kpiQuery = Purchase::query()
            ->when($this->startDate, function ($q) {
                $q->whereDate('purchase_date', '>=', $this->startDate);
            })
            ->when($this->endDate, function ($q) {
                $q->whereDate('purchase_date', '<=', $this->endDate);
            })
            ->when($this->supplierFilter, function ($q) {
                $q->where('supplier_id', $this->supplierFilter);
            })
            ->when($this->outletFilter, function ($q) {
                $q->where('outlet_id', $this->outletFilter);
            });

        $totalPurchasesAmount = (clone $kpiQuery)->sum('total_amount');
        $totalPurchasesCount = (clone $kpiQuery)->count();
        $totalPaidAmount = (clone $kpiQuery)->sum('paid_amount');
        $totalTempoRemaining = (clone $kpiQuery)->whereIn('payment_status', ['unpaid', 'partial'])->get()->sum('remaining_amount');
        $totalTempoCount = (clone $kpiQuery)->whereIn('payment_status', ['unpaid', 'partial'])->count();

        // Purchases Table List
        $purchases = Purchase::query()
            ->with(['supplier', 'user', 'outlet', 'items'])
            ->when($this->search, function ($q) {
                $term = '%' . $this->search . '%';
                $q->where(function ($sub) use ($term) {
                    $sub->where('invoice_number', 'like', $term)
                        ->orWhere('supplier_invoice_number', 'like', $term)
                        ->orWhereHas('supplier', function ($sup) use ($term) {
                            $sup->where('name', 'like', $term);
                        });
                });
            })
            ->when($this->supplierFilter, function ($q) {
                $q->where('supplier_id', $this->supplierFilter);
            })
            ->when($this->outletFilter, function ($q) {
                $q->where('outlet_id', $this->outletFilter);
            })
            ->when($this->paymentStatusFilter, function ($q) {
                if ($this->paymentStatusFilter === 'overdue') {
                    $q->whereIn('payment_status', ['unpaid', 'partial'])
                        ->whereDate('due_date', '<', Carbon::today());
                } else {
                    $q->where('payment_status', $this->paymentStatusFilter);
                }
            })
            ->when($this->startDate, function ($q) {
                $q->whereDate('purchase_date', '>=', $this->startDate);
            })
            ->when($this->endDate, function ($q) {
                $q->whereDate('purchase_date', '<=', $this->endDate);
            })
            ->latest('purchase_date')
            ->latest('id')
            ->paginate(10);

        return view('livewire.admin.purchase.purchase-index', [
            'purchases' => $purchases,
            'suppliers' => $suppliers,
            'outlets' => $outlets,
            'totalPurchasesAmount' => $totalPurchasesAmount,
            'totalPurchasesCount' => $totalPurchasesCount,
            'totalPaidAmount' => $totalPaidAmount,
            'totalTempoRemaining' => $totalTempoRemaining,
            'totalTempoCount' => $totalTempoCount,
        ]);
    }
}
