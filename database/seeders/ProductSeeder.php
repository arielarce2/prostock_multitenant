<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear productos de prueba
        Product::create([
            'name' => 'Producto 1',
            'description' => 'Descripción del producto 1',
            'price' => 10,
        ]);

    }
}
