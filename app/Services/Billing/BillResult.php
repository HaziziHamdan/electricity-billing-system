<?php

namespace App\Services\Billing;

final readonly class BillResult
{
    /**
     * @param  string  $amount  Total in RM, 2 decimal places, e.g. "189.30"
     * @param  list<array{up_to_kwh: int|null, rate: string, units: int, amount: string}>  $breakdown
     */
    public function __construct(
        public string $amount,
        public array $breakdown,
    ) {}
}
