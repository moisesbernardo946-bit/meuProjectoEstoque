<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CostCenter;
use Faker\Factory as Faker;

class CostCenterSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // Criar 3 empresas
        for ($i = 1; $i <= 3; $i++) {
            $empresa = CostCenter::create([
                'code' => sprintf('%04d', $i),
                'name' => "Empresa {$faker->company}",
                'type' => 'empresa'
            ]);

            // Cada empresa terá 2 a 4 subcentros
            $numSub = rand(2, 4);
            for ($j = 1; $j <= $numSub; $j++) {
                CostCenter::create([
                    'code' => $empresa->code . '.' . sprintf('%02d', $j),
                    'name' => "Subcentro {$j} - {$empresa->name}",
                    'type' => 'subcentro',
                    'parent_id' => $empresa->id
                ]);
            }
        }

        $this->command->info('✅ CostCenters seeded with realistic hierarchy.');
    }
}
