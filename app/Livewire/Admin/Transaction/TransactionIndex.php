<?php

namespace App\Livewire\Admin\Transaction;

use App\Models\Customer;
use App\Models\Product;
use App\Models\SaleReturn;
use App\Models\SaleReturnItem;
use App\Models\StockMutation;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Riwayat Transaksi & Laporan Penjualan - Syafa Frozen Food')]
class TransactionIndex extends Component
{
    use WithPagination;

    // View Level: 'yearly', 'monthly', 'daily', 'transactions'
    #[Url(as: 'view', except: 'yearly', history: true)]
    public string $viewLevel = 'yearly';

    #[Url(as: 'year', except: null, history: true)]
    public ?int $selectedYear = null;

    #[Url(as: 'month', except: null, history: true)]
    public ?int $selectedMonth = null;

    #[Url(as: 'date', except: null, history: true)]
    public ?string $selectedDate = null;

    // Global Search Invoice / Struk
    #[Url(as: 'q', except: '', history: true)]
    public string $searchInvoice = '';

    // Modal Detail / Nota Digital
    public bool $showDetailModal = false;
    public ?int $selectedTransactionId = null;
    public string $waPhoneNumber = '';

    // Modal Retur / Pengembalian Barang
    public bool $showReturnModal = false;
    public ?int $returningTransactionId = null;
    public array $returnItems = []; // [item_id => ['selected' => bool, 'qty' => int, 'max_qty' => int, 'price' => float, 'name' => string, 'product_id' => int]]
    public string $returnReason = 'Salah Beli / Tukar Barang';
    public string $returnNotes = '';
    public bool $returnRestock = true;

    // Modal Edit Transaksi
    public bool $showEditModal = false;
    public ?int $editingTransactionId = null;
    public string $editInvoiceNumber = '';
    public string $editCustomerName = '';
    public ?int $editCustomerId = null;
    public bool $editIsLoyalty = false;
    public string $editPaymentMethod = 'cash';
    public float $editDiscountAmount = 0;
    public float $editPaidAmount = 0;
    public string $editNotes = '';
    public array $editItems = [];

    // Modal Delete / Batal Transaksi
    public bool $showDeleteModal = false;
    public ?int $deletingTransactionId = null;
    public string $deletingInvoiceNumber = '';

    // Filter Khusus Level Transaksi Harian
    public string $filterPaymentMethod = '';
    public string $filterCashierId = '';

    public function mount(): void
    {
        if ($this->viewLevel === 'monthly' && !$this->selectedYear) {
            $this->selectedYear = (int) date('Y');
        } elseif ($this->viewLevel === 'daily') {
            if (!$this->selectedYear) $this->selectedYear = (int) date('Y');
            if (!$this->selectedMonth) $this->selectedMonth = (int) date('m');
        } elseif ($this->viewLevel === 'transactions') {
            if (!$this->selectedDate && empty($this->searchInvoice)) {
                $this->selectedDate = Carbon::today()->format('Y-m-d');
            }
        }
    }

    // ================= NAVIGATION / DRILL-DOWN =================

    public function goToYearly(): void
    {
        $this->viewLevel = 'yearly';
        $this->selectedYear = null;
        $this->selectedMonth = null;
        $this->selectedDate = null;
        $this->searchInvoice = '';
        $this->resetPage();
    }

    public function goToMonthly(?int $year = null): void
    {
        $this->viewLevel = 'monthly';
        $this->selectedYear = $year ?? ($this->selectedYear ?? (int) date('Y'));
        $this->selectedMonth = null;
        $this->selectedDate = null;
        $this->searchInvoice = '';
        $this->resetPage();
    }

    public function goToDaily(?int $year = null, ?int $month = null): void
    {
        $this->viewLevel = 'daily';
        if ($year !== null) $this->selectedYear = $year;
        if ($month !== null) $this->selectedMonth = $month;
        if (!$this->selectedYear) $this->selectedYear = (int) date('Y');
        if (!$this->selectedMonth) $this->selectedMonth = (int) date('m');
        $this->selectedDate = null;
        $this->searchInvoice = '';
        $this->resetPage();
    }

    public function goToTransactions(?string $date = null): void
    {
        $this->viewLevel = 'transactions';
        $this->selectedDate = $date ?? ($this->selectedDate ?? Carbon::today()->format('Y-m-d'));
        $parsed = Carbon::parse($this->selectedDate);
        $this->selectedYear = (int) $parsed->year;
        $this->selectedMonth = (int) $parsed->month;
        $this->searchInvoice = '';
        $this->resetPage();
    }

    public function updatedSearchInvoice(): void
    {
        $query = trim($this->searchInvoice);
        if (!empty($query)) {
            $this->viewLevel = 'transactions';
            $this->selectedDate = null;
            $this->resetPage();
        } else {
            $this->goToYearly();
        }
    }

    public function resetSearch(): void
    {
        $this->goToYearly();
    }

    // ================= RETUR / PENGEMBALIAN BARANG =================

    public function openReturnModal(int $id): void
    {
        $trx = Transaction::with(['items.product'])->findOrFail($id);
        $this->returningTransactionId = $trx->id;
        $this->returnReason = 'Salah Beli / Tukar Barang';
        $this->returnNotes = '';
        $this->returnRestock = true;

        $this->returnItems = [];
        foreach ($trx->items as $item) {
            $this->returnItems[$item->id] = [
                'selected' => false,
                'qty' => (int) $item->quantity,
                'max_qty' => (int) $item->quantity,
                'unit_price' => (float) $item->unit_price,
                'cost_price' => (float) $item->cost_price,
                'product_name' => $item->product_name,
                'product_id' => $item->product_id,
            ];
        }

        $this->showReturnModal = true;
    }

    public function closeReturnModal(): void
    {
        $this->showReturnModal = false;
        $this->returningTransactionId = null;
        $this->returnItems = [];
    }

    public function processReturn(): void
    {
        if (!$this->returningTransactionId) return;

        $hasSelected = false;
        foreach ($this->returnItems as $item) {
            if (!empty($item['selected']) && $item['qty'] > 0) {
                $hasSelected = true;
                break;
            }
        }

        if (!$hasSelected) {
            session()->flash('error', 'Pilih minimal satu produk yang ingin diretur dengan jumlah minimal 1.');
            return;
        }

        try {
            $refundTotal = 0;
            $returnedItemCount = 0;

            DB::transaction(function () use (&$refundTotal, &$returnedItemCount) {
                $trx = Transaction::with('items')->lockForUpdate()->findOrFail($this->returningTransactionId);
                $originalItems = $trx->items->keyBy('id');

                $saleReturn = SaleReturn::create([
                    'return_number' => SaleReturn::generateReturnNumber(),
                    'transaction_id' => $trx->id,
                    'invoice_number' => $trx->invoice_number,
                    'user_id' => Auth::id() ?? 1,
                    'customer_id' => $trx->customer_id,
                    'total_refund' => 0,
                    'total_items' => 0,
                    'reason' => $this->returnReason,
                    'notes' => $this->returnNotes ? trim($this->returnNotes) : null,
                    'restock' => (bool) $this->returnRestock,
                ]);

                foreach ($this->returnItems as $itemId => $retData) {
                    if (empty($retData['selected']) || $retData['qty'] <= 0) {
                        continue;
                    }

                    $qtyToReturn = min((int) $retData['qty'], (int) $retData['max_qty']);
                    $itemModel = $originalItems->get($itemId);

                    if (!$itemModel) continue;

                    $itemRefund = $qtyToReturn * $itemModel->unit_price;
                    $refundTotal += $itemRefund;
                    $returnedItemCount += $qtyToReturn;

                    // Catat item retur
                    SaleReturnItem::create([
                        'sale_return_id' => $saleReturn->id,
                        'product_id' => $itemModel->product_id,
                        'product_name' => $itemModel->product_name ?? ($itemModel->product?->name ?? 'Produk'),
                        'quantity' => $qtyToReturn,
                        'unit_price' => $itemModel->unit_price,
                        'subtotal' => $itemRefund,
                    ]);

                    // 1. Restock barang ke inventory jika diaktifkan
                    if ($this->returnRestock && $itemModel->product_id) {
                        $product = Product::lockForUpdate()->find($itemModel->product_id);
                        if ($product) {
                            $stockBefore = $product->stock;
                            $product->increment('stock', $qtyToReturn);
                            $stockAfter = $product->fresh()->stock;

                            StockMutation::create([
                                'product_id' => $product->id,
                                'user_id' => Auth::id() ?? 1,
                                'type' => 'in',
                                'quantity' => $qtyToReturn,
                                'stock_before' => $stockBefore,
                                'stock_after' => $stockAfter,
                                'reference' => $trx->invoice_number,
                                'description' => "Retur Penjualan ({$this->returnReason}): {$qtyToReturn}x {$product->name}" . ($this->returnNotes ? " - {$this->returnNotes}" : ''),
                            ]);
                        }
                    }

                    // 2. Sesuaikan item dalam transaksi
                    if ($qtyToReturn >= $itemModel->quantity) {
                        $itemModel->delete();
                    } else {
                        $newQty = $itemModel->quantity - $qtyToReturn;
                        $itemModel->update([
                            'quantity' => $newQty,
                            'subtotal' => $newQty * $itemModel->unit_price,
                        ]);
                    }
                }

                // Update SaleReturn totals
                $saleReturn->update([
                    'total_refund' => $refundTotal,
                    'total_items' => $returnedItemCount,
                ]);

                // 3. Recalculate Transaction
                $remainingItems = $trx->items()->get();
                if ($remainingItems->isEmpty()) {
                    // Semua item diretur
                    $trx->update([
                        'subtotal' => 0,
                        'discount_amount' => 0,
                        'grand_total' => 0,
                        'paid_amount' => 0,
                        'change_amount' => 0,
                        'notes' => trim(($trx->notes ? $trx->notes . "\n" : '') . "[RETUR TOTAL " . Carbon::now()->format('d/m/Y H:i') . "] {$this->returnReason}: Pengembalian dana Rp " . number_format($refundTotal, 0, ',', '.')),
                    ]);
                } else {
                    $newSubtotal = $remainingItems->sum('subtotal');
                    $newGrandTotal = max(0, $newSubtotal - $trx->discount_amount);
                    $newChange = max(0, $trx->paid_amount - $newGrandTotal);

                    $trx->update([
                        'subtotal' => $newSubtotal,
                        'grand_total' => $newGrandTotal,
                        'change_amount' => $newChange,
                        'notes' => trim(($trx->notes ? $trx->notes . "\n" : '') . "[RETUR PARSIAL " . Carbon::now()->format('d/m/Y H:i') . "] {$this->returnReason}: Pengembalian dana Rp " . number_format($refundTotal, 0, ',', '.')),
                    ]);
                }
            });

            $msg = "Retur berhasil diproses! {$returnedItemCount} barang dikembalikan ke stok. Dana refund: Rp " . number_format($refundTotal, 0, ',', '.');
            session()->flash('success', $msg);
            $this->dispatch('toast', ['message' => $msg, 'type' => 'success']);
            $this->closeReturnModal();

        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memproses retur: ' . $e->getMessage());
            $this->dispatch('toast', ['message' => 'Gagal memproses retur: ' . $e->getMessage(), 'type' => 'error']);
        }
    }

    // ================= DETAIL / NOTA DIGITAL =================

    public function openDetail(int $id): void
    {
        $this->selectedTransactionId = $id;
        $trx = Transaction::with(['customer', 'items', 'user', 'discount'])->findOrFail($id);
        $this->waPhoneNumber = $trx->customer?->phone ?: '';
        $this->showDetailModal = true;
    }

    public function closeDetail(): void
    {
        $this->showDetailModal = false;
        $this->selectedTransactionId = null;
        $this->waPhoneNumber = '';
    }

    public function getWhatsAppShareUrl(Transaction $trx): string
    {
        $phone = preg_replace('/[^0-9]/', '', $this->waPhoneNumber ?: ($trx->customer?->phone ?? ''));
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        $text = $this->generateWhatsAppReceiptText($trx);
        $encoded = urlencode($text);

        if ($phone) {
            return "https://wa.me/{$phone}?text={$encoded}";
        }

        return "https://api.whatsapp.com/send?text={$encoded}";
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

        $no = 1;
        foreach ($trx->items as $item) {
            $unitPriceFmt = number_format($item->unit_price, 0, ',', '.');
            $subtotalFmt = number_format($item->subtotal, 0, ',', '.');
            $lines[] = "{$no}. {$item->product_name}";
            $lines[] = "   {$item->quantity}x @ Rp {$unitPriceFmt} = Rp {$subtotalFmt}";
            $no++;
        }

        $lines[] = "----------------------------------------";
        $lines[] = "*Subtotal:* Rp " . number_format($trx->subtotal, 0, ',', '.');

        if ($trx->discount_amount > 0) {
            $discLabel = $trx->discount ? "Diskon ({$trx->discount->name})" : "Diskon Promo";
            $lines[] = "*{$discLabel}:* -Rp " . number_format($trx->discount_amount, 0, ',', '.');
        }

        $lines[] = "*TOTAL AKHIR:* *Rp " . number_format($trx->grand_total, 0, ',', '.') . "*";
        if ($trx->payment_method === 'tempo') {
            $statusLabel = $trx->payment_status === 'paid' ? 'LUNAS' : ($trx->payment_status === 'partial' ? 'DICICIL SEBAGIAN' : 'BELUM LUNAS');
            $lines[] = "*Metode Bayar:* TEMPO (PIUTANG)";
            $lines[] = "*DP / Terbayar:* Rp " . number_format($trx->paid_amount, 0, ',', '.');
            $lines[] = "*Sisa Piutang:* *Rp " . number_format($trx->remaining_amount, 0, ',', '.') . "*";
            $lines[] = "*Jatuh Tempo:* " . ($trx->due_date ? $trx->due_date->format('d/m/Y') : '-');
            $lines[] = "*Status Tagihan:* " . $statusLabel;
        } else {
            $lines[] = "*Metode Bayar:* " . strtoupper($trx->payment_method);
            $lines[] = "*Jumlah Bayar:* Rp " . number_format($trx->paid_amount, 0, ',', '.');
            $lines[] = "*Kembalian:* Rp " . number_format($trx->change_amount, 0, ',', '.');
        }
        $lines[] = "----------------------------------------";
        $lines[] = "_Terima kasih telah berbelanja di Syafa Frozen Food!_";
        $lines[] = "_Simpan nota ini sebagai bukti transaksi sah._";

        return implode("\n", $lines);
    }

    // ================= EDIT TRANSAKSI =================

    public function openEditModal(int $id): void
    {
        $trx = Transaction::with(['items'])->findOrFail($id);
        $this->editingTransactionId = $trx->id;
        $this->editInvoiceNumber = $trx->invoice_number;
        $this->editCustomerName = $trx->customer_name;
        $this->editCustomerId = $trx->customer_id;
        $this->editIsLoyalty = (bool) $trx->is_loyalty;
        $this->editPaymentMethod = $trx->payment_method;
        $this->editDiscountAmount = (float) $trx->discount_amount;
        $this->editPaidAmount = (float) $trx->paid_amount;
        $this->editNotes = $trx->notes ?? '';

        $this->editItems = [];
        foreach ($trx->items as $item) {
            $this->editItems[] = [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'product_name' => $item->product_name,
                'unit_type' => $item->unit_type,
                'price_type' => $item->price_type ?? 'regular',
                'discount_type' => $item->discount_type,
                'discount_rate' => (float) $item->discount_rate,
                'discount_amount' => (float) $item->discount_amount,
                'quantity' => (int) $item->quantity,
                'unit_price' => (float) $item->unit_price,
                'cost_price' => (float) $item->cost_price,
                'subtotal' => (float) $item->subtotal,
            ];
        }

        $this->resetValidation();
        $this->showEditModal = true;
    }

    public function updateItemQuantity(int $index, int $qty): void
    {
        if ($qty < 1) $qty = 1;
        if (isset($this->editItems[$index])) {
            $this->editItems[$index]['quantity'] = $qty;
            $gross = $qty * $this->editItems[$index]['unit_price'];
            $discType = $this->editItems[$index]['discount_type'] ?? null;
            $discRate = (float) ($this->editItems[$index]['discount_rate'] ?? 0);
            $discAmount = 0;
            if ($discType === 'percent' && $discRate > 0) {
                $discAmount = ($gross * $discRate) / 100;
            } elseif ($discType === 'fixed' && $discRate > 0) {
                $discAmount = min($gross, $discRate * $qty);
            }
            $this->editItems[$index]['discount_amount'] = $discAmount;
            $this->editItems[$index]['subtotal'] = max(0, $gross - $discAmount);
        }
    }

    public function saveEditTransaction(): void
    {
        $this->validate([
            'editCustomerName' => 'required|string|max:100',
            'editPaymentMethod' => 'required|in:cash,qris,transfer,tempo',
            'editPaidAmount' => 'required|numeric|min:0',
        ]);

        if (empty($this->editItems)) {
            session()->flash('error', 'Transaksi harus memiliki minimal 1 item produk.');
            return;
        }

        try {
            DB::transaction(function () {
                $trx = Transaction::with('items')->lockForUpdate()->findOrFail($this->editingTransactionId);

                $newSubtotal = 0;
                $originalItemsMap = $trx->items->keyBy('id');

                foreach ($this->editItems as $itemData) {
                    $gross = $itemData['quantity'] * $itemData['unit_price'];
                    $discAmount = (float) ($itemData['discount_amount'] ?? 0);
                    $itemSubtotal = max(0, $gross - $discAmount);
                    $newSubtotal += $itemSubtotal;

                    if (!empty($itemData['product_id'])) {
                        $product = Product::lockForUpdate()->find($itemData['product_id']);
                        if ($product) {
                            $originalItem = $originalItemsMap->get($itemData['id'] ?? null);
                            $oldQty = $originalItem ? $originalItem->quantity : 0;
                            $diff = $itemData['quantity'] - $oldQty;

                            if ($diff !== 0) {
                                $product->decrement('stock', $diff);
                            }
                        }
                    }

                    if (!empty($itemData['id']) && $originalItemsMap->has($itemData['id'])) {
                        $trxItem = $originalItemsMap->get($itemData['id']);
                        $trxItem->update([
                            'quantity' => $itemData['quantity'],
                            'unit_price' => $itemData['unit_price'],
                            'discount_amount' => $discAmount,
                            'subtotal' => $itemSubtotal,
                        ]);
                    }
                }

                $newGrandTotal = max(0, $newSubtotal - $this->editDiscountAmount);
                $newChangeAmount = max(0, $this->editPaidAmount - $newGrandTotal);

                $trx->update([
                    'customer_name' => $this->editCustomerName,
                    'customer_id' => $this->editCustomerId,
                    'payment_method' => $this->editPaymentMethod,
                    'subtotal' => $newSubtotal,
                    'discount_amount' => $this->editDiscountAmount,
                    'grand_total' => $newGrandTotal,
                    'paid_amount' => $this->editPaidAmount,
                    'change_amount' => $newChangeAmount,
                    'notes' => $this->editNotes ?: null,
                ]);
            });

            session()->flash('success', "Transaksi '{$this->editInvoiceNumber}' berhasil diperbarui.");
            $this->showEditModal = false;
            $this->reset(['editingTransactionId', 'editInvoiceNumber', 'editCustomerName', 'editItems']);

        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memperbarui transaksi: ' . $e->getMessage());
        }
    }

    // ================= DELETE / BATAL TRANSAKSI =================

    public function confirmDelete(int $id): void
    {
        $trx = Transaction::findOrFail($id);
        $this->deletingTransactionId = $trx->id;
        $this->deletingInvoiceNumber = $trx->invoice_number;
        $this->showDeleteModal = true;
    }

    public function deleteTransaction(): void
    {
        if (!$this->deletingTransactionId) {
            return;
        }

        try {
            DB::transaction(function () {
                $trx = Transaction::with('items')->lockForUpdate()->findOrFail($this->deletingTransactionId);

                // Kembalikan stok produk yang terjual
                foreach ($trx->items as $item) {
                    if ($item->product_id) {
                        $product = Product::lockForUpdate()->find($item->product_id);
                        if ($product) {
                            $product->increment('stock', $item->quantity);
                        }
                    }
                }

                $trx->delete();
            });

            session()->flash('success', "Transaksi '{$this->deletingInvoiceNumber}' berhasil dibatalkan dan stok produk telah dikembalikan.");
            $this->showDeleteModal = false;
            $this->reset(['deletingTransactionId', 'deletingInvoiceNumber']);

        } catch (\Exception $e) {
            session()->flash('error', 'Gagal membatalkan transaksi: ' . $e->getMessage());
        }
    }

    // ================= DATA CALCULATION FOR CHARTS & TABLES =================

    public function getChartPayload(): array
    {
        $costSubquery = DB::table('transaction_items')
            ->select('transaction_id', DB::raw('SUM(cost_price * quantity) as total_cost'))
            ->groupBy('transaction_id');

        $chartPayload = [
            'labels' => [],
            'txCounts' => [],
            'profits' => [],
            'revenues' => [],
        ];

        // 1. TAHUNAN (per Tahun): Tampilkan 4 tahun terakhir (e.g. 2023, 2024, 2025, 2026)
        if ($this->viewLevel === 'yearly') {
            $isSqlite = DB::connection()->getDriverName() === 'sqlite';
            $yearExpression = $isSqlite ? "strftime('%Y', transactions.created_at) as year" : "YEAR(transactions.created_at) as year";

            $rawYearly = DB::table('transactions')
                ->leftJoinSub($costSubquery, 'costs', function ($join) {
                    $join->on('transactions.id', '=', 'costs.transaction_id');
                })
                ->where('transactions.status', 'completed')
                ->select(
                    DB::raw($yearExpression),
                    DB::raw('COUNT(transactions.id) as total_transactions'),
                    DB::raw('SUM(transactions.grand_total) as total_revenue'),
                    DB::raw('SUM(COALESCE(costs.total_cost, 0)) as total_cost')
                )
                ->groupBy('year')
                ->get()
                ->keyBy('year');

            $currentYear = (int) date('Y');
            $startYear = max(2023, $currentYear - 3);

            for ($y = $startYear; $y <= $currentYear; $y++) {
                $row = $rawYearly->get($y);
                $tx = $row ? (int) $row->total_transactions : 0;
                $rev = $row ? (float) $row->total_revenue : 0;
                $cost = $row ? (float) $row->total_cost : 0;
                $profit = $rev - $cost;

                $chartPayload['labels'][] = (string) $y;
                $chartPayload['txCounts'][] = $tx;
                $chartPayload['profits'][] = $profit;
                $chartPayload['revenues'][] = $rev;
            }
        } 
        
        // 2. BULANAN (per Bulan): Tampilkan bulan Jan s/d Des / bulan saat ini
        elseif ($this->viewLevel === 'monthly') {
            $year = $this->selectedYear ?? (int) date('Y');
            $isSqlite = DB::connection()->getDriverName() === 'sqlite';
            $monthExpr = $isSqlite ? "CAST(strftime('%m', transactions.created_at) AS INTEGER) as month_num" : "MONTH(transactions.created_at) as month_num";

            $rawMonthly = DB::table('transactions')
                ->leftJoinSub($costSubquery, 'costs', function ($join) {
                    $join->on('transactions.id', '=', 'costs.transaction_id');
                })
                ->where('transactions.status', 'completed')
                ->whereYear('transactions.created_at', $year)
                ->select(
                    DB::raw($monthExpr),
                    DB::raw('COUNT(transactions.id) as total_transactions'),
                    DB::raw('SUM(transactions.grand_total) as total_revenue'),
                    DB::raw('SUM(COALESCE(costs.total_cost, 0)) as total_cost')
                )
                ->groupBy('month_num')
                ->get()
                ->keyBy('month_num');

            $maxMonth = ($year === (int) date('Y')) ? (int) date('m') : 12;
            $monthNames = [
                1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
                5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
                9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
            ];

            for ($m = 1; $m <= $maxMonth; $m++) {
                $row = $rawMonthly->get($m);
                $tx = $row ? (int) $row->total_transactions : 0;
                $rev = $row ? (float) $row->total_revenue : 0;
                $cost = $row ? (float) $row->total_cost : 0;
                $profit = $rev - $cost;

                $chartPayload['labels'][] = $monthNames[$m] . ' ' . $year;
                $chartPayload['txCounts'][] = $tx;
                $chartPayload['profits'][] = $profit;
                $chartPayload['revenues'][] = $rev;
            }
        } 
        
        // 3. HARIAN (per Hari): Tampilkan hari 01 s/d hari saat ini
        elseif ($this->viewLevel === 'daily') {
            $year = $this->selectedYear ?? (int) date('Y');
            $month = $this->selectedMonth ?? (int) date('m');
            $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;
            $isSqlite = DB::connection()->getDriverName() === 'sqlite';
            $dayExpr = $isSqlite ? "CAST(strftime('%d', transactions.created_at) AS INTEGER) as day_num" : "DAY(transactions.created_at) as day_num";

            $rawDaily = DB::table('transactions')
                ->leftJoinSub($costSubquery, 'costs', function ($join) {
                    $join->on('transactions.id', '=', 'costs.transaction_id');
                })
                ->where('transactions.status', 'completed')
                ->whereYear('transactions.created_at', $year)
                ->whereMonth('transactions.created_at', $month)
                ->select(
                    DB::raw($dayExpr),
                    DB::raw('COUNT(transactions.id) as total_transactions'),
                    DB::raw('SUM(transactions.grand_total) as total_revenue'),
                    DB::raw('SUM(COALESCE(costs.total_cost, 0)) as total_cost')
                )
                ->groupBy('day_num')
                ->get()
                ->keyBy('day_num');

            $maxDay = ($year === (int) date('Y') && $month === (int) date('m')) ? (int) date('d') : $daysInMonth;
            $monthShort = Carbon::createFromDate($year, $month, 1)->translatedFormat('M');

            for ($d = 1; $d <= $maxDay; $d++) {
                $row = $rawDaily->get($d);
                $tx = $row ? (int) $row->total_transactions : 0;
                $rev = $row ? (float) $row->total_revenue : 0;
                $cost = $row ? (float) $row->total_cost : 0;
                $profit = $rev - $cost;

                $chartPayload['labels'][] = sprintf('%02d %s', $d, $monthShort);
                $chartPayload['txCounts'][] = $tx;
                $chartPayload['profits'][] = $profit;
                $chartPayload['revenues'][] = $rev;
            }
        }

        return $chartPayload;
    }

    public function render()
    {
        $cashiers = User::orderBy('name')->get();
        $chartPayload = $this->getChartPayload();

        $yearlyData = collect();
        $monthlyData = collect();
        $dailyData = collect();
        $transactionsData = null;

        // Subquery Cost per Transaction
        $costSubquery = DB::table('transaction_items')
            ->select('transaction_id', DB::raw('SUM(cost_price * quantity) as total_cost'))
            ->groupBy('transaction_id');

        // LEVEL 1: TAHUNAN
        if ($this->viewLevel === 'yearly') {
            $isSqlite = DB::connection()->getDriverName() === 'sqlite';
            $yearExpression = $isSqlite ? "strftime('%Y', transactions.created_at) as year" : "YEAR(transactions.created_at) as year";

            $rawYearly = DB::table('transactions')
                ->leftJoinSub($costSubquery, 'costs', function ($join) {
                    $join->on('transactions.id', '=', 'costs.transaction_id');
                })
                ->where('transactions.status', 'completed')
                ->select(
                    DB::raw($yearExpression),
                    DB::raw('COUNT(transactions.id) as total_transactions'),
                    DB::raw('SUM(transactions.grand_total) as total_revenue'),
                    DB::raw('SUM(COALESCE(costs.total_cost, 0)) as total_cost')
                )
                ->groupBy('year')
                ->orderBy('year', 'desc')
                ->get();

            $yearlyData = $rawYearly->map(function ($row) {
                $profit = (float) $row->total_revenue - (float) $row->total_cost;
                return (object) [
                    'year' => (int) $row->year,
                    'total_transactions' => (int) $row->total_transactions,
                    'total_revenue' => (float) $row->total_revenue,
                    'total_cost' => (float) $row->total_cost,
                    'total_profit' => $profit,
                ];
            });

            // If empty, supply current year
            if ($yearlyData->isEmpty()) {
                $yearlyData = collect([(object) [
                    'year' => (int) date('Y'),
                    'total_transactions' => 0,
                    'total_revenue' => 0,
                    'total_cost' => 0,
                    'total_profit' => 0,
                ]]);
            }
        }

        // LEVEL 2: BULANAN
        elseif ($this->viewLevel === 'monthly') {
            $year = $this->selectedYear ?? (int) date('Y');
            $isSqlite = DB::connection()->getDriverName() === 'sqlite';
            $monthExpr = $isSqlite ? "CAST(strftime('%m', transactions.created_at) AS INTEGER) as month_num" : "MONTH(transactions.created_at) as month_num";

            $rawMonthly = DB::table('transactions')
                ->leftJoinSub($costSubquery, 'costs', function ($join) {
                    $join->on('transactions.id', '=', 'costs.transaction_id');
                })
                ->where('transactions.status', 'completed')
                ->whereYear('transactions.created_at', $year)
                ->select(
                    DB::raw($monthExpr),
                    DB::raw('COUNT(transactions.id) as total_transactions'),
                    DB::raw('SUM(transactions.grand_total) as total_revenue'),
                    DB::raw('SUM(COALESCE(costs.total_cost, 0)) as total_cost')
                )
                ->groupBy('month_num')
                ->get()
                ->keyBy('month_num');

            $allMonths = collect();
            for ($m = 1; $m <= 12; $m++) {
                $row = $rawMonthly->get($m);
                $tx = $row ? (int) $row->total_transactions : 0;
                $rev = $row ? (float) $row->total_revenue : 0;
                $cost = $row ? (float) $row->total_cost : 0;
                $profit = $rev - $cost;

                $allMonths->push((object) [
                    'year' => $year,
                    'month_num' => $m,
                    'month_label' => sprintf('%02d-%d', $m, $year),
                    'month_name' => Carbon::createFromDate($year, $m, 1)->translatedFormat('F'),
                    'total_transactions' => $tx,
                    'total_revenue' => $rev,
                    'total_cost' => $cost,
                    'total_profit' => $profit,
                ]);
            }

            // For Table: show active months with data first or desc
            $monthlyData = $allMonths->filter(fn($r) => $r->total_transactions > 0 || $r->month_num <= (int) date('m'))->sortByDesc('month_num');
            if ($monthlyData->isEmpty()) {
                $monthlyData = $allMonths->sortByDesc('month_num');
            }
        }

        // LEVEL 3: HARIAN
        elseif ($this->viewLevel === 'daily') {
            $year = $this->selectedYear ?? (int) date('Y');
            $month = $this->selectedMonth ?? (int) date('m');
            $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;
            $isSqlite = DB::connection()->getDriverName() === 'sqlite';
            $dateExpr = $isSqlite ? "strftime('%Y-%m-%d', transactions.created_at) as date_val" : "DATE(transactions.created_at) as date_val";
            $dayExpr = $isSqlite ? "CAST(strftime('%d', transactions.created_at) AS INTEGER) as day_num" : "DAY(transactions.created_at) as day_num";

            $rawDaily = DB::table('transactions')
                ->leftJoinSub($costSubquery, 'costs', function ($join) {
                    $join->on('transactions.id', '=', 'costs.transaction_id');
                })
                ->where('transactions.status', 'completed')
                ->whereYear('transactions.created_at', $year)
                ->whereMonth('transactions.created_at', $month)
                ->select(
                    DB::raw($dateExpr),
                    DB::raw($dayExpr),
                    DB::raw('COUNT(transactions.id) as total_transactions'),
                    DB::raw('SUM(transactions.grand_total) as total_revenue'),
                    DB::raw('SUM(COALESCE(costs.total_cost, 0)) as total_cost')
                )
                ->groupBy('date_val', 'day_num')
                ->get()
                ->keyBy('day_num');

            $allDays = collect();
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $dateString = sprintf('%04d-%02d-%02d', $year, $month, $d);
                $row = $rawDaily->get($d);
                $tx = $row ? (int) $row->total_transactions : 0;
                $rev = $row ? (float) $row->total_revenue : 0;
                $cost = $row ? (float) $row->total_cost : 0;
                $profit = $rev - $cost;

                $allDays->push((object) [
                    'date' => $dateString,
                    'day_num' => $d,
                    'date_formatted' => Carbon::createFromDate($year, $month, $d)->translatedFormat('d-m-Y'),
                    'day_name' => Carbon::createFromDate($year, $month, $d)->translatedFormat('l, d F Y'),
                    'total_transactions' => $tx,
                    'total_revenue' => $rev,
                    'total_cost' => $cost,
                    'total_profit' => $profit,
                ]);
            }

            // For Table: show active days or up to today desc
            $dailyData = $allDays->filter(fn($r) => $r->total_transactions > 0 || Carbon::parse($r->date)->lte(Carbon::today()))->sortByDesc('day_num');
            if ($dailyData->isEmpty()) {
                $dailyData = $allDays->sortByDesc('day_num');
            }
        }

        // LEVEL 4: DAFTAR TRANSAKSI
        elseif ($this->viewLevel === 'transactions') {
            $query = Transaction::query()
                ->completed()
                ->with(['user', 'customer', 'items']);

            if (!empty(trim($this->searchInvoice))) {
                $search = trim($this->searchInvoice);
                $query->where(function ($sub) use ($search) {
                    $sub->where('invoice_number', 'like', '%' . $search . '%')
                        ->orWhere('customer_name', 'like', '%' . $search . '%');
                });
            } else {
                $query->whereDate('created_at', $this->selectedDate ?? Carbon::today()->format('Y-m-d'));
            }

            if ($this->filterPaymentMethod) {
                $query->where('payment_method', $this->filterPaymentMethod);
            }

            if ($this->filterCashierId) {
                $query->where('user_id', $this->filterCashierId);
            }

            $transactionsData = $query->latest('id')->paginate(15);
        }

        $selectedTransaction = null;
        if ($this->selectedTransactionId) {
            $selectedTransaction = Transaction::with(['items', 'user', 'customer', 'discount'])->find($this->selectedTransactionId);
        }

        // ================= EXECUTIVE KPI STATS =================
        $stats = (object) [
            'total_transactions' => 0,
            'total_revenue' => 0,
            'total_cost' => 0,
            'total_profit' => 0,
            'profit_margin' => 0,
            'aov' => 0,
            'best_label' => '-',
            'best_revenue' => 0,
            'active_periods' => 0,
        ];

        if ($this->viewLevel === 'yearly') {
            $totalTx = (int) $yearlyData->sum('total_transactions');
            $totalRev = (float) $yearlyData->sum('total_revenue');
            $totalCost = (float) $yearlyData->sum('total_cost');
            $totalProfit = (float) $yearlyData->sum('total_profit');
            $best = $yearlyData->sortByDesc('total_revenue')->first();

            $stats->total_transactions = $totalTx;
            $stats->total_revenue = $totalRev;
            $stats->total_cost = $totalCost;
            $stats->total_profit = $totalProfit;
            $stats->profit_margin = $totalRev > 0 ? round(($totalProfit / $totalRev) * 100, 1) : 0;
            $stats->aov = $totalTx > 0 ? round($totalRev / $totalTx) : 0;
            $stats->best_label = $best && $best->total_revenue > 0 ? "Tahun {$best->year}" : '-';
            $stats->best_revenue = $best ? $best->total_revenue : 0;
            $stats->active_periods = $yearlyData->where('total_transactions', '>', 0)->count();
        } elseif ($this->viewLevel === 'monthly') {
            $totalTx = (int) $monthlyData->sum('total_transactions');
            $totalRev = (float) $monthlyData->sum('total_revenue');
            $totalCost = (float) $monthlyData->sum('total_cost');
            $totalProfit = (float) $monthlyData->sum('total_profit');
            $best = $monthlyData->sortByDesc('total_revenue')->first();

            $stats->total_transactions = $totalTx;
            $stats->total_revenue = $totalRev;
            $stats->total_cost = $totalCost;
            $stats->total_profit = $totalProfit;
            $stats->profit_margin = $totalRev > 0 ? round(($totalProfit / $totalRev) * 100, 1) : 0;
            $stats->aov = $totalTx > 0 ? round($totalRev / $totalTx) : 0;
            $stats->best_label = $best && $best->total_revenue > 0 ? "Bulan {$best->month_name}" : '-';
            $stats->best_revenue = $best ? $best->total_revenue : 0;
            $stats->active_periods = $monthlyData->where('total_transactions', '>', 0)->count();
        } elseif ($this->viewLevel === 'daily') {
            $totalTx = (int) $dailyData->sum('total_transactions');
            $totalRev = (float) $dailyData->sum('total_revenue');
            $totalCost = (float) $dailyData->sum('total_cost');
            $totalProfit = (float) $dailyData->sum('total_profit');
            $best = $dailyData->sortByDesc('total_revenue')->first();

            $stats->total_transactions = $totalTx;
            $stats->total_revenue = $totalRev;
            $stats->total_cost = $totalCost;
            $stats->total_profit = $totalProfit;
            $stats->profit_margin = $totalRev > 0 ? round(($totalProfit / $totalRev) * 100, 1) : 0;
            $stats->aov = $totalTx > 0 ? round($totalRev / $totalTx) : 0;
            $stats->best_label = $best && $best->total_revenue > 0 ? Carbon::parse($best->date)->translatedFormat('d M') : '-';
            $stats->best_revenue = $best ? $best->total_revenue : 0;
            $stats->active_periods = $dailyData->where('total_transactions', '>', 0)->count();
        } elseif ($this->viewLevel === 'transactions') {
            $dayTrx = DB::table('transactions')
                ->leftJoinSub($costSubquery, 'costs', function ($join) {
                    $join->on('transactions.id', '=', 'costs.transaction_id');
                })
                ->where('transactions.status', 'completed')
                ->whereDate('transactions.created_at', $this->selectedDate ?? Carbon::today()->format('Y-m-d'))
                ->select(
                    DB::raw('COUNT(transactions.id) as total_transactions'),
                    DB::raw('SUM(transactions.grand_total) as total_revenue'),
                    DB::raw('SUM(COALESCE(costs.total_cost, 0)) as total_cost')
                )
                ->first();

            $totalTx = $dayTrx ? (int) $dayTrx->total_transactions : 0;
            $totalRev = $dayTrx ? (float) $dayTrx->total_revenue : 0;
            $totalCost = $dayTrx ? (float) $dayTrx->total_cost : 0;
            $totalProfit = $totalRev - $totalCost;

            $stats->total_transactions = $totalTx;
            $stats->total_revenue = $totalRev;
            $stats->total_cost = $totalCost;
            $stats->total_profit = $totalProfit;
            $stats->profit_margin = $totalRev > 0 ? round(($totalProfit / $totalRev) * 100, 1) : 0;
            $stats->aov = $totalTx > 0 ? round($totalRev / $totalTx) : 0;
        }

        return view('livewire.admin.transaction.transaction-index', [
            'cashiers' => $cashiers,
            'chartPayload' => $chartPayload,
            'yearlyData' => $yearlyData,
            'monthlyData' => $monthlyData,
            'dailyData' => $dailyData,
            'transactionsData' => $transactionsData,
            'selectedTransaction' => $selectedTransaction,
            'stats' => $stats,
        ]);
    }
}
