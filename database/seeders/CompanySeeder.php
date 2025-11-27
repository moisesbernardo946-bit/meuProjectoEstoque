<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\CompanyGroup;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        // Garantir que os grupos existem
        $terraInterior  = CompanyGroup::where('name', 'TERRA INTERIOR')->first();
        $terraFlorestal = CompanyGroup::where('name', 'TERRA FLORESTAL')->first();
        $madetintas     = CompanyGroup::where('name', 'MADETINTAS')->first();

        $companies = [
            // Grupo: TERRA INTERIOR
            [
                'company_group_id' => $terraInterior?->id,
                'name'             => 'Home Decor',
                'code'             => '0001',
            ],
            [
                'company_group_id' => $terraInterior?->id,
                'name'             => 'Indústria',
                'code'             => '0002',
            ],
            [
                'company_group_id' => $terraInterior?->id,
                'name'             => 'Construtora',
                'code'             => '0003',
            ],

            // Grupo: TERRA FLORESTAL
            [
                'company_group_id' => $terraFlorestal?->id,
                'name'             => 'Máquinas',
                'code'             => '0101',
            ],
            [
                'company_group_id' => $terraFlorestal?->id,
                'name'             => 'Made',
                'code'             => '0102',
            ],
            [
                'company_group_id' => $terraFlorestal?->id,
                'name'             => 'Youd',
                'code'             => '0103',
            ],

            // Grupo: MADETINTAS
            [
                'company_group_id' => $madetintas?->id,
                'name'             => 'Vernis',
                'code'             => '0201',
            ],
            [
                'company_group_id' => $madetintas?->id,
                'name'             => 'Kila',
                'code'             => '0202',
            ],
            [
                'company_group_id' => $madetintas?->id,
                'name'             => 'Poit',
                'code'             => '0203',
            ],
        ];

        foreach ($companies as $company) {
            if (!$company['company_group_id']) {
                continue; // se por algum motivo o grupo não existir
            }

            Company::firstOrCreate(
                [
                    'code' => $company['code'],
                ],
                [
                    'company_group_id' => $company['company_group_id'],
                    'name'             => $company['name'],
                ]
            );
        }
    }
}
