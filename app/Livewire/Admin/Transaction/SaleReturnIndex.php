<?php

namespace App\Livewire\Admin\Transaction;

use App\Models\Customer;
use App\Models\ReceiptSetting;
use App\Models\SaleReturn;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Daftar Retur Penjualan & Pengembalian Barang - Syafa Frozen Food')]
class SaleReturnIndex extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true)]
    public string $period = 'all'; // 'all', 'today', 'this_week', 'this_month', 'this_year', 'custom'

    #[Url(history: true)]
    public ?string $startDate = null;

    #[Url(history: true)]
    public ?string $endDate = null;

    #[Url(history: true)]
    public string $reasonFilter = 'all';

    #[Url(history: true)]
    public string $restockFilter = 'all'; // 'all', '1', '0'

    // Modal Detail Bukti Retur
    public bool $showDetailModal = false;
    public ?SaleReturn $selectedReturn = null;
    public string $waPhoneNumber = '';

    // Modal Hapus Retur
    public bool $showDeleteModal = false;
    public ?int $deletingReturnId = null;
    public ?SaleReturn $deletingReturn = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPeriod(): void
    {
        if ($this->period !== 'custom') {
            $this->startDate = null;
            $this->endDate = null;
        }
        $this->resetPage();
    }

    public function updatedReasonFilter(): void
    {
        $this->resetPage();
    }

    public function updatedRestockFilter(): void
    {
        $this->resetPage();
    }

    public function setPeriod(string $p): void
    {
        $this->period = $p;
        if ($p !== 'custom') {
            $this->startDate = null;
            $this->endDate = null;
        }
        $this->resetPage();
    }

    public function openDetail(int $id): void
    {
        $this->selectedReturn = SaleReturn::with(['transaction', 'user', 'customer', 'items.product'])->findOrFail($id);
        $this->waPhoneNumber = $this->selectedReturn->customer?->phone ?: '';
        $this->showDetailModal = true;
    }

    public function closeDetail(): void
    {
        $this->showDetailModal = false;
        $this->selectedReturn = null;
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingReturn = SaleReturn::with(['items', 'customer'])->findOrFail($id);
        $this->deletingReturnId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteReturn(): void
    {
        if (!$this->deletingReturnId) {
            return;
        }

        $saleReturn = SaleReturn::with('items')->findOrFail($this->deletingReturnId);

        DB::transaction(function () use ($saleReturn) {
            // Jika retur sebelumnya mengembalikan stok ke inventaris, kembalikan (kurangi) stoknya kembali
            if ($saleReturn->restock) {
                foreach ($saleReturn->items as $item) {
                    if ($item->product_id) {
                        $product = \App\Models\Product::lockForUpdate()->find($item->product_id);
                        if ($product) {
                            $stockBefore = $product->stock;
                            $product->decrement('stock', $item->quantity);
                            $stockAfter = $product->fresh()->stock;

                            \App\Models\StockMutation::create([
                                'product_id' => $product->id,
                                'user_id' => \Illuminate\Support\Facades\Auth::id() ?? 1,
                                'type' => 'out',
                                'quantity' => $item->quantity,
                                'stock_before' => $stockBefore,
                                'stock_after' => $stockAfter,
                                'description' => "Pembatalan / Hapus Retur Penjualan #{$saleReturn->return_number}",
                            ]);
                        }
                    }
                }
            }

            $returnNum = $saleReturn->return_number;
            $saleReturn->delete();

            $msg = "Data retur penjualan #{$returnNum} berhasil dihapus.";
            session()->flash('success', $msg);
            $this->dispatch('toast', ['message' => $msg, 'type' => 'success']);
        });

        $this->showDeleteModal = false;
        $this->reset(['deletingReturnId', 'deletingReturn']);
    }

    protected function applyFilters(Builder $query): Builder
    {
        // Search
        if (!empty($this->search)) {
            $term = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('return_number', 'LIKE', $term)
                  ->orWhere('invoice_number', 'LIKE', $term)
                  ->orWhere('reason', 'LIKE', $term)
                  ->orWhere('notes', 'LIKE', $term)
                  ->orWhereHas('customer', function ($cq) use ($term) {
                      $cq->where('name', 'LIKE', $term);
                  })
                  ->orWhereHas('items', function ($iq) use ($term) {
                      $iq->where('product_name', 'LIKE', $term);
                  });
            });
        }

        // Period filter
        $today = Carbon::today();
        if ($this->period === 'today') {
            $query->whereDate('created_at', $today);
        } elseif ($this->period === 'this_week') {
            $query->whereBetween('created_at', [$today->copy()->startOfWeek(), $today->copy()->endOfWeek()]);
        } elseif ($this->period === 'this_month') {
            $query->whereYear('created_at', $today->year)->whereMonth('created_at', $today->month);
        } elseif ($this->period === 'this_year') {
            $query->whereYear('created_at', $today->year);
        } elseif ($this->period === 'custom' && $this->startDate && $this->endDate) {
            $query->whereBetween('created_at', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay(),
            ]);
        }

        // Reason filter
        if ($this->reasonFilter !== 'all') {
            $query->where('reason', $this->reasonFilter);
        }

        // Restock filter
        if ($this->restockFilter === '1') {
            $query->where('restock', true);
        } elseif ($this->restockFilter === '0') {
            $query->where('restock', false);
        }

        return $query;
    }

    public function render()
    {
        // 1. KPI Statistics based on current filters
        $baseQuery = SaleReturn::query();
        $baseQuery = $this->applyFilters($baseQuery);

        $stats = [
            'total_cases' => (clone $baseQuery)->count(),
            'total_items' => (clone $baseQuery)->sum('total_items'),
            'total_refund' => (clone $baseQuery)->sum('total_refund'),
            'total_restocked' => (clone $baseQuery)->where('restock', true)->sum('total_items'),
        ];

        // 2. Paginated results
        $returnListQuery = SaleReturn::query()->with(['transaction', 'user', 'customer', 'items']);
        $returnListQuery = $this->applyFilters($returnListQuery);
        $returns = $returnListQuery->latest()->paginate(15);

        // 3. Unique reasons for dropdown filter
        $reasons = [
            'Salah Beli / Tukar Barang',
            'Barang Rusak / Cacat',
            'Kemasan Bocor / Rusak',
            'Kadaluarsa / Expired',
            'Salah Input Kasir',
            'Lainnya',
        ];

        $receiptSetting = ReceiptSetting::getSettings();

        return view('livewire.admin.transaction.sale-return-index', [
            'returns' => $returns,
            'stats' => $stats,
            'reasons' => $reasons,
            'receiptSetting' => $receiptSetting,
        ]);
    }
}
