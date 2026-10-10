<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('payment_status', 20)->default('paid')->after('payment_method'); // paid, partial, unpaid
            $table->date('due_date')->nullable()->after('payment_status');
        });

        // Convert payment_method column to string in MySQL to allow 'tempo' along with 'cash', 'qris', 'transfer'
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE transactions MODIFY payment_method VARCHAR(50) DEFAULT 'cash'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['payment_status', 'due_date']);
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE transactions MODIFY payment_method ENUM('cash', 'qris', 'transfer') DEFAULT 'cash'");
        }
    }
};
