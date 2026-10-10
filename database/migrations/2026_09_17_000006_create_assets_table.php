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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_code')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name'); // e.g. Chest Freezer RSA 500L (Unit A)
            $table->string('category', 50)->default('freezer_refrigeration'); // freezer_refrigeration, pos_hardware, operational_equipment, furniture_facility, other
            $table->string('brand')->nullable(); // e.g. RSA, Sanden, Gea
            $table->string('specs')->nullable(); // e.g. 500 Liter, 240W, Suhu -22C
            $table->string('location')->nullable(); // e.g. Toko Depan, Gudang Belakang
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_cost', 15, 2)->default(0);
            $table->string('condition', 30)->default('good'); // good, fair, maintenance, broken
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
