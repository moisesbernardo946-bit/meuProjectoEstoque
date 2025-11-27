<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CompanyGroup;

class CompanyGroupSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            [
                'name' => 'TERRA INTERIOR',
                'nif'  => '5417530573',
            ],
            [
                'name' => 'TERRA FLORESTAL',
                'nif'  => '5417530557',
            ],
            [
                'name' => 'MADETINTAS',
                'nif'  => '5417530549',
            ],
        ];

        foreach ($groups as $group) {
            CompanyGroup::firstOrCreate(
                ['name' => $group['name']],
                ['nif' => $group['nif']]
            );
        }
    }
}
