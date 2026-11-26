<?php

use App\Models\BuildingType;
use App\Models\ElectricityUsage;
use App\Models\State;
use App\Models\TariffTier;
use Database\Seeders\BuildingTypeSeeder;
use Database\Seeders\StateSeeder;
use Database\Seeders\TariffTierSeeder;
// use Illuminate\Foundation\Testing\DatabaseTruncation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind different classes or traits.
|
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    // ->use(DatabaseTruncation::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function something()
{
    // ..
}

function seedReferenceData(): void
{
    test()->seed([BuildingTypeSeeder::class, StateSeeder::class, TariffTierSeeder::class]);
}

function buildingTypeId(string $code): int
{
    return (int) BuildingType::where('code', $code)->value('id');
}

function stateId(string $code): int
{
    return (int) State::where('code', $code)->value('id');
}

/** @param array<string, mixed> $attributes */
function makeUsage(array $attributes = []): ElectricityUsage
{
    return ElectricityUsage::factory()->create([
        'customer_name' => 'TEST CUSTOMER',
        'building_type_id' => buildingTypeId('COMMERCIAL'),
        'month' => 6,
        'usage_kwh' => 210,
        'state_id' => stateId('SGR'),
        ...$attributes,
    ]);
}

/**
 * Valid form payload, with optional overrides.
 *
 * @param  array<string, mixed>  $overrides
 * @return array<string, mixed>
 */
function usagePayload(array $overrides = []): array
{
    return [
        'customer_name' => 'JOHN',
        'building_type_id' => buildingTypeId('COMMERCIAL'),
        'month' => 6,
        'usage_kwh' => 210,
        'state_id' => stateId('SGR'),
        ...$overrides,
    ];
}

function setTierRate(string $buildingTypeCode, ?int $upToKwh, string $rate): void
{
    TariffTier::query()
        ->where('building_type_id', buildingTypeId($buildingTypeCode))
        ->when(
            $upToKwh === null,
            fn ($q) => $q->whereNull('up_to_kwh'),
            fn ($q) => $q->where('up_to_kwh', $upToKwh),
        )
        ->update(['rate' => $rate]);
}
