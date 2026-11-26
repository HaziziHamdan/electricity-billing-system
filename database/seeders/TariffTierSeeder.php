<?php

namespace Database\Seeders;

use App\Enums\BuildingTypeCode;
use App\Models\BuildingType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TariffTierSeeder extends Seeder
{
    /**
     * Default rates from the brief. Each tier is [up_to_kwh, rate];
     * null up_to_kwh = open-ended final tier.
     *
     * @return array<string, list<array{0: int|null, 1: string}>>
     */
    /**
     * Run the database seeds.
     */
    private function defaults(): array
    {
        return [
            BuildingTypeCode::Residential->value => [
                [200, '0.4500'],
                [null, '0.9700'],
            ],
            BuildingTypeCode::Commercial->value => [
                [200, '0.8900'],
                [null, '1.1300'],   // brief says "300 kWh onwards"; treated as 201+ (see README)
            ],
        ];
    }

    public function run(): void
    {
        DB::transaction(function () {
            foreach ($this->defaults() as $code => $tiers) {
                $buildingType = BuildingType::where('code', $code)->firstOrFail();

                // Rates are configurable: never overwrite tiers that already exist.
                if ($buildingType->tariffTiers()->exists()) {
                    continue;
                }

                $buildingType->tariffTiers()->createMany(
                    array_map(
                        fn (array $tier) => ['up_to_kwh' => $tier[0], 'rate' => $tier[1]],
                        $tiers,
                    ),
                );
            }
        });
    }
}
