<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $catNugget = Category::where('name', 'Nugget & Sosis')->first();
        $catDimsum = Category::where('name', 'Dimsum & Siomay')->first();
        $catSeafood = Category::where('name', 'Olahan Seafood & Ikan')->first();
        $catKentang = Category::where('name', 'Kentang & Sayur Beku')->first();
        $catDaging = Category::where('name', 'Daging Sapi & Unggas')->first();
        $catCamilan = Category::where('name', 'Camilan & Roti Beku')->first();

        $products = [
            // Nugget & Sosis
            [
                'category_id' => $catNugget?->id,
                'name' => 'Fiesta Crispy Nugget 500g',
                'barcode' => '8992775210011',
                'cost_price' => 45000,
                'selling_price' => 52000,
                'loyalty_price' => 49000,
                'stock' => 25,
                'min_stock_alert' => 5,
                'unit' => 'pack',
            ],
            [
                'category_id' => $catNugget?->id,
                'name' => 'Kanzler Singles Sosis Keju 65g',
                'barcode' => '8993175532014',
                'cost_price' => 7500,
                'selling_price' => 9500,
                'loyalty_price' => 8800,
                'stock' => 50,
                'min_stock_alert' => 10,
                'unit' => 'pcs',
            ],
            [
                'category_id' => $catNugget?->id,
                'name' => 'Champ Chicken Nugget 500g',
                'barcode' => '8992775220027',
                'cost_price' => 34000,
                'selling_price' => 40000,
                'loyalty_price' => 37500,
                'stock' => 20,
                'min_stock_alert' => 5,
                'unit' => 'pack',
            ],
            [
                'category_id' => $catNugget?->id,
                'name' => 'Kanzler Beef Cocktail 500g',
                'barcode' => '8993175532052',
                'cost_price' => 55000,
                'selling_price' => 64000,
                'loyalty_price' => 60000,
                'stock' => 15,
                'min_stock_alert' => 4,
                'unit' => 'pack',
            ],

            // Dimsum & Siomay
            [
                'category_id' => $catDimsum?->id,
                'name' => 'Dimsum Ayam Mix Isi 15 + Saus',
                'barcode' => '8998822100015',
                'cost_price' => 26000,
                'selling_price' => 33000,
                'loyalty_price' => 30000,
                'stock' => 30,
                'min_stock_alert' => 6,
                'unit' => 'pack',
            ],
            [
                'category_id' => $catDimsum?->id,
                'name' => 'Siomay Ikan Premium Isi 10',
                'barcode' => '8998822100022',
                'cost_price' => 20000,
                'selling_price' => 26000,
                'loyalty_price' => 23500,
                'stock' => 18,
                'min_stock_alert' => 5,
                'unit' => 'pack',
            ],

            // Olahan Seafood
            [
                'category_id' => $catSeafood?->id,
                'name' => 'Cedea Salmon Ball 500g',
                'barcode' => '8997025810034',
                'cost_price' => 28000,
                'selling_price' => 35000,
                'loyalty_price' => 32000,
                'stock' => 22,
                'min_stock_alert' => 5,
                'unit' => 'pack',
            ],
            [
                'category_id' => $catSeafood?->id,
                'name' => 'Cedea Crab Stick 250g',
                'barcode' => '8997025810041',
                'cost_price' => 15000,
                'selling_price' => 19500,
                'loyalty_price' => 17500,
                'stock' => 30,
                'min_stock_alert' => 5,
                'unit' => 'pack',
            ],
            [
                'category_id' => $catSeafood?->id,
                'name' => 'Minaku Bola Udang 500g',
                'barcode' => '8994512001018',
                'cost_price' => 31000,
                'selling_price' => 38000,
                'loyalty_price' => 35000,
                'stock' => 12,
                'min_stock_alert' => 3,
                'unit' => 'pack',
            ],

            // Kentang & Sayur Beku
            [
                'category_id' => $catKentang?->id,
                'name' => 'French Fries Shoestring 1kg Golden Farm',
                'barcode' => '8991002341019',
                'cost_price' => 32000,
                'selling_price' => 39000,
                'loyalty_price' => 36000,
                'stock' => 25,
                'min_stock_alert' => 5,
                'unit' => 'pack',
            ],
            [
                'category_id' => $catKentang?->id,
                'name' => 'Mixed Vegetables 500g Golden Farm',
                'barcode' => '8991002341026',
                'cost_price' => 18000,
                'selling_price' => 23000,
                'loyalty_price' => 21000,
                'stock' => 15,
                'min_stock_alert' => 4,
                'unit' => 'pack',
            ],

            // Daging Sapi & Unggas
            [
                'category_id' => $catDaging?->id,
                'name' => 'US Shortplate Beef Slice 500g',
                'barcode' => '8999901122017',
                'cost_price' => 62000,
                'selling_price' => 74000,
                'loyalty_price' => 69000,
                'stock' => 14,
                'min_stock_alert' => 3,
                'unit' => 'pack',
            ],
            [
                'category_id' => $catDaging?->id,
                'name' => 'Chicken Patty Burger Isi 10',
                'barcode' => '8999901122024',
                'cost_price' => 24000,
                'selling_price' => 30000,
                'loyalty_price' => 27500,
                'stock' => 16,
                'min_stock_alert' => 4,
                'unit' => 'pack',
            ],

            // Camilan & Roti Beku
            [
                'category_id' => $catCamilan?->id,
                'name' => 'Cireng Salju Brecxelle Original',
                'barcode' => '8996541230018',
                'cost_price' => 12000,
                'selling_price' => 16000,
                'loyalty_price' => 14500,
                'stock' => 35,
                'min_stock_alert' => 8,
                'unit' => 'pack',
            ],
            [
                'category_id' => $catCamilan?->id,
                'name' => 'Donat Kentang Beku Isi 10 + Gula Salju',
                'barcode' => '8996541230025',
                'cost_price' => 15000,
                'selling_price' => 20000,
                'loyalty_price' => 18000,
                'stock' => 20,
                'min_stock_alert' => 5,
                'unit' => 'pack',
            ],
            [
                'category_id' => $catCamilan?->id,
                'name' => 'Roti Maryam Original Isi 5',
                'barcode' => '8996541230032',
                'cost_price' => 14000,
                'selling_price' => 18500,
                'loyalty_price' => 16500,
                'stock' => 18,
                'min_stock_alert' => 5,
                'unit' => 'pack',
            ],
        ];

        foreach ($products as $item) {
            Product::updateOrCreate(
                ['barcode' => $item['barcode']],
                [
                    'category_id' => $item['category_id'],
                    'name' => $item['name'],
                    'slug' => Str::slug($item['name']),
                    'cost_price' => $item['cost_price'],
                    'selling_price' => $item['selling_price'],
                    'loyalty_price' => $item['loyalty_price'],
                    'stock' => $item['stock'],
                    'min_stock_alert' => $item['min_stock_alert'],
                    'unit' => $item['unit'],
                    'is_active' => true,
                ]
            );
        }
    }
}
