<?php

use App\Models\BuildingType;
use App\Models\TariffTier;

use function Pest\Laravel\get;
use function Pest\Laravel\put;

beforeEach(fn () => seedReferenceData());

function commercialType(): BuildingType
{
    return BuildingType::where('code', 'COMMERCIAL')->firstOrFail();
}

it('lists the current tiers per building type', function () {
    get(route('tariffs.index'))
        ->assertOk()
        ->assertSee(['Commercial', 'Residential', '0.8900', '1.1300', '201 kWh onwards']);
});

it('replaces a building type\'s tiers', function () {
    put(route('tariffs.update', commercialType()), ['tiers' => [
        ['up_to_kwh' => 100, 'rate' => '0.5'],
        ['up_to_kwh' => 300, 'rate' => '0.75'],
        ['up_to_kwh' => null, 'rate' => '1.2'],
    ]])->assertRedirect(route('tariffs.index'));

    expect(commercialType()->tariffTiers->map->only(['up_to_kwh', 'rate'])->all())->toBe([
        ['up_to_kwh' => 100, 'rate' => '0.5000'],
        ['up_to_kwh' => 300, 'rate' => '0.7500'],
        ['up_to_kwh' => null, 'rate' => '1.2000'],
    ]);
});

it('does not change existing bills when rates change', function () {
    $usage = makeUsage(['usage_kwh' => 210]);   // 189.30

    put(route('tariffs.update', commercialType()), ['tiers' => [
        ['up_to_kwh' => null, 'rate' => '5.0'],
    ]]);

    expect($usage->refresh()->bill_amount)->toBe('189.30');
});

it('uses the new rates for bills created afterwards', function () {
    put(route('tariffs.update', commercialType()), ['tiers' => [
        ['up_to_kwh' => null, 'rate' => '1.0'],
    ]]);

    expect(makeUsage(['usage_kwh' => 210])->bill_amount)->toBe('210.00');
});

it('rejects invalid tier sets', function (array $tiers) {
    $before = TariffTier::count();

    put(route('tariffs.update', commercialType()), ['tiers' => $tiers])
        ->assertSessionHasErrors();

    expect(TariffTier::count())->toBe($before);   // nothing changed
})->with([
    'no tiers' => [[]],
    'last tier has a limit' => [[['up_to_kwh' => 200, 'rate' => '0.89']]],
    'open tier not last' => [[['up_to_kwh' => null, 'rate' => '0.89'], ['up_to_kwh' => 200, 'rate' => '1.13']]],
    'limits not increasing' => [[['up_to_kwh' => 300, 'rate' => '0.5'], ['up_to_kwh' => 200, 'rate' => '0.6'], ['up_to_kwh' => null, 'rate' => '1']]],
    'duplicate limits' => [[['up_to_kwh' => 200, 'rate' => '0.5'], ['up_to_kwh' => 200, 'rate' => '0.6'], ['up_to_kwh' => null, 'rate' => '1']]],
    'zero rate' => [[['up_to_kwh' => null, 'rate' => '0']]],
    'too many decimals' => [[['up_to_kwh' => null, 'rate' => '0.12345']]],
]);

it('returns 404 for an unknown building type code', function () {
    get('/tariffs/INDUSTRIAL/edit')->assertNotFound();
});
