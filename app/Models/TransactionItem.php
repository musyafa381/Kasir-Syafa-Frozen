<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'product_id',
        'product_name',
        'unit_type',
        'pieces_per_unit',
        'cost_price',
        'unit_price',
        'price_type',
        'discount_type',
        'discount_rate',
        'discount_amount',
        'quantity',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'unit_type' => 'string',
            'pieces_per_unit' => 'integer',
            'cost_price' => 'decimal:2',
            'unit_price' => 'decimal:2',
            'price_type' => 'string',
            'discount_type' => 'string',
            'discount_rate' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'quantity' => 'integer',
            'subtotal' => 'decimal:2',
        ];
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function isBox(): bool
    {
        return $this->unit_type === 'box';
    }

    public function getFormattedQuantityAttribute(): string
    {
        if ($this->isBox()) {
            $totalPieces = $this->quantity * ($this->pieces_per_unit ?: 1);
            return "{$this->quantity} Dus ({$totalPieces} pcs)";
        }

        return "{$this->quantity} " . ($this->product ? $this->product->unit : 'pcs');
    }
}
