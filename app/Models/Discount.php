<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Discount extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'type',
        'value',
        'min_purchase',
        'max_discount_amount',
        'start_date',
        'end_date',
        'is_active',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'min_purchase' => 'decimal:2',
            'max_discount_amount' => 'decimal:2',
            'start_date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        $today = Carbon::today();
        return $query->where('is_active', true)
            ->where(function ($q) use ($today) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', $today);
            })
            ->where(function ($q) use ($today) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $today);
            });
    }

    /**
     * Calculate discount amount given a subtotal.
     */
    public function calculateDiscount(float $subtotal): float
    {
        if ($subtotal < (float) $this->min_purchase) {
            return 0.0;
        }

        if ($this->type === 'percentage') {
            $discount = ($subtotal * (float) $this->value) / 100;
            if ($this->max_discount_amount !== null && (float) $this->max_discount_amount > 0) {
                $discount = min($discount, (float) $this->max_discount_amount);
            }
        } else {
            // Fixed discount
            $discount = min((float) $this->value, $subtotal);
        }

        return max(0.0, round($discount, 2));
    }

    public function getFormattedValueAttribute(): string
    {
        if ($this->type === 'percentage') {
            return rtrim(rtrim(number_format($this->value, 2, '.', ''), '0'), '.') . '%';
        }
        return 'Rp ' . number_format($this->value, 0, ',', '.');
    }

    public function getFormattedMinPurchaseAttribute(): string
    {
        return 'Rp ' . number_format($this->min_purchase, 0, ',', '.');
    }

    public function getStatusLabelAttribute(): string
    {
        if (!$this->is_active) {
            return 'Nonaktif';
        }

        $today = Carbon::today();
        if ($this->start_date && $this->start_date->gt($today)) {
            return 'Belum Mulai';
        }

        if ($this->end_date && $this->end_date->lt($today)) {
            return 'Kedaluwarsa';
        }

        return 'Aktif';
    }
}
