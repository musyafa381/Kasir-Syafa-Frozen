<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cashierUser = User::where('email', 'kasir@syafafrozen.com')->first();

        // 1. Kasir Utama
        Employee::updateOrCreate(
            ['employee_code' => 'EMP-001'],
            [
                'user_id' => $cashierUser?->id,
                'name' => 'Siti Kasir Utama',
                'phone' => '081298765432',
                'position' => 'Kasir POS',
                'employment_status' => 'tetap',
                'base_salary' => 2500000,
                'daily_allowance' => 20000,
                'join_date' => '2025-01-10',
                'bank_name' => 'BCA',
                'bank_account_number' => '8801234567',
                'bank_account_holder' => 'Siti Kasir',
                'address' => 'Jl. Merdeka No. 12',
                'is_active' => true,
            ]
        );

        // 2. Mas Yulai (Staff Gudang & Operasional)
        Employee::updateOrCreate(
            ['employee_code' => 'EMP-002'],
            [
                'user_id' => null,
                'name' => 'Mas Yulai',
                'phone' => '081345678901',
                'position' => 'Staff Gudang & Freezer',
                'employment_status' => 'tetap',
                'base_salary' => 2800000,
                'daily_allowance' => 25000,
                'join_date' => '2024-06-01',
                'bank_name' => 'BRI',
                'bank_account_number' => '012301098765501',
                'bank_account_holder' => 'Yulai',
                'address' => 'Jl. Mawar No. 45',
                'is_active' => true,
            ]
        );
    }
}
