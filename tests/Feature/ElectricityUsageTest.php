<?php

use App\Models\ElectricityUsage;

use function Pest\Laravel\assertSoftDeleted;
use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\put;

beforeEach(fn () => seedReferenceData());

// ── a) Create ────────────────────────────────────────────────────────────────

it('creates a record with a calculated bill and tariff snapshot', function () {
    post(route('usages.store'), usagePayload())
        ->assertRedirect(route('usages.index'))
        ->assertSessionHas('status', 'Record saved.');

    $usage = ElectricityUsage::sole();

    expect($usage->customer_name)->toBe('JOHN')
        ->and($usage->bill_amount)->toBe('189.30')
        ->and($usage->tariff_snapshot)->toHaveCount(2)
        ->and($usage->tariff_snapshot[1]['amount'])->toBe('11.30');
});

it('rejects an empty form with an error for every field', function () {
    post(route('usages.store'), [])
        ->assertSessionHasErrors(['customer_name', 'building_type_id', 'month', 'usage_kwh', 'state_id']);

    expect(ElectricityUsage::count())->toBe(0);
});

it('rejects invalid values', function (array $overrides, string $field) {
    post(route('usages.store'), usagePayload($overrides))
        ->assertSessionHasErrors($field);
})->with([
    'month 0' => [['month' => 0], 'month'],
    'month 13' => [['month' => 13], 'month'],
    'negative usage' => [['usage_kwh' => -1], 'usage_kwh'],
    'decimal usage' => [['usage_kwh' => 10.5], 'usage_kwh'],
    'unknown building type' => [['building_type_id' => 999999], 'building_type_id'],
    'unknown state' => [['state_id' => 999999], 'state_id'],
    'name too long' => [['customer_name' => str_repeat('A', 151)], 'customer_name'],
]);

// ── c) Update ────────────────────────────────────────────────────────────────

it('updates all fields of a record', function () {
    $usage = makeUsage();

    put(route('usages.update', $usage), usagePayload([
        'customer_name' => 'JOHN DOE',
        'month' => 7,
        'state_id' => stateId('JHR'),
    ]))->assertRedirect(route('usages.index'));

    $usage->refresh();

    expect($usage->customer_name)->toBe('JOHN DOE')
        ->and($usage->month)->toBe(7)
        ->and($usage->state_id)->toBe(stateId('JHR'));
});

it('keeps the original bill when only non-billing fields change, even after a rate change', function () {
    $usage = makeUsage(['usage_kwh' => 210]);            // 189.30 at original rates
    setTierRate('COMMERCIAL', 200, '1.0000');            // rates go up afterwards

    put(route('usages.update', $usage), usagePayload(['customer_name' => 'RENAMED']));

    expect($usage->refresh()->bill_amount)->toBe('189.30');
});

it('recalculates the bill at current rates when usage changes', function () {
    $usage = makeUsage(['usage_kwh' => 210]);
    setTierRate('COMMERCIAL', 200, '1.0000');

    put(route('usages.update', $usage), usagePayload(['usage_kwh' => 100]));

    expect($usage->refresh()->bill_amount)->toBe('100.00');   // 100 × 1.00
});

it('recalculates the bill when building type changes', function () {
    $usage = makeUsage(['usage_kwh' => 150]);            // commercial: 133.50

    put(route('usages.update', $usage), usagePayload([
        'usage_kwh' => 150,
        'building_type_id' => buildingTypeId('RESIDENTIAL'),
    ]));

    expect($usage->refresh()->bill_amount)->toBe('67.50');    // residential: 150 × 0.45
});

// ── b) Delete ────────────────────────────────────────────────────────────────

it('soft deletes a record and hides it from the listing and detail page', function () {
    $usage = makeUsage(['customer_name' => 'TO BE DELETED']);

    delete(route('usages.destroy', $usage))
        ->assertRedirect(route('usages.index'))
        ->assertSessionHas('status', 'Record deleted.');

    assertSoftDeleted($usage);
    get(route('usages.index'))->assertDontSee('TO BE DELETED');
    get(route('usages.show', $usage))->assertNotFound();
});

// ── Detail page ──────────────────────────────────────────────────────────────

it('shows the bill breakdown on the detail page', function () {
    $usage = makeUsage(['usage_kwh' => 210]);

    get(route('usages.show', $usage))
        ->assertOk()
        ->assertSeeInOrder(['178.00', '11.30', '189.30']);
});

// ── d) Listing: filters and sorting ──────────────────────────────────────────

it('filters by building type', function () {
    makeUsage(['customer_name' => 'COMM ONE', 'building_type_id' => buildingTypeId('COMMERCIAL')]);
    makeUsage(['customer_name' => 'RESI ONE', 'building_type_id' => buildingTypeId('RESIDENTIAL')]);

    get(route('usages.index', ['building_type_id' => buildingTypeId('RESIDENTIAL')]))
        ->assertOk()
        ->assertSee('RESI ONE')
        ->assertDontSee('COMM ONE');
});

it('filters by state', function () {
    makeUsage(['customer_name' => 'IN SELANGOR', 'state_id' => stateId('SGR')]);
    makeUsage(['customer_name' => 'IN JOHOR', 'state_id' => stateId('JHR')]);

    get(route('usages.index', ['state_id' => stateId('JHR')]))
        ->assertSee('IN JOHOR')
        ->assertDontSee('IN SELANGOR');
});

it('sorts by usage ascending and descending', function () {
    makeUsage(['customer_name' => 'MEDIUM', 'usage_kwh' => 200]);
    makeUsage(['customer_name' => 'SMALL', 'usage_kwh' => 10]);
    makeUsage(['customer_name' => 'LARGE', 'usage_kwh' => 900]);

    get(route('usages.index', ['sort' => 'usage', 'direction' => 'asc']))
        ->assertSeeInOrder(['SMALL', 'MEDIUM', 'LARGE']);

    get(route('usages.index', ['sort' => 'usage', 'direction' => 'desc']))
        ->assertSeeInOrder(['LARGE', 'MEDIUM', 'SMALL']);
});

it('sorts by building type ascending and descending', function () {
    makeUsage(['customer_name' => 'RESI', 'building_type_id' => buildingTypeId('RESIDENTIAL')]);
    makeUsage(['customer_name' => 'COMM', 'building_type_id' => buildingTypeId('COMMERCIAL')]);

    get(route('usages.index', ['sort' => 'building_type', 'direction' => 'asc']))
        ->assertSeeInOrder(['COMM', 'RESI']);

    get(route('usages.index', ['sort' => 'building_type', 'direction' => 'desc']))
        ->assertSeeInOrder(['RESI', 'COMM']);
});

it('ignores invalid filter and sort parameters instead of failing', function (array $query) {
    makeUsage(['customer_name' => 'STILL LISTED']);

    get(route('usages.index', $query))
        ->assertOk()
        ->assertSee('STILL LISTED');
})->with([
    'unknown sort field' => [['sort' => 'DROP TABLE', 'direction' => 'sideways']],
    'array instead of string' => [['sort' => ['x']]],
    'non-numeric id' => [['state_id' => 'abc']],
]);

it('redirects the home page to the records listing', function () {
    get('/')->assertRedirect(route('usages.index'));
});
