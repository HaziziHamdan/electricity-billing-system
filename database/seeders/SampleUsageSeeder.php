<?php

namespace Database\Seeders;

use App\Models\BuildingType;
use App\Models\State;
use App\Services\ElectricityUsageService;
use Illuminate\Database\Seeder;

class SampleUsageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(ElectricityUsageService $service): void
    {
        $buildingTypeIds = BuildingType::pluck('id', 'code');
        $selangorId = State::where('code', 'SGR')->value('id');

        $samples = [
            ['JOHN', 6, 'COMMERCIAL', 210],
            ['AHMAD', 10, 'COMMERCIAL', 100],
            ['MARY', 1, 'RESIDENTIAL', 150],
        ];

        foreach ($samples as [$name, $month, $typeCode, $kwh]) {
            $service->create([
                'customer_name' => $name,
                'building_type_id' => $buildingTypeIds[$typeCode],
                'month' => $month,
                'usage_kwh' => $kwh,
                'state_id' => $selangorId,
            ]);
        }
    }
}
