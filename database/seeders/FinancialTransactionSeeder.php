<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FinancialTransaction;
use App\Models\CostCenter;
use App\Models\PaymentCard;
use App\Models\User;
use Faker\Factory as Faker;
use Carbon\Carbon;

class FinancialTransactionSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        $costCenters = CostCenter::all();
        $cards = PaymentCard::all();

        // Para cada empresa e subcentro, criar transações consistentes
        foreach ($costCenters as $cc) {
            $numTrans = rand(10, 20);

            for ($i = 0; $i < $numTrans; $i++) {
                $card = $cards->random();
                $user = $card->user;

                // Gerar datas nos últimos 6 meses
                $date = Carbon::now()->subMonths(rand(0, 5))->addDays(rand(0, 27));

                // Se for empresa, mais receitas que despesas, se subcentro equilibrado
                if ($cc->type === 'empresa') {
                    // 2/3 de chance de ser entrada, 1/3 saída
                    $type = (rand(1, 3) <= 2) ? 'entrada' : 'saida';
                } else {
                    $type = $faker->randomElement(['entrada', 'saida']);
                }

                $amount = $type === 'entrada'
                    ? $faker->numberBetween(5000, 50000)
                    : $faker->numberBetween(1000, 30000);

                FinancialTransaction::create([
                    'cost_center_id' => $cc->id,
                    'payment_card_id' => $card->id,
                    'user_id' => $user->id,
                    'type' => $type,
                    'amount' => $amount,
                    'transaction_date' => $date
                ]);
            }
        }

        $this->command->info('✅ FinancialTransactions seeded with consistent values per company/subcentro.');
    }
}
