<?php

namespace App\Livewire\Admin\Backup;

use App\Models\BackupLog;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Backup Data Transaksi & Google Drive - Syafa Frozen Food')]
class BackupManager extends Component
{
    use WithPagination;

    // Filter & Generator State
    public string $backupMode = 'monthly'; // 'monthly' or 'custom'
    public int $selectedMonth;
    public int $selectedYear;
    public string $startDate = '';
    public string $endDate = '';
    public string $backupFormat = 'excel'; // 'excel', 'csv', or 'json'
    public string $googleDriveFolderUrl = '';

    public function mount(): void
    {
        $now = Carbon::now();
        $this->selectedMonth = (int) $now->format('m');
        $this->selectedYear = (int) $now->format('Y');
        $this->startDate = $now->copy()->startOfMonth()->toDateString();
        $this->endDate = $now->toDateString();
        $this->googleDriveFolderUrl = session('syafa_gdrive_folder_url', 'https://drive.google.com/drive/my-drive');
    }

    public function saveGoogleDriveUrl(): void
    {
        session(['syafa_gdrive_folder_url' => $this->googleDriveFolderUrl]);
        session()->flash('success', 'Tautan Folder Google Drive toko berhasil diperbarui.');
    }

    public function generateBackup()
    {
        if ($this->backupMode === 'monthly') {
            $start = Carbon::createFromDate($this->selectedYear, $this->selectedMonth, 1)->startOfMonth();
            $end = $start->copy()->endOfMonth();
            $monthName = $start->translatedFormat('F_Y');
            $periodLabel = $start->translatedFormat('F Y');
        } else {
            $this->validate([
                'startDate' => 'required|date',
                'endDate' => 'required|date|after_or_equal:startDate',
            ]);
            $start = Carbon::parse($this->startDate)->startOfDay();
            $end = Carbon::parse($this->endDate)->endOfDay();
            $monthName = $start->format('Ymd') . '_sd_' . $end->format('Ymd');
            $periodLabel = $start->translatedFormat('d M Y') . ' - ' . $end->translatedFormat('d M Y');
        }

        // Fetch transactions in range
        $transactions = Transaction::query()
            ->completed()
            ->with(['items.product.category', 'user', 'customer', 'discount', 'tax'])
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at', 'asc')
            ->get();

        if ($transactions->isEmpty()) {
            session()->flash('error', "Tidak ada data transaksi penjualan pada periode {$periodLabel}.");
            return;
        }

        $totalTransactions = $transactions->count();
        $totalAmount = (float) $transactions->sum('grand_total');
        $timestamp = Carbon::now()->format('Ymd_His');

        // Ensure backups directory exists
        if (!Storage::disk('local')->exists('backups')) {
            Storage::disk('local')->makeDirectory('backups');
        }

        if ($this->backupFormat === 'json') {
            $filename = "Backup_Transaksi_SyafaFrozen_{$monthName}_{$timestamp}.json";
            $relativePath = "backups/{$filename}";

            $exportData = [
                'app_name' => 'Syafa Frozen Food Management System',
                'backup_type' => 'Transactions & Sales Archive',
                'period' => $periodLabel,
                'start_date' => $start->toDateString(),
                'end_date' => $end->toDateString(),
                'generated_at' => Carbon::now()->toIso8601String(),
                'generated_by' => auth()->user()->name ?? 'Admin',
                'total_transactions' => $totalTransactions,
                'total_sales_amount' => $totalAmount,
                'transactions' => $transactions->map(function ($t) {
                    return [
                        'invoice_number' => $t->invoice_number,
                        'date' => $t->created_at->format('Y-m-d H:i:s'),
                        'cashier' => $t->user?->name ?? 'Kasir',
                        'customer' => $t->customer?->name ?? ($t->customer_name ?: 'Umum'),
                        'payment_method' => $t->payment_method,
                        'payment_status' => $t->payment_status,
                        'due_date' => $t->due_date?->format('Y-m-d'),
                        'subtotal' => (float) $t->subtotal,
                        'discount_amount' => (float) $t->discount_amount,
                        'tax_amount' => (float) $t->tax_amount,
                        'grand_total' => (float) $t->grand_total,
                        'paid_amount' => (float) $t->paid_amount,
                        'change_amount' => (float) $t->change_amount,
                        'notes' => $t->notes,
                        'items' => $t->items->map(function ($item) {
                            return [
                                'product_code' => $item->product?->code ?? '-',
                                'product_name' => $item->product_name ?? $item->product?->name,
                                'category' => $item->product?->category?->name ?? 'Uncategorized',
                                'quantity' => (int) $item->quantity,
                                'unit' => $item->product?->unit ?? 'pcs',
                                'price' => (float) $item->price,
                                'cost_price' => (float) $item->cost_price,
                                'subtotal' => (float) $item->subtotal,
                                'gross_profit' => ((float) $item->price - (float) $item->cost_price) * (int) $item->quantity,
                            ];
                        }),
                    ];
                }),
            ];

            Storage::disk('local')->put($relativePath, json_encode($exportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        } elseif ($this->backupFormat === 'csv') {
            // Semicolon-separated CSV with BOM and sep=; for Windows Excel
            $filename = "Backup_Transaksi_SyafaFrozen_{$monthName}_{$timestamp}.csv";
            $relativePath = "backups/{$filename}";

            $handle = fopen('php://temp', 'r+');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
            fwrite($handle, "sep=;\r\n"); // Force Excel to use semicolon delimiter

            fputcsv($handle, [
                'No',
                'No. Invoice',
                'Tanggal Transaksi',
                'Waktu',
                'Kasir',
                'Pelanggan',
                'Metode Pembayaran',
                'Status Pembayaran',
                'Jatuh Tempo (Tempo)',
                'Kode Produk',
                'Nama Produk Frozen',
                'Kategori',
                'Qty',
                'Satuan',
                'Harga Jual Satuan (Rp)',
                'Modal HPP Satuan (Rp)',
                'Subtotal Item (Rp)',
                'Laba Kotor Item (Rp)',
                'Diskon Nota (Rp)',
                'Pajak PPN Nota (Rp)',
                'Grand Total Nota (Rp)',
                'Catatan',
            ], ';');

            $rowNumber = 1;
            foreach ($transactions as $t) {
                $trxDate = $t->created_at ? $t->created_at->format('Y-m-d') : '-';
                $trxTime = $t->created_at ? $t->created_at->format('H:i:s') : '-';
                $cashierName = $t->user?->name ?? 'Kasir';
                $customerName = $t->customer?->name ?? ($t->customer_name ?: 'Pelanggan Umum');
                $dueDate = $t->due_date ? $t->due_date->format('Y-m-d') : '-';

                foreach ($t->items as $item) {
                    $itemQty = (int) $item->quantity;
                    $itemPrice = (float) $item->price;
                    $itemHpp = (float) $item->cost_price;
                    $itemSubtotal = (float) $item->subtotal;
                    $itemGrossProfit = ($itemPrice - $itemHpp) * $itemQty;

                    fputcsv($handle, [
                        $rowNumber++,
                        $t->invoice_number,
                        $trxDate,
                        $trxTime,
                        $cashierName,
                        $customerName,
                        strtoupper($t->payment_method),
                        strtoupper($t->payment_status),
                        $dueDate,
                        $item->product?->code ?? '-',
                        $item->product_name ?? $item->product?->name ?? '-',
                        $item->product?->category?->name ?? 'Uncategorized',
                        $itemQty,
                        $item->product?->unit ?? 'pcs',
                        $itemPrice,
                        $itemHpp,
                        $itemSubtotal,
                        $itemGrossProfit,
                        (float) $t->discount_amount,
                        (float) $t->tax_amount,
                        (float) $t->grand_total,
                        $t->notes ?? '',
                    ], ';');
                }
            }

            rewind($handle);
            $csvContent = stream_get_contents($handle);
            fclose($handle);

            Storage::disk('local')->put($relativePath, $csvContent);
        } else {
            // Default & Recommended: Professional Formatted Excel Spreadsheet (.xls)
            $filename = "Backup_Transaksi_SyafaFrozen_{$monthName}_{$timestamp}.xls";
            $relativePath = "backups/{$filename}";

            $excelContent = $this->generateExcelHtmlTable($transactions, $periodLabel, $totalTransactions, $totalAmount);
            Storage::disk('local')->put($relativePath, $excelContent);
        }

        $fullPath = Storage::disk('local')->path($relativePath);
        $fileSize = file_exists($fullPath) ? filesize($fullPath) : strlen(Storage::disk('local')->get($relativePath));

        // Create log record
        $log = BackupLog::create([
            'filename' => $filename,
            'file_path' => $relativePath,
            'file_size' => $fileSize,
            'period_label' => $periodLabel,
            'start_date' => $start->toDateString(),
            'end_date' => $end->toDateString(),
            'total_transactions' => $totalTransactions,
            'total_amount' => $totalAmount,
            'backup_format' => $this->backupFormat,
            'google_drive_link' => $this->googleDriveFolderUrl,
            'user_id' => auth()->id(),
        ]);

        session()->flash('success', "Arsip backup transaksi periode {$periodLabel} ({$totalTransactions} transaksi) berhasil dibuat.");
        
        // Return download trigger
        return $this->downloadBackup($log->id);
    }

    private function generateExcelHtmlTable($transactions, string $periodLabel, int $totalTransactions, float $totalAmount): string
    {
        $generatedDate = Carbon::now()->translatedFormat('d F Y, H:i');
        $generatedBy = auth()->user()->name ?? 'Admin';

        $html = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
        $html .= '<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
        $html .= '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Arsip Transaksi</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->';
        $html .= '<style>';
        $html .= 'body { font-family: Calibri, Arial, sans-serif; font-size: 11pt; color: #1e293b; }';
        $html .= 'table { border-collapse: collapse; width: 100%; }';
        $html .= '.header-title { font-size: 16pt; font-weight: bold; color: #1e3a8a; text-align: left; }';
        $html .= '.header-sub { font-size: 10pt; color: #475569; margin-bottom: 12px; }';
        $html .= 'th { background-color: #1e40af; color: #ffffff; font-weight: bold; font-size: 10pt; border: 1px solid #94a3b8; padding: 10px 8px; text-align: center; vertical-align: middle; }';
        $html .= 'td { border: 1px solid #cbd5e1; padding: 7px 8px; vertical-align: middle; font-size: 10pt; }';
        $html .= '.zebra { background-color: #f8fafc; }';
        $html .= '.total-row td { background-color: #e2e8f0; font-weight: bold; border-top: 2px solid #64748b; border-bottom: 2px solid #64748b; font-size: 11pt; }';
        $html .= '.text-center { text-align: center; }';
        $html .= '.text-right { text-align: right; }';
        $html .= '.text-left { text-align: left; }';
        $html .= '.bold { font-weight: bold; }';
        $html .= '.num { mso-number-format:"\#\,\#\#0"; }';
        $html .= '.date-col { mso-number-format:"yyyy-mm-dd"; text-align: center; }';
        $html .= '.time-col { mso-number-format:"hh\:mm\:ss"; text-align: center; }';
        $html .= '</style></head><body>';

        // Title Block
        $html .= '<table>';
        $html .= '<tr><td colspan="22" class="header-title" style="border:none; padding: 10px 0 2px 0;">SYAFA FROZEN FOOD - LAPORAN ARSIP TRANSAKSI & PENJUALAN</td></tr>';
        $html .= '<tr><td colspan="22" class="header-sub" style="border:none; padding-bottom: 15px;">Periode: <strong>' . htmlspecialchars($periodLabel) . '</strong> &nbsp;|&nbsp; Dibuat Pada: ' . $generatedDate . ' &nbsp;|&nbsp; Oleh: ' . htmlspecialchars($generatedBy) . ' &nbsp;|&nbsp; Total: ' . number_format($totalTransactions) . ' Transaksi (Rp ' . number_format($totalAmount, 0, ',', '.') . ')</td></tr>';
        $html .= '<tr><td colspan="22" style="border:none; height: 10px;"></td></tr>';

        // Table Header
        $html .= '<tr>';
        $html .= '<th style="width: 40px;">No</th>';
        $html .= '<th style="width: 140px;">No. Invoice</th>';
        $html .= '<th style="width: 90px;">Tanggal</th>';
        $html .= '<th style="width: 70px;">Waktu</th>';
        $html .= '<th style="width: 130px;">Kasir</th>';
        $html .= '<th style="width: 140px;">Pelanggan</th>';
        $html .= '<th style="width: 80px;">Metode Bayar</th>';
        $html .= '<th style="width: 90px;">Status Bayar</th>';
        $html .= '<th style="width: 90px;">Jatuh Tempo</th>';
        $html .= '<th style="width: 90px;">Kode Produk</th>';
        $html .= '<th style="width: 220px;">Nama Produk Frozen</th>';
        $html .= '<th style="width: 130px;">Kategori</th>';
        $html .= '<th style="width: 50px;">Qty</th>';
        $html .= '<th style="width: 60px;">Satuan</th>';
        $html .= '<th style="width: 110px;">Harga Jual Satuan (Rp)</th>';
        $html .= '<th style="width: 110px;">Modal HPP Satuan (Rp)</th>';
        $html .= '<th style="width: 110px;">Subtotal Item (Rp)</th>';
        $html .= '<th style="width: 110px;">Laba Kotor Item (Rp)</th>';
        $html .= '<th style="width: 90px;">Diskon Nota (Rp)</th>';
        $html .= '<th style="width: 90px;">PPN Nota (Rp)</th>';
        $html .= '<th style="width: 120px;">Grand Total Nota (Rp)</th>';
        $html .= '<th style="width: 160px;">Catatan</th>';
        $html .= '</tr>';

        $rowNum = 1;
        $sumQty = 0;
        $sumSubtotal = 0;
        $sumGrossProfit = 0;

        foreach ($transactions as $t) {
            $trxDate = $t->created_at ? $t->created_at->format('Y-m-d') : '-';
            $trxTime = $t->created_at ? $t->created_at->format('H:i:s') : '-';
            $cashierName = htmlspecialchars($t->user?->name ?? 'Kasir');
            $customerName = htmlspecialchars($t->customer?->name ?? ($t->customer_name ?: 'Pelanggan Umum'));
            $dueDate = $t->due_date ? $t->due_date->format('Y-m-d') : '-';
            $invoice = htmlspecialchars($t->invoice_number);
            $payMethod = strtoupper($t->payment_method);
            $payStatus = strtoupper($t->payment_status);
            $discountAmt = (float) $t->discount_amount;
            $taxAmt = (float) $t->tax_amount;
            $grandTotal = (float) $t->grand_total;
            $notes = htmlspecialchars($t->notes ?? '');

            foreach ($t->items as $item) {
                $qty = (int) $item->quantity;
                $price = (float) $item->price;
                $hpp = (float) $item->cost_price;
                $subtotal = (float) $item->subtotal;
                $grossProfit = ($price - $hpp) * $qty;

                $sumQty += $qty;
                $sumSubtotal += $subtotal;
                $sumGrossProfit += $grossProfit;

                $zebraClass = ($rowNum % 2 === 0) ? ' class="zebra"' : '';

                $html .= "<tr{$zebraClass}>";
                $html .= "<td class=\"text-center\">{$rowNum}</td>";
                $html .= "<td class=\"bold text-left\">{$invoice}</td>";
                $html .= "<td class=\"date-col\">{$trxDate}</td>";
                $html .= "<td class=\"time-col\">{$trxTime}</td>";
                $html .= "<td class=\"text-left\">{$cashierName}</td>";
                $html .= "<td class=\"text-left\">{$customerName}</td>";
                $html .= "<td class=\"text-center bold\">{$payMethod}</td>";
                $html .= "<td class=\"text-center\">{$payStatus}</td>";
                $html .= "<td class=\"text-center\">{$dueDate}</td>";
                $html .= "<td class=\"text-center\">" . htmlspecialchars($item->product?->code ?? '-') . "</td>";
                $html .= "<td class=\"text-left bold\">" . htmlspecialchars($item->product_name ?? $item->product?->name ?? '-') . "</td>";
                $html .= "<td class=\"text-left\">" . htmlspecialchars($item->product?->category?->name ?? 'Uncategorized') . "</td>";
                $html .= "<td class=\"text-center bold num\">{$qty}</td>";
                $html .= "<td class=\"text-center\">" . htmlspecialchars($item->product?->unit ?? 'pcs') . "</td>";
                $html .= "<td class=\"text-right num\">{$price}</td>";
                $html .= "<td class=\"text-right num\">{$hpp}</td>";
                $html .= "<td class=\"text-right bold num\">{$subtotal}</td>";
                $html .= "<td class=\"text-right num\" style=\"color: #047857;\">{$grossProfit}</td>";
                $html .= "<td class=\"text-right num\">{$discountAmt}</td>";
                $html .= "<td class=\"text-right num\">{$taxAmt}</td>";
                $html .= "<td class=\"text-right bold num\" style=\"color: #1e3a8a;\">{$grandTotal}</td>";
                $html .= "<td class=\"text-left\">{$notes}</td>";
                $html .= "</tr>";

                $rowNum++;
            }
        }

        // Summary Row at Bottom
        $html .= '<tr class="total-row">';
        $html .= '<td colspan="12" class="text-right bold" style="padding: 10px;">TOTAL REKAPITULASI PENJUALAN:</td>';
        $html .= "<td class=\"text-center bold num\">{$sumQty}</td>";
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= '<td></td>';
        $html .= "<td class=\"text-right bold num\">{$sumSubtotal}</td>";
        $html .= "<td class=\"text-right bold num\" style=\"color: #047857;\">{$sumGrossProfit}</td>";
        $html .= '<td colspan="2"></td>';
        $html .= "<td class=\"text-right bold num\" style=\"color: #1e3a8a; font-size: 12pt;\">{$totalAmount}</td>";
        $html .= '<td></td>';
        $html .= '</tr>';

        $html .= '</table>';
        $html .= '</body></html>';

        return $html;
    }

    public function downloadBackup(int $id)
    {
        $log = BackupLog::findOrFail($id);

        if (!Storage::disk('local')->exists($log->file_path)) {
            session()->flash('error', 'File backup fisik tidak ditemukan di server.');
            return;
        }

        $contentType = match ($log->backup_format) {
            'json' => 'application/json; charset=UTF-8',
            'csv' => 'text/csv; charset=UTF-8',
            default => 'application/vnd.ms-excel; charset=UTF-8',
        };

        return response()->download(
            Storage::disk('local')->path($log->file_path),
            $log->filename,
            ['Content-Type' => $contentType]
        );
    }

    public function deleteBackup(int $id): void
    {
        $log = BackupLog::findOrFail($id);

        if (Storage::disk('local')->exists($log->file_path)) {
            Storage::disk('local')->delete($log->file_path);
        }

        $log->delete();
        $msg = "Arsip file {$log->filename} berhasil dihapus.";
        session()->flash('success', $msg);
        $this->dispatch('toast', ['message' => $msg, 'type' => 'success']);
    }

    public function render()
    {
        $backupLogs = BackupLog::with('user')
            ->orderBy('id', 'desc')
            ->paginate(10);

        $totalBackupsCount = BackupLog::count();
        $totalStorageBytes = (int) BackupLog::sum('file_size');
        $totalStorageFormatted = $totalStorageBytes >= 1048576 
            ? number_format($totalStorageBytes / 1048576, 2) . ' MB' 
            : number_format($totalStorageBytes / 1024, 1) . ' KB';

        // Count pending transactions for current month
        $currentMonthStart = Carbon::createFromDate($this->selectedYear, $this->selectedMonth, 1)->startOfMonth();
        $currentMonthEnd = $currentMonthStart->copy()->endOfMonth();
        $previewCount = Transaction::completed()
            ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
            ->count();
        $previewAmount = (float) Transaction::completed()
            ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
            ->sum('grand_total');

        return view('livewire.admin.backup.backup-manager', [
            'backupLogs' => $backupLogs,
            'totalBackupsCount' => $totalBackupsCount,
            'totalStorageFormatted' => $totalStorageFormatted,
            'previewCount' => $previewCount,
            'previewAmount' => $previewAmount,
        ]);
    }
}
