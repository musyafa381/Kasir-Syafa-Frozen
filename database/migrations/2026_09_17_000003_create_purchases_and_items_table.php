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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique(); // e.g. PO-20260917-0001
            $table->string('supplier_invoice_number')->nullable(); // Reference no from supplier
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users'); // staff who created/received
            $table->date('purchase_date');
            $table->decimal('total_amount', 14, 2)->default(0);
            $table->decimal('paid_amount', 14, 2)->default(0);
            $table->string('payment_type')->default('cash'); // cash, transfer, tempo
            $table->string('payment_status')->default('paid'); // paid, partial, unpaid
            $table->date('due_date')->nullable(); // for tempo / credit
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('purchase_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained('purchases')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products');
            $table->string('product_name');
            $table->integer('quantity');
            $table->decimal('unit_cost', 12, 2);
            $table->decimal('subtotal', 14, 2);
            $table->boolean('update_cost_price')->default(true);
            $table->timestamps();
        });

        Schema::table('stock_mutations', function (Blueprint $table) {
            $table->foreignId('supplier_id')->nullable()->after('user_id')->constrained('suppliers')->nullOnDelete();
            $table->foreignId('purchase_id')->nullable()->after('supplier_id')->constrained('purchases')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_mutations', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
            $table->dropForeign(['purchase_id']);
            $table->dropColumn(['supplier_id', 'purchase_id']);
        });

        Schema::dropIfExists('purchase_items');
        Schema::dropIfExists('purchases');
    }
};
