<?php

namespace App\Livewire\Admin\Transaction;

use App\Models\Customer;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Buku Piutang Pelanggan (Accounts Receivable) - Syafa Frozen Food')]
class ReceivableIndex extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true)]
    public string $statusTab = 'unpaid'; // unpaid (active), overdue, due_soon, paid, all

    #[Url(history: true)]
    public ?int $customerId = null;

    // Modal Catat Pembayaran Piutang
    public bool $showPaymentModal = false;
    public ?Transaction $selectedTransaction = null;
    public float|int|string $paymentAmount = 0;
    public string $paymentMethod = 'cash'; // cash, transfer, qris
    public string $paymentNotes = '';

    // Modal Detail / Nota Struk
    public bool $showDetailModal = false;
    public ?int $selectedTransactionId = null;
    public string $waPhoneNumber = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusTab(): void
    {
        $this->resetPage();
    }

    public function updatedCustomerId(): void
    {
        $this->resetPage();
    }

    // ================= RECORD PAYMENT / CICILAN PIUTANG =================
    public function openPaymentModal(int $transactionId): void
    {
        $this->selectedTransaction = Transaction::with(['customer', 'items', 'user'])->findOrFail($transactionId);
        $this->paymentAmount = $this->selectedTransaction->remaining_amount;
        $this->paymentMethod = 'cash';
        $this->paymentNotes = '';
        $this->resetValidation();
        $this->showPaymentModal = true;
    }

    public function closePaymentModal(): void
    {
        $this->showPaymentModal = false;
        $this->selectedTransaction = null;
        $this->paymentAmount = 0;
        $this->paymentNotes = '';
    }

    public function recordPayment(): void
    {
        if (!$this->selectedTransaction) {
            return;
        }

        $this->validate([
            'paymentAmount' => 'required|numeric|min:1',
            'paymentMethod' => 'required|in:cash,transfer,qris',
        ]);

        $amount = (float) $this->paymentAmount;
        $remaining = $this->selectedTransaction->remaining_amount;

        if ($amount > $remaining) {
            $amount = $remaining;
        }

        try {
            DB::transaction(function () use ($amount) {
                $trx = Transaction::lockForUpdate()->findOrFail($this->selectedTransaction->id);
                $newPaidAmount = (float) $trx->paid_amount + $amount;
                $newStatus = $newPaidAmount >= (float) $trx->grand_total ? 'paid' : 'partial';

                $currentNotes = $trx->notes ? $trx->notes . ' | ' : '';
                $paymentNote = "Cicilan Rp " . number_format($amount, 0, ',', '.') . " via " . strtoupper($this->paymentMethod) . " (" . Carbon::now()->format('d/m/Y H:i') . ")";
                if ($this->paymentNotes) {
                    $paymentNote .= ": {$this->paymentNotes}";
                }

                $trx->update([
                    'paid_amount' => $newPaidAmount,
                    'payment_status' => $newStatus,
                    'notes' => $currentNotes . $paymentNote,
                ]);
            });

            $msg = "Pembayaran piutang nota {$this->selectedTransaction->invoice_number} sebesar Rp " . number_format($amount, 0, ',', '.') . " berhasil dicatat.";
            session()->flash('success', $msg);
            $this->dispatch('toast', ['message' => $msg, 'type' => 'success']);
            $this->closePaymentModal();

        } catch (\Exception $e) {
            $err = 'Gagal mencatat pembayaran piutang: ' . $e->getMessage();
            session()->flash('error', $err);
            $this->dispatch('toast', ['message' => $err, 'type' => 'error']);
        }
    }

    // ================= DETAIL & NOTA MODAL =================
    public function openDetail(int $id): void
    {
        $this->selectedTransactionId = $id;
        $trx = Transaction::with(['customer', 'items', 'user'])->findOrFail($id);
        $this->waPhoneNumber = $trx->customer?->phone ?: '';
        $this->showDetailModal = true;
    }

    public function closeDetail(): void
    {
        $this->showDetailModal = false;
        $this->selectedTransactionId = null;
        $this->waPhoneNumber = '';
    }

    public function getWhatsAppReminderUrl(Transaction $trx): string
    {
        $phone = preg_replace('/[^0-9]/', '', $this->waPhoneNumber ?: ($trx->customer?->phone ?? ''));
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        $text = $this->generateWhatsAppReminderText($trx);
        $encoded = urlencode($text);

        if ($phone) {
            return "https://wa.me/{$phone}?text={$encoded}";
        }

        return "https://api.whatsapp.com/send?text={$encoded}";
    }

    public function generateWhatsAppReminderText(Transaction $trx): string
    {
        $date = $trx->created_at->translatedFormat('d/m/Y H:i');
        $dueDate = $trx->due_date ? $trx->due_date->translatedFormat('d/m/Y') : '-';
        $pelanggan = $trx->customer_name;
        $grandTotalFmt = number_format($trx->grand_total, 0, ',', '.');
        $paidFmt = number_format($trx->paid_amount, 0, ',', '.');
        $remainingFmt = number_format($trx->remaining_amount, 0, ',', '.');

        $statusLabel = $trx->payment_status === 'paid' ? 'LUNAS' : ($trx->payment_status === 'partial' ? 'DICICIL SEBAGIAN' : 'BELUM DIBAYAR');

        $lines = [];
        $lines[] = "*PENGINGAT TAGIHAN BELANJA - SYAFA FROZEN FOOD*";
        $lines[] = "Pusat Frozen Food Berkualitas & Terpercaya";
        $lines[] = "----------------------------------------";
        $lines[] = "Kepada Yth. *{$pelanggan}*,";
        $lines[] = "Berikut kami sampaikan rincian tagihan belanja tempo di toko kami:";
        $lines[] = "";
        $lines[] = "*No. Invoice:* {$trx->invoice_number}";
        $lines[] = "*Tanggal Belanja:* {$date} WIB";
        $lines[] = "*Jatuh Tempo:* *{$dueDate}*";
        $lines[] = "*Total Tagihan:* Rp {$grandTotalFmt}";
        $lines[] = "*Sudah Dibayar / DP:* Rp {$paidFmt}";
        $lines[] = "----------------------------------------";
        $lines[] = "*SISA TAGIHAN:* *Rp {$remainingFmt}*";
        $lines[] = "*Status:* {$statusLabel}";
        $lines[] = "----------------------------------------";
        $lines[] = "Pembayaran dapat ditransfer atau dibayar langsung ke kasir toko.";
        $lines[] = "Mohon konfirmasi setelah melakukan pembayaran.";
        $lines[] = "Terima kasih atas kepercayaannya berbelanja di Syafa Frozen Food.";

        return implode("\n", $lines);
    }

    public function render()
    {
        $today = Carbon::today();
        $in7Days = Carbon::today()->addDays(7);

        // 1. Hitung Metrik KPI Piutang (Semua transaksi tempo / yang memiliki piutang aktif)
        $allTempoTransactions = Transaction::query()
            ->where(function ($q) {
                $q->where('payment_method', 'tempo')
                  ->orWhereIn('payment_status', ['unpaid', 'partial']);
            })
            ->get();

        $totalActiveReceivables = 0;
        $totalOverdueReceivables = 0;
        $totalDueSoonReceivables = 0;
        $totalCollectedReceivables = 0;
        $customersWithDebt = [];

        foreach ($allTempoTransactions as $trx) {
            $rem = $trx->remaining_amount;
            if ($rem > 0) {
                $totalActiveReceivables += $rem;
                if ($trx->customer_id) {
                    $customersWithDebt[$trx->customer_id] = true;
                }

                if ($trx->due_date && $trx->due_date->lt($today)) {
                    $totalOverdueReceivables += $rem;
                } elseif ($trx->due_date && $trx->due_date->lte($in7Days)) {
                    $totalDueSoonReceivables += $rem;
                }
            }

            if ($trx->payment_method === 'tempo') {
                $totalCollectedReceivables += (float) $trx->paid_amount;
            }
        }

        $totalCustomerDebtCount = count($customersWithDebt);

        // 2. Query Transaksi Piutang dengan Filter
        $receivablesQuery = Transaction::query()
            ->with(['customer', 'user', 'items'])
            ->where(function ($q) {
                $q->where('payment_method', 'tempo')
                  ->orWhereIn('payment_status', ['unpaid', 'partial']);
            })
            ->when($this->search, function ($q) {
                $term = '%' . $this->search . '%';
                $q->where(function ($sub) use ($term) {
                    $sub->where('invoice_number', 'like', $term)
                        ->orWhere('customer_name', 'like', $term)
                        ->orWhereHas('customer', function ($cust) use ($term) {
                            $cust->where('name', 'like', $term)
                                ->orWhere('phone', 'like', $term)
                                ->orWhere('member_code', 'like', $term);
                        });
                });
            })
            ->when($this->customerId, function ($q) {
                $q->where('customer_id', $this->customerId);
            });

        switch ($this->statusTab) {
            case 'overdue':
                $receivablesQuery->whereIn('payment_status', ['unpaid', 'partial'])
                    ->whereDate('due_date', '<', $today);
                break;
            case 'due_soon':
                $receivablesQuery->whereIn('payment_status', ['unpaid', 'partial'])
                    ->whereDate('due_date', '>=', $today)
                    ->whereDate('due_date', '<=', $in7Days);
                break;
            case 'unpaid':
                $receivablesQuery->whereIn('payment_status', ['unpaid', 'partial']);
                break;
            case 'paid':
                $receivablesQuery->where('payment_status', 'paid');
                break;
            case 'all':
            default:
                break;
        }

        $receivables = $receivablesQuery->latest('id')->paginate(10);

        $customersList = Customer::orderBy('name')->get();

        $selectedDetailTransaction = null;
        if ($this->selectedTransactionId) {
            $selectedDetailTransaction = Transaction::with(['items', 'user', 'customer'])->find($this->selectedTransactionId);
        }

        return view('livewire.admin.transaction.receivable-index', [
            'receivables' => $receivables,
            'customersList' => $customersList,
            'totalActiveReceivables' => $totalActiveReceivables,
            'totalOverdueReceivables' => $totalOverdueReceivables,
            'totalDueSoonReceivables' => $totalDueSoonReceivables,
            'totalCollectedReceivables' => $totalCollectedReceivables,
            'totalCustomerDebtCount' => $totalCustomerDebtCount,
            'selectedDetailTransaction' => $selectedDetailTransaction,
        ]);
    }
}
