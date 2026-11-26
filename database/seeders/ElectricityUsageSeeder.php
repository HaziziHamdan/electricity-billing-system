<?php

namespace Database\Seeders;

use App\Models\BuildingType;
use App\Models\ElectricityUsage;
use App\Models\State;
use Illuminate\Database\Seeder;

class ElectricityUsageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $buildingTypeIds = BuildingType::pluck('id', 'code');
        $selangorId = State::where('code', 'SGR')->value('id');

        // The three sample rows from Report 1 in the brief, so reviewers can compare.
        $samples = [
            ['JOHN', 6, 'COMMERCIAL', 210],
            ['AHMAD', 10, 'COMMERCIAL', 100],
            ['MARY', 1, 'RESIDENTIAL', 150],
        ];

        foreach ($samples as [$name, $month, $typeCode, $kwh]) {
            ElectricityUsage::create([
                'customer_name' => $name,
                'building_type_id' => $buildingTypeIds[$typeCode],
                'month' => $month,
                'usage_kwh' => $kwh,
                'state_id' => $selangorId,
            ]);
        }

        ElectricityUsage::factory()->count(30)->create();
    }
}
