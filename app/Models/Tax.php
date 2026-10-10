<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tax extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'rate',
        'is_active',
        'is_default',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'rate' => 'decimal:2',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ];
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault(Builder $query): Builder
    {
        return $query->where('is_default', true);
    }

    public function getFormattedRateAttribute(): string
    {
        // Format e.g. 11% or 10.5%
        $rate = (float) $this->rate;
        return (floor($rate) == $rate ? number_format($rate, 0) : number_format($rate, 2)) . '%';
    }

    public function calculateTax(float $taxableAmount): float
    {
        if (!$this->is_active || $taxableAmount <= 0) {
            return 0.0;
        }

        $tax = $taxableAmount * ((float) $this->rate / 100);
        return round($tax, 2);
    }
}
