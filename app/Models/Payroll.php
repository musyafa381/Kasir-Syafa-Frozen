<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'payroll_number',
        'employee_id',
        'expense_id',
        'period_month',
        'period_year',
        'payment_date',
        'base_salary',
        'allowance_amount',
        'allowance_notes',
        'loan_deduction',
        'other_deduction',
        'deduction_notes',
        'net_salary',
        'payment_method',
        'payment_status',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'period_month' => 'integer',
            'period_year' => 'integer',
            'payment_date' => 'date',
            'base_salary' => 'decimal:2',
            'allowance_amount' => 'decimal:2',
            'loan_deduction' => 'decimal:2',
            'other_deduction' => 'decimal:2',
            'net_salary' => 'decimal:2',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function expense(): BelongsTo
    {
        return $this->belongsTo(Expense::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function loanPayments(): HasMany
    {
        return $this->hasMany(EmployeeLoanPayment::class);
    }

    public function getPeriodLabelAttribute(): string
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        return ($months[$this->period_month] ?? '') . ' ' . $this->period_year;
    }

    public static function generatePayrollNumber(): string
    {
        $prefix = 'PAY-' . date('Ymd') . '-';
        $last = self::where('payroll_number', 'LIKE', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        if (!$last) {
            $number = 1;
        } else {
            $lastNumber = (int) substr($last->payroll_number, -4);
            $number = $lastNumber + 1;
        }

        return $prefix . str_pad((string) $number, 4, '0', STR_PAD_LEFT);
    }
}
