<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TariffTier extends Model
{
    protected $fillable = ['building_type_id', 'up_to_kwh', 'rate'];

    protected function casts(): array
    {
        return ['up_to_kwh' => 'integer', 'rate' => 'decimal:4'];
    }

    /** @return BelongsTo<BuildingType, $this> */
    public function buildingType(): BelongsTo
    {
        return $this->belongsTo(BuildingType::class);
    }
}
