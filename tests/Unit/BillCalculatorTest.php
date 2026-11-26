<?php

use App\Models\TariffTier;
use App\Services\Billing\BillCalculator;

/**
 * Build in-memory tiers (no database).
 *
 * @param  list<array{0: int|null, 1: string}>  $tiers  [up_to_kwh, rate]
 * @return list<TariffTier>
 */
function tiers(array $tiers): array
{
    return array_map(
        fn (array $t) => new TariffTier(['up_to_kwh' => $t[0], 'rate' => $t[1]]),
        $tiers,
    );
}

function residential(): array
{
    return tiers([[200, '0.4500'], [null, '0.9700']]);
}

function commercial(): array
{
    return tiers([[200, '0.8900'], [null, '1.1300']]);
}

function calculator(): BillCalculator
{
    return new BillCalculator;
}

it('calculates residential bills at tier boundaries', function (int $kwh, string $expected) {
    expect(calculator()->calculateFromTiers(residential(), $kwh)->amount)->toBe($expected);
})->with([
    'zero usage' => [0, '0.00'],
    'first kWh' => [1, '0.45'],
    'top of first tier' => [200, '90.00'],
    'first kWh of second tier' => [201, '90.97'],
    'high usage' => [1000, '866.00'],
]);

it('calculates commercial bills at tier boundaries', function (int $kwh, string $expected) {
    expect(calculator()->calculateFromTiers(commercial(), $kwh)->amount)->toBe($expected);
})->with([
    'zero usage' => [0, '0.00'],
    'first kWh' => [1, '0.89'],
    'top of first tier' => [200, '178.00'],
    'first kWh of second tier' => [201, '179.13'],
    'brief sample: JOHN' => [210, '189.30'],
    'brief sample: AHMAD' => [100, '89.00'],
    'high usage' => [1000, '1082.00'],
]);

it('matches the brief sample for MARY', function () {
    expect(calculator()->calculateFromTiers(residential(), 150)->amount)->toBe('67.50');
});

it('returns a breakdown per tier that adds up to the total', function () {
    $bill = calculator()->calculateFromTiers(commercial(), 210);

    expect($bill->breakdown)->toBe([
        ['up_to_kwh' => 200, 'rate' => '0.8900', 'units' => 200, 'amount' => '178.00'],
        ['up_to_kwh' => null, 'rate' => '1.1300', 'units' => 10, 'amount' => '11.30'],
    ]);

    $sum = array_reduce($bill->breakdown, fn (string $carry, array $line) => bcadd($carry, $line['amount'], 2), '0.00');
    expect($sum)->toBe($bill->amount);
});

it('returns an empty breakdown for zero usage', function () {
    expect(calculator()->calculateFromTiers(residential(), 0)->breakdown)->toBe([]);
});

it('only bills the first tier when usage stays within it', function () {
    $bill = calculator()->calculateFromTiers(residential(), 150);

    expect($bill->breakdown)->toHaveCount(1)
        ->and($bill->breakdown[0]['units'])->toBe(150);
});

it('rounds each line half up to the nearest sen', function (string $rate, int $kwh, string $expected) {
    $bill = calculator()->calculateFromTiers(tiers([[null, $rate]]), $kwh);

    expect($bill->amount)->toBe($expected);
})->with([
    'exactly half a sen rounds up' => ['0.4550', 1, '0.46'],
    'just below half rounds down' => ['0.4549', 1, '0.45'],
    'fractional total' => ['0.4567', 3, '1.37'],   // 1.3701
]);

it('supports any number of tiers, not just two', function () {
    // 100 × 0.10 + 200 × 0.20 + 50 × 0.30 = 10 + 40 + 15
    $bill = calculator()->calculateFromTiers(
        tiers([[100, '0.1000'], [300, '0.2000'], [null, '0.3000']]),
        350,
    );

    expect($bill->amount)->toBe('65.00')
        ->and($bill->breakdown)->toHaveCount(3);
});

it('works when usage is within a capped tier even without an open-ended tier', function () {
    expect(calculator()->calculateFromTiers(tiers([[200, '0.8900']]), 150)->amount)->toBe('133.50');
});

it('refuses to undercharge when tiers do not cover the full usage', function () {
    calculator()->calculateFromTiers(tiers([[200, '0.8900']]), 250);
})->throws(RuntimeException::class);

it('rejects negative usage', function () {
    calculator()->calculateFromTiers(residential(), -1);
})->throws(InvalidArgumentException::class);
