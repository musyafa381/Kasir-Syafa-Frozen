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
        Schema::table('products', function (Blueprint $table) {
            $table->string('box_barcode', 50)->nullable()->unique()->after('barcode');
            $table->integer('pieces_per_box')->nullable()->after('unit');
            $table->decimal('box_selling_price', 15, 2)->nullable()->after('loyalty_price');
            $table->decimal('box_loyalty_price', 15, 2)->nullable()->after('box_selling_price');
        });

        Schema::table('transaction_items', function (Blueprint $table) {
            $table->string('unit_type', 20)->default('unit')->after('product_name'); // 'unit' or 'box'
            $table->integer('pieces_per_unit')->default(1)->after('unit_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaction_items', function (Blueprint $table) {
            $table->dropColumn(['unit_type', 'pieces_per_unit']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'box_barcode',
                'pieces_per_box',
                'box_selling_price',
                'box_loyalty_price',
            ]);
        });
    }
};
