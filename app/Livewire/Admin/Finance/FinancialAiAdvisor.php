<?php

namespace App\Livewire\Admin\Finance;

use App\Models\Asset;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\Payroll;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Transaction;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Penasihat Keuangan & Alokasi Toko - Syafa Frozen Food')]
class FinancialAiAdvisor extends Component
{
    // Periode Analisis: 30_days, this_month, last_month, 90_days
    public string $analysisPeriod = '30_days';

    // Interactive Simulator State: Investasi Aset / Freezer Baru
    public float|int|string $simAssetPrice = 6500000; // Rp 6.500.000 (Freezer 500L)
    public float|int|string $simEstimatedMonthlyRevenue = 4500000; // Tambahan omzet Rp 4.500.000/bln
    public float|int|string $simExtraElectricity = 280000; // Tambahan listrik Rp 280.000/bln

    // Interactive Simulator State: Penggajian Karyawan
    public int $simEmployeeCount = 2; // Jumlah Karyawan
    public float|int|string $simEmployeeSalary = 1800000; // Gaji Pokok per Orang Rp 1.800.000/bln
    public float|int|string $simEmployeeAllowance = 200000; // Tunjangan/Uang Makan per Orang Rp 200.000/bln

    // Target Laba Bersih yang Diinginkan Toko (% dari omzet)
    public float|int|string $targetNetProfitMargin = 15.0; // 15%

    // Custom Pembagian Alokasi Keuangan (%)
    public int $allocRestockPercent = 40;     // 40% Modal Belanja Stok
    public int $allocOpexPercent = 20;        // 20% Operasional Harian
    public int $allocPayrollPercent = 15;     // 15% Cadangan Gaji Karyawan
    public int $allocOwnerPercent = 10;       // 10% Hak Pemilik / Prive Aman
    public int $allocEmergencyPercent = 10;   // 10% Dana Darurat & Maintenance Freezer
    public int $allocExpansionPercent = 5;    // 5% Tabungan Ekspansi

    public function mount(): void
    {
        // Default inisialisasi dari data riil karyawan jika ada di database
        $activeEmployeesCount = Employee::where('is_active', true)->count();
        if ($activeEmployeesCount > 0) {
            $this->simEmployeeCount = $activeEmployeesCount;
            $avgSalary = Employee::where('is_active', true)->avg('base_salary');
            $avgAllowance = Employee::where('is_active', true)->avg('daily_allowance');
            if ($avgSalary > 0) {
                $this->simEmployeeSalary = (float) $avgSalary;
            }
            if ($avgAllowance > 0) {
                $this->simEmployeeAllowance = (float) ($avgAllowance * 26);
            }
        }
    }

    public function useRealEmployeeData(): void
    {
        $activeEmployees = Employee::where('is_active', true)->get();
        if ($activeEmployees->count() > 0) {
            $this->simEmployeeCount = $activeEmployees->count();
            $this->simEmployeeSalary = (float) ($activeEmployees->avg('base_salary') ?? 1800000);
            $this->simEmployeeAllowance = (float) (($activeEmployees->avg('daily_allowance') ?? 0) * 26);
            
            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Data penggajian berhasil disinkronkan dengan ' . $this->simEmployeeCount . ' karyawan aktif di database!'
            ]);
        } else {
            $this->dispatch('toast', [
                'type' => 'warning',
                'message' => 'Belum ada data karyawan aktif di database.'
            ]);
        }
    }

    public function updated($propertyName, $value): void
    {
        if (in_array($propertyName, ['simAssetPrice', 'simEstimatedMonthlyRevenue', 'simExtraElectricity', 'simEmployeeSalary', 'simEmployeeAllowance', 'targetNetProfitMargin'])) {
            $clean = is_numeric($value) ? (float) $value : (float) preg_replace('/[^0-9.]/', '', (string) $value);
            $this->$propertyName = $clean;
        }

        if ($propertyName === 'simEmployeeCount') {
            $this->simEmployeeCount = max(1, (int) $value);
        }
    }

    public function render()
    {
        $now = Carbon::now();
        $startDate = match ($this->analysisPeriod) {
            'this_month' => $now->copy()->startOfMonth(),
            'last_month' => $now->copy()->subMonth()->startOfMonth(),
            '90_days' => $now->copy()->subDays(90)->startOfDay(),
            default => $now->copy()->subDays(30)->startOfDay(),
        };

        $endDate = match ($this->analysisPeriod) {
            'last_month' => $now->copy()->subMonth()->endOfMonth(),
            default => $now->copy()->endOfDay(),
        };

        $daysInPeriod = (int) round(max(1, $startDate->diffInDays($endDate) + 1));

        // 1. Analisis Penjualan & HPP
        $transactions = Transaction::query()
            ->completed()
            ->with('items')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $totalTransactions = $transactions->count();
        $netSales = 0;
        $totalHpp = 0;
        $totalDiscount = 0;

        foreach ($transactions as $trx) {
            $netSales += (float) $trx->grand_total;
            $totalDiscount += (float) $trx->discount_amount;
            foreach ($trx->items as $item) {
                $totalHpp += ((float) $item->cost_price * (int) $item->quantity);
            }
        }

        $grossProfit = $netSales - $totalHpp;
        $grossMarginPercent = $netSales > 0 ? ($grossProfit / $netSales) * 100 : 20.0;
        $effectiveGrossMarginDecimal = max(0.05, $grossMarginPercent / 100);

        $dailyAverageSales = $netSales / $daysInPeriod;
        $averageOrderValue = $totalTransactions > 0 ? $netSales / $totalTransactions : 0;

        // 2. Analisis Beban Operasional (OPEX) berdasarkan Kategori
        $expenses = Expense::query()
            ->whereDate('expense_date', '>=', $startDate->toDateString())
            ->whereDate('expense_date', '<=', $endDate->toDateString())
            ->get();

        $totalOpex = (float) $expenses->sum('amount');
        $electricityOpex = (float) $expenses->where('category', 'electricity_utility')->sum('amount');
        $payrollOpex = (float) $expenses->where('category', 'salary_labor')->sum('amount');
        $packagingOpex = (float) $expenses->where('category', 'packaging_supplies')->sum('amount');
        $maintenanceOpex = (float) $expenses->where('category', 'maintenance_repair')->sum('amount');
        $rentOpex = (float) $expenses->where('category', 'rent_premises')->sum('amount');
        $otherOpex = $totalOpex - $electricityOpex - $payrollOpex - $packagingOpex - $maintenanceOpex - $rentOpex;

        $dailyAverageOpex = $totalOpex / $daysInPeriod;
        $electricityRatio = $netSales > 0 ? ($electricityOpex / $netSales) * 100 : 0;

        // 3. True Net Profit & Net Margin
        $netProfit = $grossProfit - $totalOpex;
        $netProfitMargin = $netSales > 0 ? ($netProfit / $netSales) * 100 : 0;

        // 4. Perhitungan Break-Even Point (BEP)
        $bepOmzetTotal = $effectiveGrossMarginDecimal > 0 ? $totalOpex / $effectiveGrossMarginDecimal : 0;
        $bepOmzetHarian = $bepOmzetTotal / $daysInPeriod;

        $bepAchievementPercent = $bepOmzetHarian > 0 ? ($dailyAverageSales / $bepOmzetHarian) * 100 : 100;
        $bepStatus = match (true) {
            $dailyAverageSales >= ($bepOmzetHarian * 1.3) => 'surplus_tinggi',
            $dailyAverageSales >= $bepOmzetHarian => 'aman',
            $dailyAverageSales >= ($bepOmzetHarian * 0.8) => 'waspada',
            default => 'kritis',
        };

        // Target Penjualan Optimal
        $targetNetMargin = (float) $this->targetNetProfitMargin;
        $targetMarginDecimal = max(0.01, min(0.40, $targetNetMargin / 100));
        $denominator = $effectiveGrossMarginDecimal - $targetMarginDecimal;
        $targetOmzetTotal = $denominator > 0.02 ? $totalOpex / $denominator : $bepOmzetTotal * 1.5;
        $targetOmzetHarian = $targetOmzetTotal / $daysInPeriod;

        // 5. Analisis Hutang Supplier, Piutang & Valuasi Stok
        $allUnpaidPurchases = Purchase::whereIn('payment_status', ['unpaid', 'partial'])->get();
        $totalUnpaidPayable = 0;
        $totalOverduePayable = 0;
        $todayStr = $now->toDateString();
        foreach ($allUnpaidPurchases as $po) {
            $rem = max(0, (float) $po->total_amount - (float) $po->paid_amount);
            $totalUnpaidPayable += $rem;
            if ($po->due_date && $po->due_date->format('Y-m-d') < $todayStr) {
                $totalOverduePayable += $rem;
            }
        }

        $allUnpaidTrx = Transaction::whereIn('payment_status', ['unpaid', 'partial'])->get();
        $totalUnpaidReceivable = 0;
        foreach ($allUnpaidTrx as $trx) {
            $totalUnpaidReceivable += max(0, (float) $trx->grand_total - (float) $trx->paid_amount);
        }
        
        $totalInventoryAssetValue = 0;
        $allProducts = Product::all();
        foreach ($allProducts as $p) {
            $totalInventoryAssetValue += ((float) $p->cost_price * (int) $p->stock);
        }

        // 6. Plafon Anggaran Belanja Stok (Purchasing Budget Ceiling)
        $monthlyProjectedSales = $dailyAverageSales * 30;
        $recommendedPurchaseCeilingMonthly = max(0, ($monthlyProjectedSales * (1 - $effectiveGrossMarginDecimal)) - $totalOverduePayable);
        $recommendedPurchaseCeilingWeekly = $recommendedPurchaseCeilingMonthly / 4;

        $purchaseStatus = match (true) {
            $totalOverduePayable > 0 && $netProfit <= 0 => 'kritis_tahan_belanja',
            $totalOverduePayable > 0 => 'waspada_lunasi_hutang',
            $netProfit > 0 => 'aman_restock',
            default => 'waspada_batasi',
        };

        // 7. Alokasi Pembagian Keuangan Toko Lengkap (Dynamic Calculation)
        $safeBase = max(0, $netProfit > 0 ? $netProfit : $grossProfit);
        $allocRestockNominal = $safeBase * ($this->allocRestockPercent / 100);
        $allocOpexNominal = $safeBase * ($this->allocOpexPercent / 100);
        $allocPayrollNominal = $safeBase * ($this->allocPayrollPercent / 100);
        $allocOwnerNominal = $safeBase * ($this->allocOwnerPercent / 100);
        $allocEmergencyNominal = $safeBase * ($this->allocEmergencyPercent / 100);
        $allocExpansionNominal = $safeBase * ($this->allocExpansionPercent / 100);

        // Analisis Keamanan Penarikan Hak Pemilik (Owner Draw Safety)
        $maxSafeOwnerDraw = max(0, $allocOwnerNominal - $totalOverduePayable);
        $ownerDrawStatus = match (true) {
            $netProfit <= 0 => 'tahan_rugi',
            $totalOverduePayable > 0 => 'waspada_hutang_supplier',
            $dailyAverageSales < $bepOmzetHarian => 'waspada_bep',
            default => 'aman_ditarik',
        };

        // 8. Evaluasi Simulasi Investasi Aset / Freezer Baru
        $assetPrice = (float) $this->simAssetPrice;
        $estMonthlyRev = (float) $this->simEstimatedMonthlyRevenue;
        $extraElec = (float) $this->simExtraElectricity;
        $simMonthlyGrossProfit = $estMonthlyRev * $effectiveGrossMarginDecimal;
        $simMonthlyNetContribution = $simMonthlyGrossProfit - $extraElec;
        $simPaybackMonths = $simMonthlyNetContribution > 0 ? round($assetPrice / $simMonthlyNetContribution, 1) : 0;
        
        $simInvestmentFeasibility = match (true) {
            $simMonthlyNetContribution <= 0 => 'tidak_layak',
            $simPaybackMonths <= 12 && $netProfit > 0 => 'sangat_layak',
            $simPaybackMonths <= 24 => 'layak_dengan_catatan',
            default => 'evaluasi_ulang',
        };

        // 9. Evaluasi Simulasi Penggajian & Jumlah Karyawan
        $empCount = max(1, (int) $this->simEmployeeCount);
        $empSalaryPerPerson = (float) $this->simEmployeeSalary;
        $empAllowancePerPerson = (float) $this->simEmployeeAllowance;
        $totalPayrollPerPerson = $empSalaryPerPerson + $empAllowancePerPerson;
        $totalMonthlyPayrollBurden = $totalPayrollPerPerson * $empCount;

        $simRequiredSalesForEmployee = $effectiveGrossMarginDecimal > 0 ? $totalMonthlyPayrollBurden / $effectiveGrossMarginDecimal : 0;
        $simRequiredDailySalesForEmployee = $simRequiredSalesForEmployee / 30;
        $payrollToSalesRatio = $netSales > 0 ? ($totalMonthlyPayrollBurden / $netSales) * 100 : 0;

        // Data Karyawan Riil dari Master
        $realActiveEmployeesCount = Employee::where('is_active', true)->count();
        $realTotalBaseSalary = Employee::where('is_active', true)->sum('base_salary');

        // 10. Deteksi Deadstock & Rekomendasi Diskon
        $deadstockCandidates = Product::query()
            ->where('stock', '>=', 5)
            ->where('is_active', true)
            ->withCount(['transactionItems as units_sold_period' => function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->get()
            ->filter(function ($p) {
                return $p->units_sold_period < 2;
            })
            ->take(6);

        // 11. AI Overall Financial Health Score (0 - 100)
        $score = 50;
        if ($dailyAverageSales >= $bepOmzetHarian) $score += 20;
        else $score -= 15;

        if ($netProfit > 0) $score += 15;
        else $score -= 20;

        if ($totalOverduePayable == 0) $score += 10;
        else $score -= 15;

        if ($electricityRatio <= 12 && $electricityRatio > 0) $score += 5;

        $overallHealthScore = max(10, min(100, $score));

        return view('livewire.admin.finance.financial-ai-advisor', [
            'daysInPeriod' => $daysInPeriod,
            'totalTransactions' => $totalTransactions,
            'netSales' => $netSales,
            'totalHpp' => $totalHpp,
            'grossProfit' => $grossProfit,
            'grossMarginPercent' => $grossMarginPercent,
            'effectiveGrossMarginDecimal' => $effectiveGrossMarginDecimal,
            'dailyAverageSales' => $dailyAverageSales,
            'averageOrderValue' => $averageOrderValue,
            'totalOpex' => $totalOpex,
            'electricityOpex' => $electricityOpex,
            'electricityRatio' => $electricityRatio,
            'payrollOpex' => $payrollOpex,
            'packagingOpex' => $packagingOpex,
            'maintenanceOpex' => $maintenanceOpex,
            'rentOpex' => $rentOpex,
            'otherOpex' => $otherOpex,
            'dailyAverageOpex' => $dailyAverageOpex,
            'netProfit' => $netProfit,
            'netProfitMargin' => $netProfitMargin,
            'bepOmzetTotal' => $bepOmzetTotal,
            'bepOmzetHarian' => $bepOmzetHarian,
            'bepAchievementPercent' => $bepAchievementPercent,
            'bepStatus' => $bepStatus,
            'targetOmzetTotal' => $targetOmzetTotal,
            'targetOmzetHarian' => $targetOmzetHarian,
            'totalUnpaidPayable' => $totalUnpaidPayable,
            'totalOverduePayable' => $totalOverduePayable,
            'totalUnpaidReceivable' => $totalUnpaidReceivable,
            'totalInventoryAssetValue' => $totalInventoryAssetValue,
            'recommendedPurchaseCeilingMonthly' => $recommendedPurchaseCeilingMonthly,
            'recommendedPurchaseCeilingWeekly' => $recommendedPurchaseCeilingWeekly,
            'purchaseStatus' => $purchaseStatus,
            
            // Financial Split
            'allocRestockNominal' => $allocRestockNominal,
            'allocOpexNominal' => $allocOpexNominal,
            'allocPayrollNominal' => $allocPayrollNominal,
            'allocOwnerNominal' => $allocOwnerNominal,
            'allocEmergencyNominal' => $allocEmergencyNominal,
            'allocExpansionNominal' => $allocExpansionNominal,
            'maxSafeOwnerDraw' => $maxSafeOwnerDraw,
            'ownerDrawStatus' => $ownerDrawStatus,

            // Freezer Simulation
            'simMonthlyGrossProfit' => $simMonthlyGrossProfit,
            'simMonthlyNetContribution' => $simMonthlyNetContribution,
            'simPaybackMonths' => $simPaybackMonths,
            'simInvestmentFeasibility' => $simInvestmentFeasibility,

            // Payroll Simulation
            'empCount' => $empCount,
            'empSalaryPerPerson' => $empSalaryPerPerson,
            'empAllowancePerPerson' => $empAllowancePerPerson,
            'totalPayrollPerPerson' => $totalPayrollPerPerson,
            'totalMonthlyPayrollBurden' => $totalMonthlyPayrollBurden,
            'simRequiredSalesForEmployee' => $simRequiredSalesForEmployee,
            'simRequiredDailySalesForEmployee' => $simRequiredDailySalesForEmployee,
            'payrollToSalesRatio' => $payrollToSalesRatio,
            'realActiveEmployeesCount' => $realActiveEmployeesCount,
            'realTotalBaseSalary' => $realTotalBaseSalary,

            'deadstockCandidates' => $deadstockCandidates,
            'overallHealthScore' => $overallHealthScore,
        ]);
    }
}
