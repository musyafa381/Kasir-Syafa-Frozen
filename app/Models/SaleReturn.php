<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SaleReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'return_number',
        'transaction_id',
        'invoice_number',
        'user_id',
        'customer_id',
        'total_refund',
        'total_items',
        'reason',
        'notes',
        'restock',
    ];

    protected function casts(): array
    {
        return [
            'total_refund' => 'decimal:2',
            'total_items' => 'integer',
            'restock' => 'boolean',
        ];
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleReturnItem::class);
    }

    public static function generateReturnNumber(): string
    {
        $date = Carbon::now()->format('Ymd');
        $prefix = 'RET-' . $date . '-';
        $latest = static::where('return_number', 'LIKE', $prefix . '%')
            ->orderBy('return_number', 'desc')
            ->first();

        if ($latest) {
            $lastSequence = (int) substr($latest->return_number, -4);
            $newSequence = str_pad($lastSequence + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newSequence = '0001';
        }

        return $prefix . $newSequence;
    }
}
