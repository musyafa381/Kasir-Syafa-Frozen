<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'expense_number',
        'user_id',
        'outlet_id',
        'category',
        'title',
        'amount',
        'expense_date',
        'payment_method',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'expense_date' => 'date',
        ];
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payroll(): HasOne
    {
        return $this->hasOne(Payroll::class);
    }

    public function scopeThisMonth(Builder $query): Builder
    {
        return $query->whereYear('expense_date', Carbon::now()->year)
            ->whereMonth('expense_date', Carbon::now()->month);
    }

    public function scopeCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    public static function getCategoryLabel(string $category): string
    {
        return match ($category) {
            'electricity_utility' => 'Listrik & Utilitas PLN/Air',
            'salary_labor' => 'Gaji Karyawan & Staf',
            'packaging_supplies' => 'Plastik, Ice Gel & Packing',
            'maintenance_repair' => 'Servis & Perawatan Freezer',
            'rent_premises' => 'Sewa Tempat / Kios',
            'other_operational' => 'Operasional Toko Lainnya',
            default => 'Lain-lain',
        };
    }

    public static function generateExpenseNumber(): string
    {
        $prefix = 'EXP-' . date('Ymd') . '-';
        $last = self::where('expense_number', 'LIKE', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        if (!$last) {
            $number = 1;
        } else {
            $lastNumber = (int) substr($last->expense_number, -4);
            $number = $lastNumber + 1;
        }

        return $prefix . str_pad((string) $number, 4, '0', STR_PAD_LEFT);
    }
}
