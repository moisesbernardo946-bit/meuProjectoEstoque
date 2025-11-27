<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Buscar empresas filhas por código
        $homeDecor    = Company::where('code', '0001')->first();
        $industria    = Company::where('code', '0002')->first();
        $construtora  = Company::where('code', '0003')->first();

        $users = [
            // Diretores
            [
                'name'       => 'Diretor Home Decor',
                'email'      => 'diretor.home@terra.com',
                'phone'      => '943738312',
                'password'   => Hash::make('password'),
                'role'       => 'diretor',
                'company_id' => $homeDecor?->id,
            ],
            [
                'name'       => 'Diretor Indústria',
                'email'      => 'diretor.industria@terra.com',
                'phone'      => '943738312',
                'password'   => Hash::make('password'),
                'role'       => 'diretor',
                'company_id' => $industria?->id,
            ],
            [
                'name'       => 'Diretor Construtora',
                'email'      => 'diretor.construtora@terra.com',
                'phone'      => '943738312',
                'password'   => Hash::make('password'),
                'role'       => 'diretor',
                'company_id' => $construtora?->id,
            ],

            // Financeiro
            [
                'name'       => 'Financeiro Geral',
                'email'      => 'financeiro@terra.com',
                'phone'      => '943738312',
                'password'   => Hash::make('password'),
                'role'       => 'financeiro',
                'company_id' => $homeDecor?->id,
            ],

            // Almoxarife
            [
                'name'       => 'Almoxarife Home Decor',
                'email'      => 'almoxarife.home@terra.com',
                'phone'      => '943738312',
                'password'   => Hash::make('password'),
                'role'       => 'almoxarife',
                'company_id' => $homeDecor?->id,
            ],

            // Comprador
            [
                'name'       => 'Comprador Indústria',
                'email'      => 'comprador.ind@terra.com',
                'phone'      => '943738312',
                'password'   => Hash::make('password'),
                'role'       => 'comprador',
                'company_id' => $industria?->id,
            ],

            // Motorista
            [
                'name'       => 'Motorista Construtora',
                'email'      => 'motorista.const@terra.com',
                'phone'      => '943738312',
                'password'   => Hash::make('password'),
                'role'       => 'motorista',
                'company_id' => $construtora?->id,
            ],
        ];

        foreach ($users as $data) {
            if (!$data['company_id']) {
                continue;
            }

            User::firstOrCreate(
                ['email' => $data['email']],
                $data
            );
        }
    }
}
