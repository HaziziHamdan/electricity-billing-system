<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Reference data: required in every environment.
        $this->call([
            BuildingTypeSeeder::class,
            StateSeeder::class,
            TariffTierSeeder::class,
        ]);

        // Demo data: never in production.
        if (! app()->isProduction()) {
            $this->call(ElectricityUsageSeeder::class);
        }
    }
}
