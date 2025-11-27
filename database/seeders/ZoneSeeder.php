<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Zone;

class ZoneSeeder extends Seeder
{
    public function run(): void
    {
        $zones = [
            ['name' => 'Armazém Central', 'location' => 'estante 4'],
            ['name' => 'Depósito Norte', 'location' => 'corredor 9'],
            ['name' => 'Depósito Sul', 'location' => 'pratileira 1'],
        ];

        foreach ($zones as $z) {
            Zone::create($z);
        }
    }
}
