<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CompanyGroupSeeder::class,
            CompanySeeder::class,
            ClientSeeder::class,
            UserSeeder::class,
            CategorySeeder::class,
            UnitSeeder::class,
            ZoneSeeder::class,
            ProductSeeder::class,
        ]);
    }
}
