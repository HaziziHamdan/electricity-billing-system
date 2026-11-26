<?php

namespace Database\Seeders;

use App\Models\ElectricityUsage;
use Illuminate\Database\Seeder;

class DemoUsageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ElectricityUsage::factory()->count(30)->create();
    }
}
