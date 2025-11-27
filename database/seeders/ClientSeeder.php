<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Models\Company;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        // Buscar algumas empresas por código
        $homeDecor    = Company::where('code', '0001')->first();
        $industria    = Company::where('code', '0002')->first();
        $construtora  = Company::where('code', '0003')->first();

        $clients = [
            // Clientes da Home Decor
            [
                'company_id' => $homeDecor?->id,
                'name'       => 'Cliente Home A',
                'code'       => '0001.01',
                'email'    => 'cliente.home.a@terra.com',
            ],
            [
                'company_id' => $homeDecor?->id,
                'name'       => 'Cliente Home B',
                'code'       => '0001.02',
                'email'    => 'cliente.home.b@terra.com',
            ],

            // Clientes da Indústria
            [
                'company_id' => $industria?->id,
                'name'       => 'Cliente Indústria A',
                'code'       => '0002.01',
                'email'    => 'cliente.ind.a@terra.com',
            ],
            [
                'company_id' => $industria?->id,
                'name'       => 'Cliente Indústria B',
                'code'       => '0002.02',
                'email'    => 'cliente.ind.b@terra.com',
            ],

            // Clientes da Construtora
            [
                'company_id' => $construtora?->id,
                'name'       => 'Cliente Construtora A',
                'code'       => '0003.01',
                'email'    => 'cliente.const.a@terra.com',
            ],
        ];

        foreach ($clients as $client) {
            if (!$client['company_id']) {
                continue;
            }

            Client::firstOrCreate(
                [
                    'code' => $client['code'],
                ],
                [
                    'company_id' => $client['company_id'],
                    'name'       => $client['name'],
                    'email'    => $client['email'],
                ]
            );
        }
    }
}
