<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentCard;
use App\Models\User;
use Faker\Factory as Faker;

class PaymentCardSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // Criar 5 usuários e cartões
        for ($i = 1; $i <= 5; $i++) {
            $user = User::factory()->create(['name' => $faker->name]);

            PaymentCard::create([
                'name' => "Cartão {$i} - {$user->name}",
                'type' => $faker->randomElement(['crédito', 'débito']),
                'user_id' => $user->id
            ]);
        }

        $this->command->info('✅ PaymentCards seeded with users.');
    }
}
