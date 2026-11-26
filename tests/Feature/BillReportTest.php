<?php

use App\Models\ElectricityUsage;

use function Pest\Laravel\get;

beforeEach(function () {
    seedReferenceData();

    makeUsage(['customer_name' => 'JOHN', 'month' => 6, 'building_type_id' => buildingTypeId('COMMERCIAL'), 'usage_kwh' => 210]);   // 189.30
    makeUsage(['customer_name' => 'AHMAD', 'month' => 6, 'building_type_id' => buildingTypeId('COMMERCIAL'), 'usage_kwh' => 100]);  // 89.00
    makeUsage(['customer_name' => 'MARY', 'month' => 6, 'building_type_id' => buildingTypeId('RESIDENTIAL'), 'usage_kwh' => 150]);  // 67.50
    makeUsage(['customer_name' => 'OCTOBER GUY', 'month' => 10, 'building_type_id' => buildingTypeId('COMMERCIAL'), 'usage_kwh' => 50]);
});

it('filters by month and building type and totals the matching records', function () {
    get(route('reports.bills', ['month' => 6, 'building_type_id' => buildingTypeId('COMMERCIAL')]))
        ->assertOk()
        ->assertSee(['JOHN', 'AHMAD', '189.30', '89.00'])
        ->assertDontSee(['MARY', 'OCTOBER GUY'])
        ->assertSee('Total (2 records)')
        ->assertSee('278.30');           // 189.30 + 89.00
});

it('shows all records when no filter is applied', function () {
    get(route('reports.bills'))
        ->assertSee(['JOHN', 'AHMAD', 'MARY', 'OCTOBER GUY'])
        ->assertSee('Total (4 records)');
});

it('reports the stored bill, not a recalculation at current rates', function () {
    setTierRate('COMMERCIAL', 200, '5.0000');

    get(route('reports.bills', ['month' => 6, 'building_type_id' => buildingTypeId('COMMERCIAL')]))
        ->assertSee('189.30');
});

it('excludes soft-deleted records from rows and totals', function () {
    ElectricityUsage::where('customer_name', 'AHMAD')->first()->delete();

    get(route('reports.bills', ['month' => 6, 'building_type_id' => buildingTypeId('COMMERCIAL')]))
        ->assertDontSee('AHMAD')
        ->assertSee('Total (1 record)')
        ->assertSee('189.30');
});

it('ignores an out-of-range month', function () {
    get(route('reports.bills', ['month' => 13]))
        ->assertOk()
        ->assertSee('Total (4 records)');
});
