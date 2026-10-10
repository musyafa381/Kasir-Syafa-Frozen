<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->string('payroll_number', 30)->unique();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('expense_id')->nullable()->constrained('expenses')->nullOnDelete();
            $table->unsignedTinyInteger('period_month'); // 1-12
            $table->unsignedSmallInteger('period_year'); // e.g. 2026
            $table->date('payment_date');
            
            // Income Breakdown
            $table->decimal('base_salary', 15, 2)->default(0);
            $table->decimal('allowance_amount', 15, 2)->default(0); // Tunjangan / Bonus / Lembur
            $table->string('allowance_notes', 255)->nullable();
            
            // Deduction Breakdown
            $table->decimal('loan_deduction', 15, 2)->default(0); // Potongan Kasbon
            $table->decimal('other_deduction', 15, 2)->default(0); // Potongan Absen/Keterlambatan/Lainnya
            $table->string('deduction_notes', 255)->nullable();
            
            // Net Total
            $table->decimal('net_salary', 15, 2); // (base_salary + allowance_amount) - (loan_deduction + other_deduction)
            
            // Payment info
            $table->string('payment_method', 20)->default('cash'); // cash, transfer
            $table->string('payment_status', 20)->default('paid'); // paid, pending
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
