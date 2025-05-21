<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        for ($i = 1; $i <= 10; $i++) {
            Product::create([
                'name' => "Producto $i",
                'price' => rand(100, 1000), // valores positivos entre 100 y 1000
            ]);
        }
    }
}
