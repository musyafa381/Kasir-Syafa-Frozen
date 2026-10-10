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
        // 1. Tabel Shift Kasir (Cashier Shifts / Cash Drawer Session)
        Schema::create('cashier_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('outlet_id')->nullable()->constrained()->nullOnDelete();
            $table->dateTime('opened_at');
            $table->dateTime('closed_at')->nullable();
            $table->decimal('starting_cash', 15, 2)->default(0);
            $table->decimal('total_cash_sales', 15, 2)->default(0);
            $table->decimal('total_qris_sales', 15, 2)->default(0);
            $table->decimal('total_transfer_sales', 15, 2)->default(0);
            $table->decimal('total_tempo_sales', 15, 2)->default(0);
            $table->decimal('total_split_sales', 15, 2)->default(0);
            $table->unsignedInteger('total_transactions_count')->default(0);
            $table->decimal('expected_ending_cash', 15, 2)->default(0);
            $table->decimal('actual_ending_cash', 15, 2)->nullable();
            $table->decimal('cash_difference', 15, 2)->nullable();
            $table->string('status', 20)->default('open'); // 'open', 'closed'
            $table->text('opening_notes')->nullable();
            $table->text('closing_notes')->nullable();
            $table->timestamps();
        });

        // 2. Tabel Antrean Tertahan (Hold / Suspended Transactions)
        Schema::create('hold_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('hold_code', 30)->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('outlet_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_name')->default('Pelanggan Umum');
            $table->longText('cart_data');
            $table->foreignId('discount_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('tax_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('enable_tax')->default(false);
            $table->boolean('is_loyalty')->default(false);
            $table->string('notes')->nullable();
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);
            $table->timestamps();
        });

        // 3. Tambahkan kolom shift & split payment pada tabel transactions
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('cashier_shift_id')->nullable()->after('outlet_id')->constrained('cashier_shifts')->nullOnDelete();
            $table->decimal('split_cash_amount', 15, 2)->default(0)->after('payment_method');
            $table->decimal('split_non_cash_amount', 15, 2)->default(0)->after('split_cash_amount');
            $table->string('split_non_cash_method', 30)->nullable()->after('split_non_cash_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['cashier_shift_id']);
            $table->dropColumn(['cashier_shift_id', 'split_cash_amount', 'split_non_cash_amount', 'split_non_cash_method']);
        });

        Schema::dropIfExists('hold_transactions');
        Schema::dropIfExists('cashier_shifts');
    }
};
