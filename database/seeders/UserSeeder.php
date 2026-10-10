<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin (Pemilik Toko)
        User::updateOrCreate(
            ['email' => 'admin@syafafrozen.com'],
            [
                'name' => 'Owner Syafa Frozen',
                'phone' => '081234567890',
                'role' => 'admin',
                'is_active' => true,
                'password' => Hash::make('admin123'),
            ]
        );

        // Kasir 1
        User::updateOrCreate(
            ['email' => 'kasir@syafafrozen.com'],
            [
                'name' => 'Kasir Utama',
                'phone' => '081298765432',
                'role' => 'cashier',
                'is_active' => true,
                'password' => Hash::make('kasir123'),
            ]
        );
    }
}
