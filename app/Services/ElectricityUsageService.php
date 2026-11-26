<?php

namespace App\Services;

use App\Models\BuildingType;
use App\Models\ElectricityUsage;
use App\Services\Billing\BillCalculator;
use Illuminate\Support\Facades\DB;

final class ElectricityUsageService
{
    public function __construct(
        private readonly BillCalculator $billCalculator,
    ) {}

    /** @param array{customer_name: string, building_type_id: int, month: int, usage_kwh: int, state_id: int} $data */
    public function create(array $data): ElectricityUsage
    {
        return DB::transaction(function () use ($data) {
            $bill = $this->billCalculator->calculate(
                BuildingType::findOrFail($data['building_type_id']),
                (int) $data['usage_kwh'],
            );

            return ElectricityUsage::create([
                ...$data,
                'bill_amount' => $bill->amount,
                'tariff_snapshot' => $bill->breakdown,
            ]);
        });
    }

    /**
     * Bill is recalculated with CURRENT rates only when billing inputs change.
     * Editing name or state keeps the original bill.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(ElectricityUsage $usage, array $data): ElectricityUsage
    {
        return DB::transaction(function () use ($usage, $data) {
            $usage->fill($data);

            if ($usage->isDirty(['usage_kwh', 'building_type_id'])) {
                $bill = $this->billCalculator->calculate(
                    BuildingType::findOrFail($usage->building_type_id),
                    $usage->usage_kwh,
                );

                $usage->bill_amount = $bill->amount;
                $usage->tariff_snapshot = $bill->breakdown;
            }

            $usage->save();

            return $usage;
        });
    }

    public function delete(ElectricityUsage $usage): void
    {
        $usage->delete();   // soft delete
    }
}
