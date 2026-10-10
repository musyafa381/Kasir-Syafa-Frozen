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
        Schema::create('backup_logs', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->string('file_path');
            $table->unsignedBigInteger('file_size')->default(0); // in bytes
            $table->string('period_label'); // e.g. "September 2026" or "01 Sep 2026 - 18 Sep 2026"
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedInteger('total_transactions')->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->string('backup_format')->default('csv'); // 'csv' or 'json'
            $table->string('google_drive_link')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backup_logs');
    }
};
