<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HoldTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'hold_code',
        'user_id',
        'outlet_id',
        'customer_id',
        'customer_name',
        'cart_data',
        'discount_id',
        'tax_id',
        'enable_tax',
        'is_loyalty',
        'notes',
        'subtotal',
        'grand_total',
    ];

    protected function casts(): array
    {
        return [
            'cart_data' => 'array',
            'enable_tax' => 'boolean',
            'is_loyalty' => 'boolean',
            'subtotal' => 'decimal:2',
            'grand_total' => 'decimal:2',
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

    public static function generateHoldCode(): string
    {
        return 'HOLD-' . strtoupper(substr(uniqid(), -6));
    }
}
