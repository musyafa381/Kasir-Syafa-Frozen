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
        Schema::create('restock_plans', function (Blueprint $table) {
            $table->id();
            $table->string('plan_number')->unique(); // e.g. PO-PLAN-20260922-0001
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('purchase_id')->nullable()->constrained('purchases')->nullOnDelete();
            $table->string('status')->default('draft'); // draft, ordered, received, cancelled
            $table->decimal('total_estimated_cost', 14, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamp('ordered_at')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamps();
        });

        Schema::create('restock_plan_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restock_plan_id')->constrained('restock_plans')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products');
            $table->string('product_name');
            $table->integer('current_stock')->default(0);
            $table->integer('min_stock_alert')->default(0);
            $table->integer('quantity_ordered')->default(1);
            $table->string('unit')->default('pcs');
            $table->decimal('unit_cost', 12, 2)->default(0);
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restock_plan_items');
        Schema::dropIfExists('restock_plans');
    }
};
