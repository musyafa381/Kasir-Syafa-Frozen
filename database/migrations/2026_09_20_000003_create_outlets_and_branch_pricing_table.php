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
        // 1. Tabel Cabang Toko (Outlets)
        Schema::create('outlets', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 150);
            $table->text('address')->nullable();
            $table->string('phone', 30)->nullable();
            $table->text('receipt_header')->nullable();
            $table->text('receipt_footer')->nullable();
            $table->boolean('is_main')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Tabel Harga & Stok Khusus per Cabang (Outlet Products)
        Schema::create('outlet_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')->constrained('outlets')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->decimal('selling_price', 12, 2)->nullable();
            $table->decimal('loyalty_price', 12, 2)->nullable();
            $table->decimal('box_selling_price', 12, 2)->nullable();
            $table->decimal('box_loyalty_price', 12, 2)->nullable();
            $table->decimal('cost_price', 12, 2)->nullable();
            $table->integer('stock')->default(0);
            $table->integer('min_stock_alert')->nullable();
            $table->timestamps();

            $table->unique(['outlet_id', 'product_id']);
        });

        // 3. Tambahkan outlet_id ke tabel yang berelasi
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'outlet_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('outlet_id')->nullable()->after('role')->constrained('outlets')->nullOnDelete();
            });
        }

        if (Schema::hasTable('transactions') && !Schema::hasColumn('transactions', 'outlet_id')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->foreignId('outlet_id')->nullable()->after('user_id')->constrained('outlets')->nullOnDelete();
            });
        }

        if (Schema::hasTable('expenses') && !Schema::hasColumn('expenses', 'outlet_id')) {
            Schema::table('expenses', function (Blueprint $table) {
                $table->foreignId('outlet_id')->nullable()->after('user_id')->constrained('outlets')->nullOnDelete();
            });
        }

        if (Schema::hasTable('purchases') && !Schema::hasColumn('purchases', 'outlet_id')) {
            Schema::table('purchases', function (Blueprint $table) {
                $table->foreignId('outlet_id')->nullable()->after('supplier_id')->constrained('outlets')->nullOnDelete();
            });
        }

        if (Schema::hasTable('stock_mutations') && !Schema::hasColumn('stock_mutations', 'outlet_id')) {
            Schema::table('stock_mutations', function (Blueprint $table) {
                $table->foreignId('outlet_id')->nullable()->after('product_id')->constrained('outlets')->nullOnDelete();
            });
        }

        // 4. Inisialisasi Data Default: Cabang Utama (Pusat)
        $now = now();
        $mainOutletId = DB::table('outlets')->insertGetId([
            'code' => 'CB-001',
            'name' => 'Cabang Utama (Pusat)',
            'address' => 'Toko Pusat Syafa Frozen Food',
            'phone' => '08123456789',
            'is_main' => true,
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Hubungkan semua user lama, transaksi lama, pengeluaran lama, dan mutasi lama ke Cabang Utama
        DB::table('users')->whereNull('outlet_id')->update(['outlet_id' => $mainOutletId]);
        DB::table('transactions')->whereNull('outlet_id')->update(['outlet_id' => $mainOutletId]);
        DB::table('expenses')->whereNull('outlet_id')->update(['outlet_id' => $mainOutletId]);
        DB::table('purchases')->whereNull('outlet_id')->update(['outlet_id' => $mainOutletId]);
        DB::table('stock_mutations')->whereNull('outlet_id')->update(['outlet_id' => $mainOutletId]);

        // Sinkronkan seluruh produk yang sudah ada ke outlet_products Cabang Utama
        $existingProducts = DB::table('products')->get();
        foreach ($existingProducts as $p) {
            DB::table('outlet_products')->insertOrIgnore([
                'outlet_id' => $mainOutletId,
                'product_id' => $p->id,
                'selling_price' => $p->selling_price,
                'loyalty_price' => $p->loyalty_price ?? $p->selling_price,
                'box_selling_price' => $p->box_selling_price ?? null,
                'box_loyalty_price' => $p->box_loyalty_price ?? null,
                'cost_price' => $p->cost_price,
                'stock' => $p->stock,
                'min_stock_alert' => $p->min_stock_alert,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('stock_mutations') && Schema::hasColumn('stock_mutations', 'outlet_id')) {
            Schema::table('stock_mutations', function (Blueprint $table) {
                $table->dropConstrainedForeignId('outlet_id');
            });
        }

        if (Schema::hasTable('purchases') && Schema::hasColumn('purchases', 'outlet_id')) {
            Schema::table('purchases', function (Blueprint $table) {
                $table->dropConstrainedForeignId('outlet_id');
            });
        }

        if (Schema::hasTable('expenses') && Schema::hasColumn('expenses', 'outlet_id')) {
            Schema::table('expenses', function (Blueprint $table) {
                $table->dropConstrainedForeignId('outlet_id');
            });
        }

        if (Schema::hasTable('transactions') && Schema::hasColumn('transactions', 'outlet_id')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->dropConstrainedForeignId('outlet_id');
            });
        }

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'outlet_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropConstrainedForeignId('outlet_id');
            });
        }

        Schema::dropIfExists('outlet_products');
        Schema::dropIfExists('outlets');
    }
};
