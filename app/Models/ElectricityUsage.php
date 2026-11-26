<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ElectricityUsage extends Model
{
    /** @use HasFactory<ElectricityUsageFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_name',
        'building_type_id',
        'month',
        'usage_kwh',
        'state_id',
        'bill_amount',
        'tariff_snapshot',
    ];

    protected function casts(): array
    {
        return [
            'month' => 'integer',
            'usage_kwh' => 'integer',
            'bill_amount' => 'decimal:2',
            'tariff_snapshot' => 'array',
        ];
    }

    /** @return BelongsTo<BuildingType, $this> */
    public function buildingType(): BelongsTo
    {
        return $this->belongsTo(BuildingType::class);
    }

    /** @return BelongsTo<State, $this> */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }
}
