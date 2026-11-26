<?php

namespace Database\Factories;

use App\Models\BuildingType;
use App\Models\ElectricityUsage;
use App\Models\State;
use App\Services\Billing\BillCalculator;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ElectricityUsage>
 */
class ElectricityUsageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'customer_name' => fake()->name(),
            'building_type_id' => fn () => BuildingType::query()->inRandomOrder()->value('id'),
            'month' => fake()->numberBetween(1, 12),
            'usage_kwh' => fake()->numberBetween(0, 600),
            'state_id' => fn () => State::query()->inRandomOrder()->value('id'),
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (ElectricityUsage $usage) {
            if ($usage->bill_amount !== null) {
                return;   // caller supplied a bill explicitly
            }

            $bill = app(BillCalculator::class)->calculate(
                BuildingType::findOrFail($usage->building_type_id),
                $usage->usage_kwh,
            );

            $usage->bill_amount = $bill->amount;
            $usage->tariff_snapshot = $bill->breakdown;
        });
    }
}
