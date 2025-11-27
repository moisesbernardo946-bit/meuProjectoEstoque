<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Zone;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all();
        $units = Unit::all();
        $zones = Zone::all();

        $products = [
            ['name' => 'Caneta Azul', 'measure' => 'Pequena'],
            ['name' => 'Papel A4', 'measure' => '210x297mm'],
            ['name' => 'Álcool Gel', 'measure' => '500ml'],
            ['name' => 'Teclado USB', 'measure' => 'Padrão ABNT'],
            ['name' => 'Martelo de Borracha', 'measure' => '500g'],
        ];

        foreach ($products as $p) {
            Product::create([
                'name' => $p['name'],
                'code' => strtoupper(Str::random(8)),
                'description' => fake()->sentence(),
                'category_id' => $categories->random()->id,
                'unit_id' => $units->random()->id,
                'measure' => $p['measure'],
                'zone_id' => $zones->random()->id,
            ]);
        }
    }
}
