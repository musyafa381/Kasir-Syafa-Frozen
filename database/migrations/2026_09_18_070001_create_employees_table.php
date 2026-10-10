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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_code', 30)->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name', 100);
            $table->string('phone', 25)->nullable();
            $table->string('position', 50)->default('Kasir'); // Kasir, Staff Gudang, Pramuniaga, Manager, Driver, dll
            $table->string('employment_status', 30)->default('tetap'); // tetap, kontrak, harian, part_time
            $table->decimal('base_salary', 15, 2)->default(0);
            $table->decimal('daily_allowance', 15, 2)->default(0);
            $table->date('join_date')->nullable();
            $table->string('bank_name', 50)->nullable(); // BCA, BRI, Mandiri, Cash, dll
            $table->string('bank_account_number', 50)->nullable();
            $table->string('bank_account_holder', 100)->nullable();
            $table->text('address')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
