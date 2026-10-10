<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'user_id',
        'outlet_id',
        'cashier_shift_id',
        'customer_id',
        'customer_name',
        'discount_id',
        'tax_id',
        'tax_rate',
        'is_loyalty',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'grand_total',
        'paid_amount',
        'change_amount',
        'payment_method',
        'split_cash_amount',
        'split_non_cash_amount',
        'split_non_cash_method',
        'payment_status',
        'due_date',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'is_loyalty' => 'boolean',
            'tax_rate' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'grand_total' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'change_amount' => 'decimal:2',
            'split_cash_amount' => 'decimal:2',
            'split_non_cash_amount' => 'decimal:2',
            'due_date' => 'date',
        ];
    }

    public function cashierShift(): BelongsTo
    {
        return $this->belongsTo(CashierShift::class, 'cashier_shift_id');
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function discount(): BelongsTo
    {
        return $this->belongsTo(Discount::class);
    }

    public function tax(): BelongsTo
    {
        return $this->belongsTo(Tax::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function saleReturns(): HasMany
    {
        return $this->hasMany(SaleReturn::class);
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'completed');
    }

    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('created_at', Carbon::today());
    }

    public function scopeTempo(Builder $query): Builder
    {
        return $query->where('payment_method', 'tempo');
    }

    public function scopeUnpaid(Builder $query): Builder
    {
        return $query->whereIn('payment_status', ['unpaid', 'partial']);
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0, (float) $this->grand_total - (float) $this->paid_amount);
    }

    public function getIsOverdueAttribute(): bool
    {
        if ($this->payment_status === 'paid' || !$this->due_date) {
            return false;
        }
        return $this->due_date->lt(Carbon::today());
    }

    public function generateWhatsAppMessage(?ReceiptSetting $settings = null): string
    {
        $settings = $settings ?: ReceiptSetting::getSettings();
        $storeName = $settings->store_name ?: 'Syafa frozen food';
        $storeAddress = $settings->store_address ? trim($settings->store_address) : '';
        $storePhone = $settings->store_phone ? trim($settings->store_phone) : '';

        $lines = [];
        $lines[] = "*🧾 STRUK PEMBELIAN - " . strtoupper($storeName) . "*";
        if ($storeAddress) {
            $lines[] = "📍 " . $storeAddress;
        }
        if ($storePhone) {
            $lines[] = "📞 No. Telp: " . $storePhone;
        }
        $lines[] = "================================";
        $lines[] = "No. Nota  : " . $this->invoice_number;
        $lines[] = "Tanggal   : " . ($this->created_at ? $this->created_at->translatedFormat('d M Y, H:i') : date('d M Y, H:i'));
        $lines[] = "Kasir     : " . ($this->user?->name ?? 'Kasir');
        $lines[] = "Pelanggan : " . ($this->customer_name ?: 'Pelanggan Umum');
        if ($this->outlet) {
            $lines[] = "Cabang    : " . $this->outlet->name;
        }
        $lines[] = "================================";
        $lines[] = "*RINCIAN ITEM BELANJA:*";

        $i = 1;
        foreach ($this->items as $item) {
            $unitLabel = ($item->unit_type === 'box') ? "Dus (@{$item->pieces_per_unit})" : ($item->unit ?: 'pcs');
            $lines[] = "{$i}. *" . $item->product_name . "*";
            $itemDetail = "   " . number_format($item->quantity) . " {$unitLabel} x Rp " . number_format($item->unit_price, 0, ',', '.') . " = Rp " . number_format($item->subtotal, 0, ',', '.');
            if ($item->discount_amount > 0) {
                $itemDetail .= " (Disc: -Rp " . number_format($item->discount_amount, 0, ',', '.') . ")";
            }
            $lines[] = $itemDetail;
            $i++;
        }

        $lines[] = "================================";
        $lines[] = "Subtotal    : Rp " . number_format($this->subtotal, 0, ',', '.');
        if ($this->discount_amount > 0) {
            $lines[] = "Diskon      : -Rp " . number_format($this->discount_amount, 0, ',', '.');
        }
        if ($this->tax_amount > 0) {
            $lines[] = "Pajak (PPN) : Rp " . number_format($this->tax_amount, 0, ',', '.');
        }
        $lines[] = "*TOTAL AKHIR : Rp " . number_format($this->grand_total, 0, ',', '.') . "*";

        if ($this->payment_method === 'split') {
            $lines[] = "Metode      : Pembayaran Split / Kombinasi";
            $lines[] = "• Tunai     : Rp " . number_format($this->split_cash_amount, 0, ',', '.');
            $lines[] = "• " . strtoupper($this->split_non_cash_method ?: 'Non-Tunai') . "   : Rp " . number_format($this->split_non_cash_amount, 0, ',', '.');
            $lines[] = "Total Bayar : Rp " . number_format($this->paid_amount, 0, ',', '.');
            $lines[] = "Kembalian   : Rp " . number_format($this->change_amount, 0, ',', '.');
        } elseif ($this->payment_method === 'tempo') {
            $lines[] = "Metode      : Tempo (Hutang Usaha)";
            $lines[] = "Uang Muka   : Rp " . number_format($this->paid_amount, 0, ',', '.');
            $lines[] = "Sisa Hutang : Rp " . number_format($this->remaining_amount, 0, ',', '.');
            if ($this->due_date) {
                $lines[] = "Jatuh Tempo : " . $this->due_date->translatedFormat('d M Y');
            }
        } else {
            $methodName = $this->payment_method === 'cash' ? 'Tunai (Cash)' : strtoupper($this->payment_method);
            $lines[] = "Metode      : " . $methodName;
            $lines[] = "Bayar       : Rp " . number_format($this->paid_amount, 0, ',', '.');
            $lines[] = "Kembalian   : Rp " . number_format($this->change_amount, 0, ',', '.');
        }

        $lines[] = "================================";
        if ($settings->footer_notes) {
            $lines[] = "_" . $settings->footer_notes . "_";
        }
        if ($settings->footer_link) {
            $lines[] = "E-Receipt: " . $settings->footer_link;
        }

        return implode("\n", $lines);
    }

    public function getWhatsAppUrl(?string $phone = null, ?ReceiptSetting $settings = null): string
    {
        $targetPhone = $phone ?: ($this->customer?->phone ?? '');
        $cleanPhone = preg_replace('/[^0-9]/', '', (string)$targetPhone);

        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '62' . substr($cleanPhone, 1);
        } elseif (!str_starts_with($cleanPhone, '62') && !empty($cleanPhone)) {
            $cleanPhone = '62' . $cleanPhone;
        }

        $message = $this->generateWhatsAppMessage($settings);
        $encoded = urlencode($message);

        if (!empty($cleanPhone)) {
            return "https://api.whatsapp.com/send?phone={$cleanPhone}&text={$encoded}";
        }

        return "https://api.whatsapp.com/send?text={$encoded}";
    }

    public static function generateInvoiceNumber(): string
    {
        $prefix = 'INV-' . date('Ymd') . '-';
        $lastTransaction = self::where('invoice_number', 'LIKE', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastTransaction) {
            $number = 1;
        } else {
            $lastNumber = (int) substr($lastTransaction->invoice_number, -4);
            $number = $lastNumber + 1;
        }

        return $prefix . str_pad((string) $number, 4, '0', STR_PAD_LEFT);
    }
}
