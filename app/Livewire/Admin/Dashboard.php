<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\Expense;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Dashboard Executive - Syafa Frozen Food')]
class Dashboard extends Component
{
    #[Url(history: true)]
    public string $period = 'today'; // today, last_7_days, this_month, this_year

    #[Url(history: true)]
    public ?int $outletId = null;

    // Modal detail transaksi
    public bool $showDetailModal = false;
    public ?int $selectedTransactionId = null;
    public string $waPhoneNumber = '';

    public function setPeriod(string $preset): void
    {
        $this->period = $preset;
        $this->dispatch('update-dashboard-charts');
    }

    public function updatedOutletId(): void
    {
        $this->dispatch('update-dashboard-charts');
    }

    public function openTransactionDetail(int $id): void
    {
        $this->selectedTransactionId = $id;
        $trx = Transaction::with('customer')->find($id);
        $this->waPhoneNumber = $trx?->customer?->phone ?? '';
        $this->showDetailModal = true;
    }

    public function closeTransactionDetail(): void
    {
        $this->showDetailModal = false;
        $this->selectedTransactionId = null;
        $this->waPhoneNumber = '';
    }

    public function render()
    {
        $now = Carbon::now();
        $startDate = $now->copy()->startOfDay();
        $endDate = $now->copy()->endOfDay();
        $prevStartDate = $now->copy()->subDay()->startOfDay();
        $prevEndDate = $now->copy()->subDay()->endOfDay();
        $periodLabel = 'Hari Ini (' . $now->translatedFormat('d M Y') . ')';

        switch ($this->period) {
            case 'last_7_days':
                $startDate = $now->copy()->subDays(6)->startOfDay();
                $endDate = $now->copy()->endOfDay();
                $prevStartDate = $now->copy()->subDays(13)->startOfDay();
                $prevEndDate = $now->copy()->subDays(7)->endOfDay();
                $periodLabel = '7 Hari Terakhir';
                break;
            case 'this_month':
                $startDate = $now->copy()->startOfMonth()->startOfDay();
                $endDate = $now->copy()->endOfMonth()->endOfDay();
                $prevStartDate = $now->copy()->subMonth()->startOfMonth()->startOfDay();
                $prevEndDate = $now->copy()->subMonth()->endOfMonth()->endOfDay();
                $periodLabel = 'Bulan Ini (' . $now->translatedFormat('F Y') . ')';
                break;
            case 'this_year':
                $startDate = $now->copy()->startOfYear()->startOfDay();
                $endDate = $now->copy()->endOfYear()->endOfDay();
                $prevStartDate = $now->copy()->subYear()->startOfYear()->startOfDay();
                $prevEndDate = $now->copy()->subYear()->endOfYear()->endOfDay();
                $periodLabel = 'Tahun Ini (' . $now->format('Y') . ')';
                break;
            case 'today':
            default:
                $startDate = $now->copy()->startOfDay();
                $endDate = $now->copy()->endOfDay();
                $prevStartDate = $now->copy()->subDay()->startOfDay();
                $prevEndDate = $now->copy()->subDay()->endOfDay();
                $periodLabel = 'Hari Ini (' . $now->translatedFormat('d M Y') . ')';
                break;
        }

        // 1. QUERY TRANSAKSI PERIODE AKTIF
        $currentTransactionsQuery = Transaction::completed()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->when($this->outletId, fn($q) => $q->where('outlet_id', $this->outletId));

        $currentTransactions = (clone $currentTransactionsQuery)->with(['items', 'customer'])->get();

        $sales = (float) $currentTransactions->sum('grand_total');
        $ordersCount = $currentTransactions->count();
        $aov = $ordersCount > 0 ? round($sales / $ordersCount) : 0;
        $memberOrdersCount = $currentTransactions->where('is_loyalty', true)->count();
        $memberSales = (float) $currentTransactions->where('is_loyalty', true)->sum('grand_total');

        // Hitung HPP & Total Unit Terjual
        $totalHpp = 0;
        $totalUnitsSold = 0;
        foreach ($currentTransactions as $trx) {
            foreach ($trx->items as $item) {
                $totalHpp += ((float) $item->cost_price * (int) $item->quantity);
                $totalUnitsSold += (int) $item->quantity;
            }
        }

        $grossProfit = max(0, $sales - $totalHpp);
        $grossMargin = $sales > 0 ? round(($grossProfit / $sales) * 100, 1) : 0;

        // 2. QUERY TRANSAKSI PERIODE SEBELUMNYA (UNTUK GROWTH % COMPARISON)
        $prevSales = (float) Transaction::completed()
            ->whereBetween('created_at', [$prevStartDate, $prevEndDate])
            ->when($this->outletId, fn($q) => $q->where('outlet_id', $this->outletId))
            ->sum('grand_total');

        $salesGrowth = null;
        if ($prevSales > 0) {
            $salesGrowth = round((($sales - $prevSales) / $prevSales) * 100, 1);
        }

        // 3. BEBAN OPERASIONAL (EXPENSE / OPEX)
        $expenses = (float) Expense::query()
            ->whereBetween('expense_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->when($this->outletId, fn($q) => $q->where('outlet_id', $this->outletId))
            ->sum('amount');

        $netProfit = $grossProfit - $expenses;
        $netMargin = $sales > 0 ? round(($netProfit / $sales) * 100, 1) : 0;

        // 4. PIUTANG BERJALAN & PERINGATAN JATUH TEMPO
        $activeReceivablesQuery = Transaction::tempo()
            ->unpaid()
            ->when($this->outletId, fn($q) => $q->where('outlet_id', $this->outletId))
            ->with('customer');

        $activeReceivablesList = $activeReceivablesQuery->get();
        $totalReceivables = (float) $activeReceivablesList->sum(fn($t) => max(0, (float)$t->grand_total - (float)$t->paid_amount));
        $overdueReceivables = $activeReceivablesList->filter(fn($t) => $t->is_overdue);
        $overdueCount = $overdueReceivables->count();
        $overdueTotal = (float) $overdueReceivables->sum(fn($t) => max(0, (float)$t->grand_total - (float)$t->paid_amount));

        // 5. INVENTORI & VALUASI TOTAL ASET
        $totalProductsCount = Product::active()->count();
        
        $hasOutletRecords = DB::table('outlet_products')->exists();

        if ($this->outletId) {
            $inventoryValuation = (float) (DB::table('outlet_products')
                ->join('products', 'outlet_products.product_id', '=', 'products.id')
                ->where('outlet_products.outlet_id', $this->outletId)
                ->where('products.is_active', true)
                ->selectRaw('SUM(outlet_products.stock * COALESCE(NULLIF(outlet_products.cost_price, 0), products.cost_price, 0)) as total_val')
                ->value('total_val') ?? 0);

            $totalStockUnits = (int) DB::table('outlet_products')
                ->join('products', 'outlet_products.product_id', '=', 'products.id')
                ->where('outlet_products.outlet_id', $this->outletId)
                ->where('products.is_active', true)
                ->sum('outlet_products.stock');
        } elseif ($hasOutletRecords) {
            $inventoryValuation = (float) (DB::table('outlet_products')
                ->join('outlets', 'outlet_products.outlet_id', '=', 'outlets.id')
                ->join('products', 'outlet_products.product_id', '=', 'products.id')
                ->where('outlets.is_active', true)
                ->where('products.is_active', true)
                ->selectRaw('SUM(outlet_products.stock * COALESCE(NULLIF(outlet_products.cost_price, 0), products.cost_price, 0)) as total_val')
                ->value('total_val') ?? 0);

            $totalStockUnits = (int) DB::table('outlet_products')
                ->join('outlets', 'outlet_products.outlet_id', '=', 'outlets.id')
                ->join('products', 'outlet_products.product_id', '=', 'products.id')
                ->where('outlets.is_active', true)
                ->where('products.is_active', true)
                ->sum('outlet_products.stock');
        } else {
            $inventoryValuation = (float) (Product::active()
                ->selectRaw('SUM(stock * cost_price) as total_val')
                ->value('total_val') ?? 0);
            $totalStockUnits = (int) Product::active()->sum('stock');
        }

        $lowStockProducts = Product::active()
            ->lowStock()
            ->with('category')
            ->orderBy('stock', 'asc')
            ->take(5)
            ->get();
        $totalLowStockCount = Product::active()->lowStock()->count();

        // 6. TOP 5 PRODUK TERLARIS DALAM PERIODE
        $topProducts = TransactionItem::query()
            ->whereHas('transaction', function ($q) use ($startDate, $endDate) {
                $q->where('status', 'completed')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->when($this->outletId, fn($sq) => $sq->where('outlet_id', $this->outletId));
            })
            ->select('product_id', 'product_name', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_revenue'))
            ->groupBy('product_id', 'product_name')
            ->orderByDesc('total_qty')
            ->take(5)
            ->with('product.category')
            ->get();

        // 7. DISTRIBUSI METODE PEMBAYARAN
        $paymentDistribution = [
            'cash' => (float) $currentTransactions->where('payment_method', 'cash')->sum('grand_total'),
            'qris' => (float) $currentTransactions->where('payment_method', 'qris')->sum('grand_total'),
            'transfer' => (float) $currentTransactions->where('payment_method', 'transfer')->sum('grand_total'),
            'tempo' => (float) $currentTransactions->where('payment_method', 'tempo')->sum('grand_total'),
        ];

        // 8. KONTRIBUSI PENJUALAN PER KATEGORI
        $categorySalesMap = [];
        foreach ($currentTransactions as $trx) {
            foreach ($trx->items as $item) {
                $catName = $item->product?->category?->name ?? 'Lain-lain';
                if (!isset($categorySalesMap[$catName])) {
                    $categorySalesMap[$catName] = 0;
                }
                $categorySalesMap[$catName] += (float) $item->subtotal;
            }
        }
        arsort($categorySalesMap);
        $topCategories = array_slice($categorySalesMap, 0, 5, true);

        // 9. DATA GRAFIK TREN (DAILY TIMELINE)
        // Buat rentang chart (7 hari jika today, atau sesuai rentang periode)
        $chartStartDate = ($this->period === 'today') ? $now->copy()->subDays(6)->startOfDay() : $startDate->copy();
        $chartEndDate = $endDate->copy();
        
        $chartDays = CarbonPeriod::create($chartStartDate, $chartEndDate);
        $chartLabels = [];
        $chartOmset = [];
        $chartProfit = [];

        // Query agregasi harian untuk grafik
        $chartTrxItems = TransactionItem::whereHas('transaction', function ($q) use ($chartStartDate, $chartEndDate) {
            $q->where('status', 'completed')
                ->whereBetween('created_at', [$chartStartDate, $chartEndDate])
                ->when($this->outletId, fn($sq) => $sq->where('outlet_id', $this->outletId));
        })->with('transaction')->get();

        $dailyTrxMap = [];
        foreach ($chartTrxItems as $item) {
            if (!$item->transaction) continue;
            $dayKey = $item->transaction->created_at->format('Y-m-d');
            if (!isset($dailyTrxMap[$dayKey])) {
                $dailyTrxMap[$dayKey] = ['omset' => 0, 'hpp' => 0];
            }
            $dailyTrxMap[$dayKey]['omset'] += (float) $item->subtotal;
            $dailyTrxMap[$dayKey]['hpp'] += ((float) $item->cost_price * (int) $item->quantity);
        }

        foreach ($chartDays as $date) {
            $dayKey = $date->format('Y-m-d');
            $chartLabels[] = $date->translatedFormat('d M');
            $dayOmset = $dailyTrxMap[$dayKey]['omset'] ?? 0;
            $dayHpp = $dailyTrxMap[$dayKey]['hpp'] ?? 0;
            $chartOmset[] = (int) $dayOmset;
            $chartProfit[] = (int) max(0, $dayOmset - $dayHpp);
        }

        // 10. TRANSAKSI TERBARU (6 Transaksi)
        $recentTransactions = Transaction::with(['user', 'customer', 'items', 'outlet'])
            ->when($this->outletId, fn($q) => $q->where('outlet_id', $this->outletId))
            ->latest()
            ->take(6)
            ->get();

        $outlets = Outlet::where('is_active', true)->orderBy('is_main', 'desc')->orderBy('name')->get();

        // Selected Transaction for Detail Modal
        $selectedTransaction = $this->selectedTransactionId
            ? Transaction::with(['items.product', 'user', 'customer', 'outlet'])->find($this->selectedTransactionId)
            : null;

        return view('livewire.admin.dashboard', [
            'periodLabel' => $periodLabel,
            'sales' => $sales,
            'ordersCount' => $ordersCount,
            'aov' => $aov,
            'memberOrdersCount' => $memberOrdersCount,
            'memberSales' => $memberSales,
            'grossProfit' => $grossProfit,
            'grossMargin' => $grossMargin,
            'salesGrowth' => $salesGrowth,
            'expenses' => $expenses,
            'netProfit' => $netProfit,
            'netMargin' => $netMargin,
            'totalReceivables' => $totalReceivables,
            'overdueCount' => $overdueCount,
            'overdueTotal' => $overdueTotal,
            'overdueReceivables' => $overdueReceivables->take(3),
            'totalProductsCount' => $totalProductsCount,
            'totalStockUnits' => $totalStockUnits,
            'inventoryValuation' => $inventoryValuation,
            'lowStockProducts' => $lowStockProducts,
            'totalLowStockCount' => $totalLowStockCount,
            'topProducts' => $topProducts,
            'paymentDistribution' => $paymentDistribution,
            'topCategories' => $topCategories,
            'chartLabels' => $chartLabels,
            'chartOmset' => $chartOmset,
            'chartProfit' => $chartProfit,
            'recentTransactions' => $recentTransactions,
            'outlets' => $outlets,
            'selectedTransaction' => $selectedTransaction,
        ]);
    }
}
