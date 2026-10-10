<?php

namespace App\Livewire\Admin\Report;

use App\Models\Outlet;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Laporan Penjualan & Grafik - Syafa Frozen Food')]
class SalesReport extends Component
{
    use WithPagination;

    // Filters
    public string $period = 'this_month'; // today, yesterday, last_7_days, this_month, last_month, this_year, custom
    public string $startDate = '';
    public string $endDate = '';
    public string $paymentMethod = '';
    public string $cashierId = '';
    public ?int $outletId = null;
    public string $search = '';

    // Modal Detail Transaksi
    public bool $showDetailModal = false;
    public ?int $selectedTransactionId = null;
    public string $waPhoneNumber = '';

    public function mount(): void
    {
        $this->applyPeriodPreset('this_month');
    }

    public function setPeriod(string $preset): void
    {
        $this->period = $preset;
        $this->applyPeriodPreset($preset);
        $this->resetPage();
        $this->dispatchChartsUpdate();
    }

    public function updatedStartDate(): void
    {
        $this->period = 'custom';
        $this->resetPage();
        $this->dispatchChartsUpdate();
    }

    public function updatedEndDate(): void
    {
        $this->period = 'custom';
        $this->resetPage();
        $this->dispatchChartsUpdate();
    }

    public function updatedPaymentMethod(): void
    {
        $this->resetPage();
        $this->dispatchChartsUpdate();
    }

    public function updatedCashierId(): void
    {
        $this->resetPage();
        $this->dispatchChartsUpdate();
    }

    public function updatedOutletId(): void
    {
        $this->resetPage();
        $this->dispatchChartsUpdate();
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
            default:
                break;
        }
    }

    public function openDetail(int $id): void
    {
        $this->selectedTransactionId = $id;
        $trx = Transaction::with(['customer'])->find($id);
        $this->waPhoneNumber = $trx?->customer?->phone ?: '';
        $this->showDetailModal = true;
    }

    public function closeDetail(): void
    {
        $this->showDetailModal = false;
        $this->selectedTransactionId = null;
        $this->waPhoneNumber = '';
    }

    public function generateWhatsAppReceiptText(Transaction $trx): string
    {
        $date = $trx->created_at->translatedFormat('d/m/Y H:i');
        $kasir = $trx->user->name ?? 'Kasir';
        $pelanggan = $trx->customer_name . ($trx->is_loyalty ? ' (Member)' : '');

        $lines = [];
        $lines[] = "*NOTA PEMBELIAN - SYAFA FROZEN FOOD*";
        $lines[] = "Pusat Frozen Food Berkualitas & Terpercaya";
        $lines[] = "----------------------------------------";
        $lines[] = "*No. Invoice:* {$trx->invoice_number}";
        $lines[] = "*Waktu:* {$date} WIB";
        $lines[] = "*Kasir:* {$kasir}";
        $lines[] = "*Pelanggan:* {$pelanggan}";
        $lines[] = "----------------------------------------";
        $lines[] = "*Rincian Belanja:*";

        foreach ($trx->items as $item) {
            $lines[] = "• *{$item->product_name}*";
            $lines[] = "  {$item->quantity} x Rp " . number_format($item->unit_price, 0, ',', '.') . " = Rp " . number_format($item->subtotal, 0, ',', '.');
        }

        $lines[] = "----------------------------------------";
        $lines[] = "*Total Belanja:* Rp " . number_format($trx->grand_total, 0, ',', '.');
        $lines[] = "*Bayar:* Rp " . number_format($trx->paid_amount, 0, ',', '.');
        $lines[] = "*Kembalian:* Rp " . number_format($trx->change_amount, 0, ',', '.');
        $lines[] = "----------------------------------------";
        $lines[] = "Terima kasih telah berbelanja di Syafa Frozen Food!";

        return implode("\n", $lines);
    }

    private function getBaseQuery()
    {
        return Transaction::query()
            ->completed()
            ->with(['user', 'customer', 'items'])
            ->when($this->startDate, function ($q) {
                $q->whereDate('created_at', '>=', $this->startDate);
            })
            ->when($this->endDate, function ($q) {
                $q->whereDate('created_at', '<=', $this->endDate);
            })
            ->when($this->paymentMethod, function ($q) {
                $q->where('payment_method', $this->paymentMethod);
            })
            ->when($this->cashierId, function ($q) {
                $q->where('user_id', $this->cashierId);
            });
    }

    private function dispatchChartsUpdate(): void
    {
        $this->dispatch('update-charts');
    }

    public function render()
    {
        $cashiers = User::orderBy('name')->get();

        $baseQuery = $this->getBaseQuery();

        // 1. Financial KPI Summary
        $allTransactions = (clone $baseQuery)->get();
        $totalTransactionsCount = $allTransactions->count();

        $grossSales = 0;
        $totalDiscount = 0;
        $netSales = 0;
        $totalHpp = 0;
        $totalUnitsSold = 0;

        $cashTotal = 0;
        $cashCount = 0;
        $qrisTotal = 0;
        $qrisCount = 0;
        $transferTotal = 0;
        $transferCount = 0;

        // Daily aggregated map for Trend Chart
        $dailyMap = [];

        try {
            $start = Carbon::parse($this->startDate);
            $end = Carbon::parse($this->endDate);
            if ($start->diffInDays($end) <= 62) {
                $period = CarbonPeriod::create($start, $end);
                foreach ($period as $date) {
                    $key = $date->format('Y-m-d');
                    $dailyMap[$key] = [
                        'date' => $key,
                        'label' => $date->translatedFormat('d M'),
                        'omset' => 0,
                        'hpp' => 0,
                        'profit' => 0,
                    ];
                }
            }
        } catch (\Exception $e) {
            // fallback
        }

        foreach ($allTransactions as $trx) {
            $trxNet = (float)$trx->grand_total;
            $trxDisc = (float)$trx->discount_amount;
            $trxGross = $trxNet + $trxDisc;

            $grossSales += $trxGross;
            $totalDiscount += $trxDisc;
            $netSales += $trxNet;

            if ($trx->payment_method === 'cash') {
                $cashTotal += $trxNet;
                $cashCount++;
            } elseif ($trx->payment_method === 'qris') {
                $qrisTotal += $trxNet;
                $qrisCount++;
            } elseif ($trx->payment_method === 'transfer') {
                $transferTotal += $trxNet;
                $transferCount++;
            }

            $trxHpp = 0;
            foreach ($trx->items as $item) {
                $itemHpp = ((float)$item->cost_price * (int)$item->quantity);
                $totalHpp += $itemHpp;
                $trxHpp += $itemHpp;
                $totalUnitsSold += (int)$item->quantity;
            }

            $trxDate = Carbon::parse($trx->created_at)->format('Y-m-d');
            if (isset($dailyMap[$trxDate])) {
                $dailyMap[$trxDate]['omset'] += $trxNet;
                $dailyMap[$trxDate]['hpp'] += $trxHpp;
                $dailyMap[$trxDate]['profit'] += ($trxNet - $trxHpp);
            } else {
                $dailyMap[$trxDate] = [
                    'date' => $trxDate,
                    'label' => Carbon::parse($trxDate)->translatedFormat('d M'),
                    'omset' => ($dailyMap[$trxDate]['omset'] ?? 0) + $trxNet,
                    'hpp' => ($dailyMap[$trxDate]['hpp'] ?? 0) + $trxHpp,
                    'profit' => ($dailyMap[$trxDate]['profit'] ?? 0) + ($trxNet - $trxHpp),
                ];
            }
        }

        $netProfit = $netSales - $totalHpp;
        $profitMargin = $netSales > 0 ? round(($netProfit / $netSales) * 100, 1) : 0;
        $averageOrderValue = $totalTransactionsCount > 0 ? round($netSales / $totalTransactionsCount) : 0;

        // 2. Chart 1: Trend Data (Omset vs Laba Bersih)
        ksort($dailyMap);
        $trendLabels = array_column($dailyMap, 'label');
        $trendOmset = array_column($dailyMap, 'omset');
        $trendProfit = array_column($dailyMap, 'profit');

        // 3. Chart 2: Payment Breakdown
        $paymentChartData = [
            'labels' => ['Tunai (Cash)', 'QRIS', 'Transfer Bank'],
            'data' => [$cashTotal, $qrisTotal, $transferTotal],
            'counts' => [$cashCount, $qrisCount, $transferCount],
        ];

        // 4. Chart 3: Top 5 Best Selling Products
        $topProducts = TransactionItem::query()
            ->whereHas('transaction', function ($q) {
                $q->completed()
                    ->when($this->startDate, fn($sq) => $sq->whereDate('created_at', '>=', $this->startDate))
                    ->when($this->endDate, fn($sq) => $sq->whereDate('created_at', '<=', $this->endDate))
                    ->when($this->cashierId, fn($sq) => $sq->where('user_id', $this->cashierId))
                    ->when($this->outletId, fn($sq) => $sq->where('outlet_id', $this->outletId))
                    ->when($this->paymentMethod, fn($sq) => $sq->where('payment_method', $this->paymentMethod));
            })
            ->select(
                'product_name',
                DB::raw('SUM(quantity) as total_qty'),
                DB::raw('SUM(subtotal) as total_revenue'),
                DB::raw('SUM(cost_price * quantity) as total_cogs')
            )
            ->groupBy('product_name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        $topProductLabels = $topProducts->pluck('product_name')->toArray();
        $topProductQty = $topProducts->pluck('total_qty')->map(fn($v) => (int)$v)->toArray();

        // 5. Paginated Transactions Table
        $transactions = (clone $baseQuery)
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('invoice_number', 'like', '%' . $this->search . '%')
                        ->orWhere('customer_name', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate(15);

        $selectedTransaction = null;
        if ($this->selectedTransactionId) {
            $selectedTransaction = Transaction::with(['items', 'user', 'customer', 'outlet'])->find($this->selectedTransactionId);
        }

        $outlets = Outlet::where('is_active', true)->orderBy('is_main', 'desc')->orderBy('name')->get();

        return view('livewire.admin.report.sales-report', [
            'cashiers' => $cashiers,
            'outlets' => $outlets,
            'transactions' => $transactions,
            'selectedTransaction' => $selectedTransaction,
            'grossSales' => $grossSales,
            'totalDiscount' => $totalDiscount,
            'netSales' => $netSales,
            'totalHpp' => $totalHpp,
            'netProfit' => $netProfit,
            'profitMargin' => $profitMargin,
            'averageOrderValue' => $averageOrderValue,
            'totalTransactionsCount' => $totalTransactionsCount,
            'totalUnitsSold' => $totalUnitsSold,
            'cashTotal' => $cashTotal,
            'cashCount' => $cashCount,
            'qrisTotal' => $qrisTotal,
            'qrisCount' => $qrisCount,
            'transferTotal' => $transferTotal,
            'transferCount' => $transferCount,
            'trendLabels' => $trendLabels,
            'trendOmset' => $trendOmset,
            'trendProfit' => $trendProfit,
            'paymentChartData' => $paymentChartData,
            'topProducts' => $topProducts,
            'topProductLabels' => $topProductLabels,
            'topProductQty' => $topProductQty,
        ]);
    }
}
