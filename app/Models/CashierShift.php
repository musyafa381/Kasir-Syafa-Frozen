<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashierShift extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'outlet_id',
        'opened_at',
        'closed_at',
        'starting_cash',
        'total_cash_sales',
        'total_qris_sales',
        'total_transfer_sales',
        'total_tempo_sales',
        'total_split_sales',
        'total_transactions_count',
        'expected_ending_cash',
        'actual_ending_cash',
        'cash_difference',
        'status',
        'opening_notes',
        'closing_notes',
    ];

    protected function casts(): array
    {
        return [
            'opened_at' => 'datetime',
            'closed_at' => 'datetime',
            'starting_cash' => 'decimal:2',
            'total_cash_sales' => 'decimal:2',
            'total_qris_sales' => 'decimal:2',
            'total_transfer_sales' => 'decimal:2',
            'total_tempo_sales' => 'decimal:2',
            'total_split_sales' => 'decimal:2',
            'total_transactions_count' => 'integer',
            'expected_ending_cash' => 'decimal:2',
            'actual_ending_cash' => 'decimal:2',
            'cash_difference' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', 'open');
    }

    public function scopeClosed(Builder $query): Builder
    {
        return $query->where('status', 'closed');
    }

    public static function getActiveShift(int $userId, ?int $outletId = null): ?self
    {
        return self::where('user_id', $userId)
            ->when($outletId, fn($q) => $q->where('outlet_id', $outletId))
            ->where('status', 'open')
            ->latest('opened_at')
            ->first();
    }

    /**
     * Hitung total penjualan tunai & non tunai dari transaksi yang terkait dengan shift ini
     */
    public function recalculateTotals(): void
    {
        $transactions = $this->transactions()->where('status', 'completed')->get();

        $cashSales = 0;
        $qrisSales = 0;
        $transferSales = 0;
        $tempoSales = 0;
        $splitSales = 0;
        $splitCashPortion = 0;

        foreach ($transactions as $trx) {
            if ($trx->payment_method === 'cash') {
                $cashSales += (float) $trx->grand_total;
            } elseif ($trx->payment_method === 'qris') {
                $qrisSales += (float) $trx->grand_total;
            } elseif ($trx->payment_method === 'transfer') {
                $transferSales += (float) $trx->grand_total;
            } elseif ($trx->payment_method === 'tempo') {
                $tempoSales += (float) $trx->grand_total;
                // Jika ada pembayaran uang muka / DP tunai saat transaksi tempo
                if ($trx->paid_amount > 0) {
                    $cashSales += (float) $trx->paid_amount;
                }
            } elseif ($trx->payment_method === 'split') {
                $splitSales += (float) $trx->grand_total;
                $splitCashPortion += (float) $trx->split_cash_amount;
                if ($trx->split_non_cash_method === 'qris') {
                    $qrisSales += (float) $trx->split_non_cash_amount;
                } else {
                    $transferSales += (float) $trx->split_non_cash_amount;
                }
            }
        }

        $totalCashReceived = $cashSales + $splitCashPortion;
        $expectedCash = (float) $this->starting_cash + $totalCashReceived;

        $this->update([
            'total_cash_sales' => $totalCashReceived,
            'total_qris_sales' => $qrisSales,
            'total_transfer_sales' => $transferSales,
            'total_tempo_sales' => $tempoSales,
            'total_split_sales' => $splitSales,
            'total_transactions_count' => $transactions->count(),
            'expected_ending_cash' => $expectedCash,
        ]);
    }
}
