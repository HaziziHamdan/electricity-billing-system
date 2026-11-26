<?php

namespace App\Data;

use App\Enums\SortDirection;
use App\Enums\UsageSortField;
use Illuminate\Http\Request;

final readonly class UsageFilters
{
    public function __construct(
        public ?int $buildingTypeId = null,
        public ?int $stateId = null,
        public ?int $month = null,
        public ?UsageSortField $sort = null,
        public SortDirection $direction = SortDirection::Asc,
    ) {}

    /**
     * Lenient parsing: anything invalid falls back to "no filter" / default order,
     * so a tampered query string never causes an error or a redirect loop.
     */
    public static function fromRequest(Request $request): self
    {
        return new self(
            buildingTypeId: self::positiveInt($request->query('building_type_id')),
            stateId: self::positiveInt($request->query('state_id')),
            month: self::positiveInt($request->query('month'), max: 12),
            sort: UsageSortField::tryFrom(self::stringOrEmpty($request->query('sort'))),
            direction: SortDirection::tryFrom(self::stringOrEmpty($request->query('direction'))) ?? SortDirection::Asc,
        );
    }

    public function isFiltered(): bool
    {
        return $this->buildingTypeId !== null || $this->stateId !== null || $this->month !== null;
    }

    private static function positiveInt(mixed $value, ?int $max = null): ?int
    {
        $options = ['min_range' => 1];

        if ($max !== null) {
            $options['max_range'] = $max;
        }

        $int = filter_var($value, FILTER_VALIDATE_INT, ['options' => $options]);

        return $int === false ? null : $int;
    }

    /** Arrays (e.g. ?sort[]=x) and nulls become '', which tryFrom() rejects safely. */
    private static function stringOrEmpty(mixed $value): string
    {
        return is_string($value) ? $value : '';
    }
}
