<?php

namespace App\Services;

use App\Models\BuildingType;
use App\Models\TariffTier;
use Illuminate\Support\Facades\DB;

final class TariffTierService
{
    /**
     * Replace all tiers of a building type atomically.
     * Existing bills are unaffected: their amounts and tariff snapshots are frozen.
     *
     * @param  list<array{up_to_kwh: int|null, rate: string}>  $tiers
     */
    public function replace(BuildingType $buildingType, array $tiers): void
    {
        DB::transaction(function () use ($buildingType, $tiers) {
            // Serialise concurrent edits of the same building type's rates.
            BuildingType::query()->whereKey($buildingType->id)->lockForUpdate()->first();

            TariffTier::query()->where('building_type_id', $buildingType->id)->delete();

            $buildingType->tariffTiers()->createMany($tiers);
        });
    }
}
