<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unit;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['name' => 'Unidade', 'symbol' => 'un'],
            ['name' => 'Litro', 'symbol' => 'L'],
            ['name' => 'Metro', 'symbol' => 'm'],
            ['name' => 'Caixa', 'symbol' => 'cx'],
            ['name' => 'Pacote', 'symbol' => 'pct'],
        ];

        foreach ($units as $u) {
            Unit::create($u);
        }
    }
}
