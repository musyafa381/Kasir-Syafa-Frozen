<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Nugget & Sosis',
                'description' => 'Aneka nugget ayam, sosis sapi, dan bratwurst premium',
                'color' => 'blue',
            ],
            [
                'name' => 'Dimsum & Siomay',
                'description' => 'Dimsum frozen, siomay ayam, gyoza, dan kuotie',
                'color' => 'emerald',
            ],
            [
                'name' => 'Olahan Seafood & Ikan',
                'description' => 'Bakso ikan, crab stick, fish cake, dan olahan salmon',
                'color' => 'cyan',
            ],
            [
                'name' => 'Kentang & Sayur Beku',
                'description' => 'French fries shoestring, crinkle, wedges, dan mix veggies',
                'color' => 'amber',
            ],
            [
                'name' => 'Daging Sapi & Unggas',
                'description' => 'Slice beef premium, patty burger, ayam katsu, dan chicken wing',
                'color' => 'rose',
            ],
            [
                'name' => 'Camilan & Roti Beku',
                'description' => 'Cireng salju, donat kentang, kebab mini, dan roti maryam',
                'color' => 'purple',
            ],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(
                ['name' => $cat['name']],
                [
                    'slug' => Str::slug($cat['name']),
                    'description' => $cat['description'],
                    'color' => $cat['color'],
                ]
            );
        }
    }
}
