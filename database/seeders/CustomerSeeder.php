<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = [
            [
                'name' => 'Ibu Hajjah Maryam',
                'phone' => '081234567001',
                'member_code' => 'MBR-0001',
                'address' => 'Jl. Mawar No. 12, Sukajadi',
                'notes' => 'Pelanggan setia mingguan',
                'is_active' => true,
            ],
            [
                'name' => 'Bpk. Hendra (Reseller)',
                'phone' => '081234567002',
                'member_code' => 'MBR-0002',
                'address' => 'Komplek Griya Indah Blok C-05',
                'notes' => 'Reseller paket sosis & nugget',
                'is_active' => true,
            ],
            [
                'name' => 'Kedai Dimsum Berkah',
                'phone' => '081234567003',
                'member_code' => 'MBR-0003',
                'address' => 'Foodcourt Pujasera Stand 4',
                'notes' => 'Langganan dimsum & siomay',
                'is_active' => true,
            ],
        ];

        foreach ($customers as $c) {
            Customer::updateOrCreate(
                ['member_code' => $c['member_code']],
                $c
            );
        }
    }
}
