<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ElectricityUsageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            SampleUsageSeeder::class,
            DemoUsageSeeder::class,
        ]);
    }
}
