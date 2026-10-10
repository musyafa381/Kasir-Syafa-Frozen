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
        Schema::table('transaction_items', function (Blueprint $table) {
            $table->string('price_type', 20)->default('regular')->after('unit_price'); // 'regular', 'loyalty', 'custom'
            $table->string('discount_type', 20)->nullable()->after('price_type'); // 'fixed', 'percent'
            $table->decimal('discount_rate', 15, 2)->default(0)->after('discount_type');
            $table->decimal('discount_amount', 15, 2)->default(0)->after('discount_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaction_items', function (Blueprint $table) {
            $table->dropColumn(['price_type', 'discount_type', 'discount_rate', 'discount_amount']);
        });
    }
};
