<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Material de Escritório', 'description' => 'Papelaria, canetas, clips, etc.'],
            ['name' => 'Equipamentos Informáticos', 'description' => 'Computadores, impressoras, cabos, etc.'],
            ['name' => 'Limpeza', 'description' => 'Detergentes, panos, desinfetantes.'],
            ['name' => 'Ferramentas', 'description' => 'Martelos, chaves, alicates.'],
        ];

        foreach ($categories as $c) {
            Category::create($c);
        }
    }
}
