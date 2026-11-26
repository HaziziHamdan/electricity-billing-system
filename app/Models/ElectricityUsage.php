<?php

namespace App\Models;

use App\Data\UsageFilters;
use App\Enums\SortDirection;
use App\Enums\UsageSortField;
use Database\Factories\ElectricityUsageFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $customer_name
 * @property int $building_type_id
 * @property int $month
 * @property int $usage_kwh
 * @property int $state_id
 * @property string $bill_amount
 * @property list<array{up_to_kwh: int|null, rate: string, units: int, amount: string}> $tariff_snapshot
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read BuildingType $buildingType
 * @property-read State $state
 */
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

    /**
     * @param  Builder<ElectricityUsage>  $query
     */
    public function scopeFilter(Builder $query, UsageFilters $filters): void
    {
        $query
            ->when($filters->buildingTypeId, fn (Builder $q, int $id) => $q->where('building_type_id', $id))
            ->when($filters->stateId, fn (Builder $q, int $id) => $q->where('state_id', $id))
            ->when($filters->month, fn (Builder $q, int $month) => $q->where('month', $month));
    }

    /**
     * @param  Builder<ElectricityUsage>  $query
     */
    public function scopeSorted(Builder $query, ?UsageSortField $field, SortDirection $direction): void
    {
        match ($field) {
            // Sort by the building type's name via a correlated subquery (no join, so no column clashes).
            UsageSortField::BuildingType => $query->orderBy(
                BuildingType::query()
                    ->select('name')
                    ->whereColumn('building_types.id', 'electricity_usages.building_type_id'),
                $direction->value,
            ),
            UsageSortField::Usage => $query->orderBy('usage_kwh', $direction->value),
            null => null,
        };

        // Tie-breaker: newest first, so pagination is stable when sort values are equal.
        $query->orderByDesc('id');
    }
}
