<?php

namespace App\Services\Billing;

use App\Models\BuildingType;
use App\Models\TariffTier;
use InvalidArgumentException;
use RuntimeException;

final class BillCalculator
{
    public function calculate(BuildingType $buildingType, int $usageKwh): BillResult
    {
        // Relationship is ordered by up_to_kwh ASC NULLS LAST.
        return $this->calculateFromTiers($buildingType->tariffTiers()->get(), $usageKwh);
    }

    /**
     * Pure calculation: no database access, so it is easy to unit test.
     *
     * @param  iterable<TariffTier>  $tiers  Ordered by up_to_kwh ascending, open-ended tier last.
     */
    public function calculateFromTiers(iterable $tiers, int $usageKwh): BillResult
    {
        if ($usageKwh < 0) {
            throw new InvalidArgumentException('Usage cannot be negative.');
        }

        $previousCap = 0;
        $total = '0.00';
        $breakdown = [];

        foreach ($tiers as $tier) {
            if ($usageKwh <= $previousCap) {
                break;
            }

            $cap = $tier->up_to_kwh;                              // null = no upper limit
            $tierEnd = $cap === null ? $usageKwh : min($usageKwh, $cap);
            $units = $tierEnd - $previousCap;
            $rate = (string) $tier->rate;
            $amount = $this->roundHalfUp(bcmul((string) $units, $rate, 4));

            $breakdown[] = [
                'up_to_kwh' => $cap,
                'rate' => $rate,
                'units' => $units,
                'amount' => $amount,
            ];

            // Total is the sum of rounded lines, so the breakdown always adds up exactly.
            $total = bcadd($total, $amount, 2);

            if ($cap === null) {
                $previousCap = $usageKwh;
                break;
            }

            $previousCap = $cap;
        }

        if ($usageKwh > $previousCap) {
            throw new RuntimeException('Tariff tiers do not cover the full usage. Configure an open-ended tier.');
        }

        return new BillResult($total, $breakdown);
    }

    /** Round a non-negative bcmath number to 2 decimal places, half up. */
    private function roundHalfUp(string $value): string
    {
        return bcadd($value, '0.005', 2);   // bcmath truncates, so add half a sen first
    }
}
