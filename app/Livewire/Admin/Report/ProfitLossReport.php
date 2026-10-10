<?php

namespace App\Livewire\Admin\Report;

use App\Models\Asset;
use App\Models\Expense;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Laporan Laba Rugi & Kesehatan Keuangan - Syafa Frozen Food')]
class ProfitLossReport extends Component
{
    #[Url(history: true)]
    public string $period = 'this_month'; // today, yesterday, last_7_days, this_month, last_month, this_year, custom

    #[Url(history: true)]
    public string $startDate = '';

    #[Url(history: true)]
    public string $endDate = '';

    #[Url(history: true)]
    public string $cashierId = '';

    #[Url(history: true)]
    public ?int $outletId = null;

    public function mount(): void
    {
        $this->applyPeriodPreset($this->period);
    }

    public function setPeriod(string $preset): void
    {
        $this->period = $preset;
        $this->applyPeriodPreset($preset);
    }

    public function updatedStartDate(): void
    {
        $this->period = 'custom';
    }

    public function updatedEndDate(): void
    {
        $this->period = 'custom';
    }

    public function updatedCashierId(): void
    {
        // Reacts automatically
    }

    public function updatedOutletId(): void
    {
        // Reacts automatically
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

    public function render()
    {
        $cashiers = User::orderBy('name')->get();
        $outlets = Outlet::where('is_active', true)->orderBy('is_main', 'desc')->orderBy('name')->get();

        // 1. Query Penjualan & HPP
        $query = Transaction::query()
            ->completed()
            ->with(['items', 'user', 'outlet'])
            ->when($this->startDate, function ($q) {
                $q->whereDate('created_at', '>=', $this->startDate);
            })
            ->when($this->endDate, function ($q) {
                $q->whereDate('created_at', '<=', $this->endDate);
            })
            ->when($this->cashierId, function ($q) {
                $q->where('user_id', $this->cashierId);
            })
            ->when($this->outletId, function ($q) {
                $q->where('outlet_id', $this->outletId);
            });

        $allTransactions = $query->get();
        $totalTransactionsCount = $allTransactions->count();

        $grossSales = 0;
        $regularSales = 0;
        $memberSales = 0;
        $totalDiscount = 0;
        $netSales = 0;
        $totalHpp = 0;
        $totalUnitsSold = 0;
        $totalCashCollected = 0;

        foreach ($allTransactions as $trx) {
            $trxNet = (float) $trx->grand_total;
            $trxDisc = (float) $trx->discount_amount;
            $trxGross = $trxNet + $trxDisc;

            $grossSales += $trxGross;
            $totalDiscount += $trxDisc;
            $netSales += $trxNet;
            $totalCashCollected += (float) $trx->paid_amount;

            if ($trx->is_loyalty) {
                $memberSales += $trxNet;
            } else {
                $regularSales += $trxNet;
            }

            foreach ($trx->items as $item) {
                $totalHpp += ((float) $item->cost_price * (int) $item->quantity);
                $totalUnitsSold += (int) $item->quantity;
            }
        }

        // 2. Laba Kotor (Gross Profit)
        $grossProfit = $netSales - $totalHpp;
        $grossProfitMargin = $netSales > 0 ? round(($grossProfit / $netSales) * 100, 1) : 0;
        $averageOrderValue = $totalTransactionsCount > 0 ? round($netSales / $totalTransactionsCount) : 0;

        // 3. Query Beban Operasional (OPEX) dalam Periode
        $expenseQuery = Expense::query()
            ->when($this->startDate, fn($q) => $q->whereDate('expense_date', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('expense_date', '<=', $this->endDate))
            ->when($this->outletId, fn($q) => $q->where('outlet_id', $this->outletId));

        $allExpenses = $expenseQuery->get();
        $totalExpenses = (float) $allExpenses->sum('amount');

        $electricityExpenses = (float) $allExpenses->where('category', 'electricity_utility')->sum('amount');
        $salaryExpenses = (float) $allExpenses->where('category', 'salary_labor')->sum('amount');
        $packagingExpenses = (float) $allExpenses->where('category', 'packaging_supplies')->sum('amount');
        $maintenanceExpenses = (float) $allExpenses->where('category', 'maintenance_repair')->sum('amount');
        $rentExpenses = (float) $allExpenses->where('category', 'rent_premises')->sum('amount');
        $otherExpenses = (float) $allExpenses->where('category', 'other_operational')->sum('amount');

        // 4. Laba Bersih Sejati Usaha (True Net Profit)
        $trueNetProfit = $grossProfit - $totalExpenses;
        $netProfitMargin = $netSales > 0 ? round(($trueNetProfit / $netSales) * 100, 1) : 0;

        // 5. Financial Health Metrics:
        // A. Arus Kas Bersih (Net Cashflow) Periode Ini
        $purchaseCashOutflow = (float) Purchase::query()
            ->when($this->startDate, fn($q) => $q->whereDate('purchase_date', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('purchase_date', '<=', $this->endDate))
            ->when($this->outletId, fn($q) => $q->where('outlet_id', $this->outletId))
            ->sum('paid_amount');

        $totalCashInflow = $totalCashCollected;
        $totalCashOutflow = $purchaseCashOutflow + $totalExpenses;
        $netCashFlow = $totalCashInflow - $totalCashOutflow;

        // B. Break-Even Point (BEP / Titik Impas)
        // BEP Omzet = Total Beban Tetap (OPEX) / (Rasio Margin Kotor / 100)
        $grossMarginRatio = $netSales > 0 ? ($grossProfit / $netSales) : 0.20; // default 20% jika belum ada penjualan
        $bepRevenue = $grossMarginRatio > 0 ? round($totalExpenses / $grossMarginRatio) : 0;

        // C. Valuasi Total Kekayaan Bersih Toko (Net Worth)
        $hasOutletRecords = DB::table('outlet_products')->exists();

        if ($this->outletId) {
            $totalInventoryValuation = (float) (DB::table('outlet_products')
                ->join('products', 'outlet_products.product_id', '=', 'products.id')
                ->where('outlet_products.outlet_id', $this->outletId)
                ->where('products.is_active', true)
                ->selectRaw('SUM(outlet_products.stock * COALESCE(NULLIF(outlet_products.cost_price, 0), products.cost_price, 0)) as total_val')
                ->value('total_val') ?? 0);
        } elseif ($hasOutletRecords) {
            $totalInventoryValuation = (float) (DB::table('outlet_products')
                ->join('outlets', 'outlet_products.outlet_id', '=', 'outlets.id')
                ->join('products', 'outlet_products.product_id', '=', 'products.id')
                ->where('outlets.is_active', true)
                ->where('products.is_active', true)
                ->selectRaw('SUM(outlet_products.stock * COALESCE(NULLIF(outlet_products.cost_price, 0), products.cost_price, 0)) as total_val')
                ->value('total_val') ?? 0);
        } else {
            $allActiveProducts = Product::active()->get();
            $totalInventoryValuation = 0;
            foreach ($allActiveProducts as $p) {
                $totalInventoryValuation += (max(0, (int)$p->stock) * max(0, (float)$p->cost_price));
            }
        }

        $allReceivables = Transaction::where('payment_method', 'tempo')
            ->when($this->outletId, fn($q) => $q->where('outlet_id', $this->outletId))
            ->get();
        $totalActiveReceivables = (float) $allReceivables->sum(fn($t) => $t->remaining_amount);

        $allPurchases = Purchase::query()
            ->when($this->outletId, fn($q) => $q->where('outlet_id', $this->outletId))
            ->get();
        $totalActivePayables = (float) $allPurchases->sum(fn($po) => $po->remaining_amount);

        $totalFixedAssetsValuation = (float) Asset::sum('purchase_cost');

        $totalNetWorth = ($totalInventoryValuation + $totalActiveReceivables + $totalFixedAssetsValuation) - $totalActivePayables;

        return view('livewire.admin.report.profit-loss-report', [
            'cashiers' => $cashiers,
            'outlets' => $outlets,
            'grossSales' => $grossSales,
            'regularSales' => $regularSales,
            'memberSales' => $memberSales,
            'totalDiscount' => $totalDiscount,
            'netSales' => $netSales,
            'totalHpp' => $totalHpp,
            'grossProfit' => $grossProfit,
            'grossProfitMargin' => $grossProfitMargin,
            'totalExpenses' => $totalExpenses,
            'electricityExpenses' => $electricityExpenses,
            'salaryExpenses' => $salaryExpenses,
            'packagingExpenses' => $packagingExpenses,
            'maintenanceExpenses' => $maintenanceExpenses,
            'rentExpenses' => $rentExpenses,
            'otherExpenses' => $otherExpenses,
            'trueNetProfit' => $trueNetProfit,
            'netProfitMargin' => $netProfitMargin,
            'averageOrderValue' => $averageOrderValue,
            'totalTransactionsCount' => $totalTransactionsCount,
            'totalUnitsSold' => $totalUnitsSold,
            // Health Scorecard
            'totalCashInflow' => $totalCashInflow,
            'totalCashOutflow' => $totalCashOutflow,
            'netCashFlow' => $netCashFlow,
            'bepRevenue' => $bepRevenue,
            'totalInventoryValuation' => $totalInventoryValuation,
            'totalActiveReceivables' => $totalActiveReceivables,
            'totalActivePayables' => $totalActivePayables,
            'totalFixedAssetsValuation' => $totalFixedAssetsValuation,
            'totalNetWorth' => $totalNetWorth,
        ]);
    }
}
