<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_code',
        'user_id',
        'name',
        'phone',
        'position',
        'employment_status',
        'base_salary',
        'daily_allowance',
        'join_date',
        'bank_name',
        'bank_account_number',
        'bank_account_holder',
        'address',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'base_salary' => 'decimal:2',
            'daily_allowance' => 'decimal:2',
            'join_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(EmployeeLoan::class);
    }

    public function activeLoans(): HasMany
    {
        return $this->hasMany(EmployeeLoan::class)->whereIn('status', ['unpaid', 'partial']);
    }

    /**
     * Hitung total sisa kasbon yang belum lunas
     */
    public function getTotalRemainingLoanAttribute(): float
    {
        return (float) $this->activeLoans()->sum('remaining_amount');
    }

    /**
     * Hitung total gaji yang pernah diterima
     */
    public function getTotalPaidSalaryAttribute(): float
    {
        return (float) $this->payrolls()->where('payment_status', 'paid')->sum('net_salary');
    }

    public static function generateEmployeeCode(): string
    {
        $prefix = 'EMP-';
        $last = self::where('employee_code', 'LIKE', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        if (!$last) {
            $number = 1;
        } else {
            $lastNumber = (int) str_replace($prefix, '', $last->employee_code);
            $number = $lastNumber + 1;
        }

        return $prefix . str_pad((string) $number, 3, '0', STR_PAD_LEFT);
    }
}
