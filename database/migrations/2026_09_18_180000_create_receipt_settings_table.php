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
        Schema::create('receipt_settings', function (Blueprint $table) {
            $table->id();
            $table->string('store_name')->default('Syafa Frozen Food');
            $table->text('store_address')->nullable();
            $table->string('store_phone')->nullable();
            $table->text('header_notes')->nullable();
            $table->text('footer_notes')->nullable();
            $table->string('footer_link')->nullable();
            $table->boolean('show_cashier_name')->default(true);
            $table->boolean('show_customer_name')->default(true);
            $table->string('default_paper_size', 10)->default('58mm');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipt_settings');
    }
};
