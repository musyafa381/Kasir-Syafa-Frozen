<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Payroll;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\ReceiptSetting;
use App\Models\Supplier;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReportExportController extends Controller
{
    /**
     * Helper to export data as CSV/Excel with UTF-8 BOM
     */
    private function exportCsv(string $filename, array $headers, array $rows): Response
    {
        $output = "\xEF\xBB\xBF"; // UTF-8 BOM for Excel
        $f = fopen('php://temp', 'r+');

        fputcsv($f, $headers);
        foreach ($rows as $row) {
            fputcsv($f, $row);
        }

        rewind($f);
        $output .= stream_get_contents($f);
        fclose($f);

        return response($output, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ]);
    }

    // =========================================================================
    // 1. FAKTUR PENJUALAN FORMAL (A4 / A5)
    // =========================================================================
    public function printFormalInvoice(int $id)
    {
        $transaction = Transaction::with(['items.product', 'user', 'customer', 'discount', 'tax'])->findOrFail($id);
        $receiptSetting = ReceiptSetting::getSettings();

        return view('print.invoice-formal', [
            'transaction' => $transaction,
            'receiptSetting' => $receiptSetting,
        ]);
    }

    // =========================================================================
    // 2. LAPORAN PENJUALAN (SALES REPORT)
    // =========================================================================
    public function printSalesReport(Request $request)
    {
        $startDate = $request->query('start_date', Carbon::today()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', Carbon::today()->toDateString());
        $paymentMethod = $request->query('payment_method');
        $cashierId = $request->query('cashier_id');

        $transactions = Transaction::query()
            ->completed()
            ->with(['user', 'customer', 'items'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->when($paymentMethod, fn($q) => $q->where('payment_method', $paymentMethod))
            ->when($cashierId, fn($q) => $q->where('user_id', $cashierId))
            ->latest()
            ->get();

        $totalOmset = $transactions->sum('grand_total');
        $totalHpp = 0;
        $totalQty = 0;

        foreach ($transactions as $trx) {
            foreach ($trx->items as $it) {
                $totalHpp += ($it->cost_price * $it->quantity);
                $totalQty += $it->quantity;
            }
        }

        $totalLabaKotor = $totalOmset - $totalHpp;
        $receiptSetting = ReceiptSetting::getSettings();

        return view('print.sales-report-print', [
            'transactions' => $transactions,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalOmset' => $totalOmset,
            'totalHpp' => $totalHpp,
            'totalLabaKotor' => $totalLabaKotor,
            'totalQty' => $totalQty,
            'receiptSetting' => $receiptSetting,
        ]);
    }

    public function exportSalesExcel(Request $request): Response
    {
        $startDate = $request->query('start_date', Carbon::today()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', Carbon::today()->toDateString());
        $paymentMethod = $request->query('payment_method');
        $cashierId = $request->query('cashier_id');

        $transactions = Transaction::query()
            ->completed()
            ->with(['user', 'customer', 'items'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->when($paymentMethod, fn($q) => $q->where('payment_method', $paymentMethod))
            ->when($cashierId, fn($q) => $q->where('user_id', $cashierId))
            ->latest()
            ->get();

        $headers = [
            'No',
            'No. Invoice',
            'Tanggal & Waktu',
            'Kasir',
            'Pelanggan',
            'Metode Pembayaran',
            'Total Qty',
            'Subtotal (Rp)',
            'Diskon (Rp)',
            'Pajak (Rp)',
            'Total Omset (Rp)',
            'Total HPP Modal (Rp)',
            'Laba Kotor (Rp)',
        ];

        $rows = [];
        $no = 1;

        foreach ($transactions as $trx) {
            $hpp = 0;
            $qty = 0;
            foreach ($trx->items as $it) {
                $hpp += ($it->cost_price * $it->quantity);
                $qty += $it->quantity;
            }
            $profit = $trx->grand_total - $hpp;

            $rows[] = [
                $no++,
                $trx->invoice_number,
                $trx->created_at->format('d/m/Y H:i'),
                $trx->user->name ?? 'Kasir',
                $trx->customer_name . ($trx->is_loyalty ? ' (Member)' : ''),
                ucfirst($trx->payment_method),
                $qty,
                $trx->subtotal,
                $trx->discount_amount,
                $trx->tax_amount,
                $trx->grand_total,
                $hpp,
                $profit,
            ];
        }

        $filename = "Laporan_Penjualan_{$startDate}_sampai_{$endDate}.csv";
        return $this->exportCsv($filename, $headers, $rows);
    }

    // =========================================================================
    // 3. LAPORAN LABA RUGI KOMPREHENSIF (PROFIT & LOSS)
    // =========================================================================
    public function printProfitLoss(Request $request)
    {
        $startDate = $request->query('start_date', Carbon::today()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', Carbon::today()->toDateString());

        $transactions = Transaction::query()
            ->completed()
            ->with(['items'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->get();

        $totalRevenue = $transactions->sum('grand_total');
        $totalCogs = 0;

        foreach ($transactions as $trx) {
            foreach ($trx->items as $it) {
                $totalCogs += ($it->cost_price * $it->quantity);
            }
        }

        $grossProfit = $totalRevenue - $totalCogs;

        $expenses = Expense::query()
            ->whereDate('expense_date', '>=', $startDate)
            ->whereDate('expense_date', '<=', $endDate)
            ->orderBy('expense_date')
            ->get();

        $totalExpenses = $expenses->sum('amount');
        $netProfit = $grossProfit - $totalExpenses;
        $receiptSetting = ReceiptSetting::getSettings();

        return view('print.profit-loss-print', [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalRevenue' => $totalRevenue,
            'totalCogs' => $totalCogs,
            'grossProfit' => $grossProfit,
            'expenses' => $expenses,
            'totalExpenses' => $totalExpenses,
            'netProfit' => $netProfit,
            'receiptSetting' => $receiptSetting,
        ]);
    }

    public function exportProfitLossExcel(Request $request): Response
    {
        $startDate = $request->query('start_date', Carbon::today()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', Carbon::today()->toDateString());

        $transactions = Transaction::query()
            ->completed()
            ->with(['items'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->get();

        $totalRevenue = $transactions->sum('grand_total');
        $totalCogs = 0;
        foreach ($transactions as $trx) {
            foreach ($trx->items as $it) {
                $totalCogs += ($it->cost_price * $it->quantity);
            }
        }
        $grossProfit = $totalRevenue - $totalCogs;

        $expenses = Expense::query()
            ->whereDate('expense_date', '>=', $startDate)
            ->whereDate('expense_date', '<=', $endDate)
            ->get();
        $totalExpenses = $expenses->sum('amount');
        $netProfit = $grossProfit - $totalExpenses;

        $headers = ['Komponen Keuangan', 'Kategori / Rincian', 'Jumlah (Rp)'];
        $rows = [
            ['PENDAPATAN (REVENUE)', 'Total Penjualan Kotor (Gross Sales)', $totalRevenue],
            ['HARGA POKOK PENJUALAN (HPP)', 'Total Biaya Modal Produk Terjual (COGS)', -$totalCogs],
            ['LABA KOTOR (GROSS PROFIT)', 'Penjualan - HPP Modal', $grossProfit],
            ['---', '---', '---'],
            ['BIAYA OPERASIONAL (OPEX)', 'Rincian Pengeluaran Toko:', ''],
        ];

        foreach ($expenses as $exp) {
            $rows[] = [
                'Biaya Operasional',
                $exp->title . ($exp->category ? ' (' . $exp->category . ')' : ''),
                -$exp->amount,
            ];
        }

        $rows[] = ['TOTAL BIAYA OPERASIONAL', 'Akumulasi Beban Operasional', -$totalExpenses];
        $rows[] = ['---', '---', '---'];
        $rows[] = ['LABA BERSIH RIIL (NET PROFIT)', 'Laba Kotor - Biaya Operasional', $netProfit];

        $filename = "Laporan_Laba_Rugi_{$startDate}_sampai_{$endDate}.csv";
        return $this->exportCsv($filename, $headers, $rows);
    }

    // =========================================================================
    // 4. VALUASI NILAI STOK & HPP (INVENTORY VALUATION)
    // =========================================================================
    public function printInventoryValuation(Request $request)
    {
        $categoryId = $request->query('category_id');
        $outletId = $request->query('outlet_id');

        $products = Product::with(['category', 'outletProducts'])
            ->when($categoryId, fn($q) => $q->where('category_id', $categoryId))
            ->orderBy('name')
            ->get();

        $totalStockQty = 0;
        $totalAssetValue = 0;
        $totalPotentialRevenue = 0;

        foreach ($products as $p) {
            if ($outletId) {
                $op = $p->getOutletProduct((int) $outletId);
                $p->display_stock = max(0, (int) ($op?->stock ?? 0));
                $p->display_cost = max(0, (float) ($op?->cost_price ?: $p->cost_price));
                $p->display_sell = max(0, (float) ($op?->selling_price ?: $p->selling_price));
            } else {
                $hasOps = $p->outletProducts->isNotEmpty();
                $p->display_stock = $hasOps ? max(0, (int) $p->outletProducts->sum('stock')) : max(0, (int) $p->stock);
                $firstOpCost = $p->outletProducts->firstWhere('cost_price', '>', 0)?->cost_price;
                $p->display_cost = max(0, (float) ($p->cost_price ?: ($firstOpCost ?? 0)));
                $firstOpSell = $p->outletProducts->firstWhere('selling_price', '>', 0)?->selling_price;
                $p->display_sell = max(0, (float) ($p->selling_price ?: ($firstOpSell ?? 0)));
            }

            $totalStockQty += $p->display_stock;
            $totalAssetValue += ($p->display_stock * $p->display_cost);
            $totalPotentialRevenue += ($p->display_stock * $p->display_sell);
        }

        $totalPotentialProfit = max(0, $totalPotentialRevenue - $totalAssetValue);
        $receiptSetting = ReceiptSetting::getSettings();
        $outlet = $outletId ? Outlet::find($outletId) : null;

        return view('print.inventory-valuation-print', [
            'products' => $products,
            'totalStockQty' => $totalStockQty,
            'totalAssetValue' => $totalAssetValue,
            'totalPotentialRevenue' => $totalPotentialRevenue,
            'totalPotentialProfit' => $totalPotentialProfit,
            'receiptSetting' => $receiptSetting,
            'outlet' => $outlet,
        ]);
    }

    public function exportInventoryValuationExcel(Request $request): Response
    {
        $categoryId = $request->query('category_id');
        $outletId = $request->query('outlet_id');

        $products = Product::with(['category', 'outletProducts'])
            ->when($categoryId, fn($q) => $q->where('category_id', $categoryId))
            ->orderBy('name')
            ->get();

        $headers = [
            'No',
            'Kode / Barcode',
            'Nama Produk Frozen',
            'Kategori',
            'Stok Unit',
            'Harga Modal HPP (Rp)',
            'Harga Jual Normal (Rp)',
            'Harga Jual Member (Rp)',
            'Total Nilai Aset HPP (Rp)',
            'Estimasi Total Omset (Rp)',
            'Potensi Laba (Rp)',
        ];

        $rows = [];
        $no = 1;

        foreach ($products as $p) {
            if ($outletId) {
                $op = $p->getOutletProduct((int) $outletId);
                $stock = max(0, (int) ($op?->stock ?? 0));
                $cost = max(0, (float) ($op?->cost_price ?: $p->cost_price));
                $sell = max(0, (float) ($op?->selling_price ?: $p->selling_price));
                $memberSell = max(0, (float) ($op?->loyalty_price ?: $p->loyalty_price ?: $sell));
            } else {
                $hasOps = $p->outletProducts->isNotEmpty();
                $stock = $hasOps ? max(0, (int) $p->outletProducts->sum('stock')) : max(0, (int) $p->stock);
                $firstOpCost = $p->outletProducts->firstWhere('cost_price', '>', 0)?->cost_price;
                $cost = max(0, (float) ($p->cost_price ?: ($firstOpCost ?? 0)));
                $firstOpSell = $p->outletProducts->firstWhere('selling_price', '>', 0)?->selling_price;
                $sell = max(0, (float) ($p->selling_price ?: ($firstOpSell ?? 0)));
                $firstOpLoyalty = $p->outletProducts->firstWhere('loyalty_price', '>', 0)?->loyalty_price;
                $memberSell = max(0, (float) ($p->loyalty_price ?: ($firstOpLoyalty ?? $sell)));
            }

            $assetHpp = $stock * $cost;
            $potentialOmset = $stock * $sell;
            $potentialProfit = max(0, $potentialOmset - $assetHpp);

            $rows[] = [
                $no++,
                $p->barcode ?: $p->sku ?: '-',
                $p->name,
                $p->category->name ?? 'Tanpa Kategori',
                $stock,
                $cost,
                $sell,
                $memberSell,
                $assetHpp,
                $potentialOmset,
                $potentialProfit,
            ];
        }

        $outletSuffix = $outletId ? "_Cabang_" . $outletId : "_Semua_Cabang";
        $filename = "Valuasi_Stok_Aset_Syafa_Frozen" . $outletSuffix . "_" . date('Y-m-d') . ".csv";
        return $this->exportCsv($filename, $headers, $rows);
    }

    // =========================================================================
    // 5. FORMULIR STOCK OPNAME FISIK FREEZER
    // =========================================================================
    public function printStockOpnameSheet(Request $request)
    {
        $categoryId = $request->query('category_id');
        $products = Product::with('category')
            ->when($categoryId, fn($q) => $q->where('category_id', $categoryId))
            ->orderBy('category_id')
            ->orderBy('name')
            ->get();

        $receiptSetting = ReceiptSetting::getSettings();

        return view('print.stock-opname-sheet', [
            'products' => $products,
            'receiptSetting' => $receiptSetting,
            'printDate' => Carbon::now(),
        ]);
    }

    // =========================================================================
    // 6. PIUTANG PELANGGAN & SURAT TAGIHAN
    // =========================================================================
    public function printReceivableInvoice(int $id)
    {
        $transaction = Transaction::with(['items.product', 'customer', 'user'])->findOrFail($id);
        $receiptSetting = ReceiptSetting::getSettings();

        return view('print.receivable-invoice-print', [
            'transaction' => $transaction,
            'receiptSetting' => $receiptSetting,
        ]);
    }

    public function printReceivableBook(Request $request)
    {
        $status = $request->query('status');
        $customerId = $request->query('customer_id');

        $transactions = Transaction::query()
            ->where('payment_method', 'tempo')
            ->with(['customer', 'user'])
            ->when($status, fn($q) => $q->where('payment_status', $status))
            ->when($customerId, fn($q) => $q->where('customer_id', $customerId))
            ->orderBy('due_date')
            ->get();

        $totalReceivable = $transactions->sum('grand_total');
        $totalPaid = $transactions->sum('paid_amount');
        $totalRemaining = $transactions->sum('remaining_amount');
        $receiptSetting = ReceiptSetting::getSettings();

        return view('print.receivable-book-print', [
            'transactions' => $transactions,
            'totalReceivable' => $totalReceivable,
            'totalPaid' => $totalPaid,
            'totalRemaining' => $totalRemaining,
            'receiptSetting' => $receiptSetting,
        ]);
    }

    public function exportReceivablesExcel(Request $request): Response
    {
        $status = $request->query('status');
        $customerId = $request->query('customer_id');

        $transactions = Transaction::query()
            ->where('payment_method', 'tempo')
            ->with(['customer', 'user'])
            ->when($status, fn($q) => $q->where('payment_status', $status))
            ->when($customerId, fn($q) => $q->where('customer_id', $customerId))
            ->orderBy('due_date')
            ->get();

        $headers = [
            'No',
            'No. Invoice',
            'Nama Pelanggan',
            'No. Telp / WA',
            'Tanggal Transaksi',
            'Jatuh Tempo',
            'Total Tagihan (Rp)',
            'Sudah Dibayar (Rp)',
            'Sisa Piutang (Rp)',
            'Status Pembayaran',
        ];

        $rows = [];
        $no = 1;

        foreach ($transactions as $trx) {
            $statusLabel = match ($trx->payment_status) {
                'paid' => 'Lunas',
                'partial' => 'Dicicil Sebagian',
                default => 'Belum Bayar',
            };

            $rows[] = [
                $no++,
                $trx->invoice_number,
                $trx->customer_name,
                $trx->customer?->phone ?: '-',
                $trx->created_at->format('d/m/Y'),
                $trx->due_date ? $trx->due_date->format('d/m/Y') : '-',
                $trx->grand_total,
                $trx->paid_amount,
                $trx->remaining_amount,
                $statusLabel,
            ];
        }

        $filename = "Buku_Piutang_Pelanggan_" . date('Y-m-d') . ".csv";
        return $this->exportCsv($filename, $headers, $rows);
    }

    // =========================================================================
    // 7. SURAT PESANAN (PURCHASE ORDER) & BUKU HUTANG SUPPLIER
    // =========================================================================
    public function printPurchaseOrder(int $id)
    {
        $purchase = Purchase::with(['supplier', 'items.product', 'user'])->findOrFail($id);
        $receiptSetting = ReceiptSetting::getSettings();

        return view('print.purchase-order-print', [
            'purchase' => $purchase,
            'receiptSetting' => $receiptSetting,
        ]);
    }

    public function printAccountsPayable(Request $request)
    {
        $status = $request->query('status');
        $supplierId = $request->query('supplier_id');

        $purchases = Purchase::query()
            ->where('payment_type', 'tempo')
            ->with(['supplier', 'user'])
            ->when($status, fn($q) => $q->where('payment_status', $status))
            ->when($supplierId, fn($q) => $q->where('supplier_id', $supplierId))
            ->orderBy('due_date')
            ->get();

        $totalPayable = $purchases->sum('total_amount');
        $totalPaid = $purchases->sum('paid_amount');
        $totalRemaining = $purchases->sum('remaining_amount');
        $receiptSetting = ReceiptSetting::getSettings();

        return view('print.accounts-payable-print', [
            'purchases' => $purchases,
            'totalPayable' => $totalPayable,
            'totalPaid' => $totalPaid,
            'totalRemaining' => $totalRemaining,
            'receiptSetting' => $receiptSetting,
        ]);
    }

    public function exportAccountsPayableExcel(Request $request): Response
    {
        $status = $request->query('status');
        $supplierId = $request->query('supplier_id');

        $purchases = Purchase::query()
            ->where('payment_type', 'tempo')
            ->with(['supplier', 'user'])
            ->when($status, fn($q) => $q->where('payment_status', $status))
            ->when($supplierId, fn($q) => $q->where('supplier_id', $supplierId))
            ->orderBy('due_date')
            ->get();

        $headers = [
            'No',
            'No. Faktur Masuk',
            'Nama Supplier / Distributor',
            'No. Telp Supplier',
            'Tanggal Pembelian',
            'Jatuh Tempo',
            'Total Hutang (Rp)',
            'Sudah Dibayar (Rp)',
            'Sisa Hutang (Rp)',
            'Status Pembayaran',
        ];

        $rows = [];
        $no = 1;

        foreach ($purchases as $p) {
            $statusLabel = match ($p->payment_status) {
                'paid' => 'Lunas',
                'partial' => 'Dicicil Sebagian',
                default => 'Belum Bayar',
            };

            $rows[] = [
                $no++,
                $p->invoice_number,
                $p->supplier?->name ?? 'Supplier Umum',
                $p->supplier?->phone ?? '-',
                $p->created_at->format('d/m/Y'),
                $p->due_date ? $p->due_date->format('d/m/Y') : '-',
                $p->total_amount,
                $p->paid_amount,
                $p->remaining_amount,
                $statusLabel,
            ];
        }

        $filename = "Buku_Hutang_Supplier_" . date('Y-m-d') . ".csv";
        return $this->exportCsv($filename, $headers, $rows);
    }

    // =========================================================================
    // 8. REKAPITULASI PENGGAJIAN KARYAWAN (PAYROLL SUMMARY)
    // =========================================================================
    public function printPayrollSummary(Request $request)
    {
        $month = $request->query('month', date('n'));
        $year = $request->query('year', date('Y'));

        $payrolls = Payroll::with(['employee', 'creator'])
            ->where('period_month', $month)
            ->where('period_year', $year)
            ->get();

        $totalBaseSalary = $payrolls->sum('base_salary');
        $totalAllowance = $payrolls->sum('allowance_amount');
        $totalDeductions = $payrolls->sum(fn($p) => $p->loan_deduction + $p->other_deduction);
        $totalNetSalary = $payrolls->sum('net_salary');
        $receiptSetting = ReceiptSetting::getSettings();

        return view('print.payroll-summary-print', [
            'payrolls' => $payrolls,
            'month' => $month,
            'year' => $year,
            'totalBaseSalary' => $totalBaseSalary,
            'totalAllowance' => $totalAllowance,
            'totalDeductions' => $totalDeductions,
            'totalNetSalary' => $totalNetSalary,
            'receiptSetting' => $receiptSetting,
        ]);
    }

    public function exportPayrollSummaryExcel(Request $request): Response
    {
        $month = $request->query('month', date('n'));
        $year = $request->query('year', date('Y'));

        $payrolls = Payroll::with(['employee'])
            ->where('period_month', $month)
            ->where('period_year', $year)
            ->get();

        $headers = [
            'No',
            'No. Slip Gaji',
            'Kode Karyawan',
            'Nama Karyawan',
            'Jabatan',
            'Gaji Pokok (Rp)',
            'Tunjangan / Bonus (Rp)',
            'Potongan Kasbon (Rp)',
            'Potongan Lain (Rp)',
            'Total Gaji Bersih (Rp)',
            'Metode Pembayaran',
            'Tanggal Bayar',
        ];

        $rows = [];
        $no = 1;

        foreach ($payrolls as $p) {
            $rows[] = [
                $no++,
                $p->payroll_number,
                $p->employee->employee_code,
                $p->employee->name,
                $p->employee->position,
                $p->base_salary,
                $p->allowance_amount,
                $p->loan_deduction,
                $p->other_deduction,
                $p->net_salary,
                strtoupper($p->payment_method),
                $p->payment_date->format('d/m/Y'),
            ];
        }

        $filename = "Rekap_Gaji_Karyawan_{$month}_{$year}.csv";
        return $this->exportCsv($filename, $headers, $rows);
    }

    // =========================================================================
    // 9. TEMPLATE IMPORT PRODUK (EXCEL COMPATIBLE CSV)
    // =========================================================================
    public function downloadProductTemplate(\App\Services\ProductImportService $importService): Response
    {
        $content = $importService->generateTemplate();
        $filename = "Template_Import_Produk_SyafaFrozen.csv";

        return response($content, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ]);
    }
}
