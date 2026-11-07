<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Kopi Susu Gula Aren',
                'description' => 'Kopi susu lokal dengan gula aren pilihan 250ml',
                'price' => 18000,
                'stock' => 50,
            ],
            [
                'name' => 'Espresso Single',
                'description' => 'Single shot espresso dari biji kopi arabika',
                'price' => 12000,
                'stock' => 30,
            ],
            [
                'name' => 'Croissant Butter',
                'description' => 'Roti croissant renyah dengan butter Prancis',
                'price' => 22000,
                'stock' => 15,
            ],
            [
                'name' => 'Matcha Latte',
                'description' => 'Matcha premium dengan fresh milk',
                'price' => 24000,
                'stock' => 25,
            ],
        ];

        foreach ($products as $item) {
            Product::firstOrCreate(['name' => $item['name']], $item);
        }
    }
}

