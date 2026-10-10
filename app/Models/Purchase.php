<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'supplier_invoice_number',
        'supplier_id',
        'outlet_id',
        'user_id',
        'purchase_date',
        'total_amount',
        'paid_amount',
        'payment_type',
        'payment_status',
        'due_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'purchase_date' => 'date',
            'due_date' => 'date',
            'total_amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
        ];
    }

    public static function generateInvoiceNumber(): string
    {
        $datePrefix = Carbon::now()->format('Ymd');
        $prefix = 'PO-' . $datePrefix . '-';

        $lastPurchase = static::where('invoice_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastPurchase) {
            $lastSequence = (int) substr($lastPurchase->invoice_number, -4);
            $newSequence = str_pad((string) ($lastSequence + 1), 4, '0', STR_PAD_LEFT);
        } else {
            $newSequence = '0001';
        }

        return $prefix . $newSequence;
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function stockMutations(): HasMany
    {
        return $this->hasMany(StockMutation::class);
    }

    public function restockPlan(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(RestockPlan::class);
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0, (float) $this->total_amount - (float) $this->paid_amount);
    }

    public function getIsOverdueAttribute(): bool
    {
        if ($this->payment_status === 'paid' || !$this->due_date) {
            return false;
        }

        return Carbon::now()->isAfter($this->due_date->endOfDay());
    }

    public function scopeTempo(Builder $query): Builder
    {
        return $query->where('payment_type', 'tempo');
    }

    public function scopeUnpaid(Builder $query): Builder
    {
        return $query->whereIn('payment_status', ['unpaid', 'partial']);
    }
}
