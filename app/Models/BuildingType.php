<?php

namespace App\Models;

use App\Enums\BuildingTypeCode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BuildingType extends Model
{
    protected $fillable = ['code', 'name'];

    protected function casts(): array
    {
        return ['code' => BuildingTypeCode::class];
    }

    public function tariffTiers(): HasMany
    {
        return $this->hasMany(TariffTier::class)->orderByRaw('up_to_kwh ASC NULLS LAST');
    }
}
