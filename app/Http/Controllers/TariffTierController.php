<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateTariffTiersRequest;
use App\Models\BuildingType;
use App\Services\TariffTierService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TariffTierController extends Controller
{
    public function __construct(
        private readonly TariffTierService $service,
    ) {}

    public function index(): View
    {
        return view('tariffs.index', [
            'buildingTypes' => BuildingType::query()->with('tariffTiers')->orderBy('name')->get(),
        ]);
    }

    public function edit(BuildingType $buildingType): View
    {
        $buildingType->load('tariffTiers');

        return view('tariffs.edit', ['buildingType' => $buildingType]);

    }

    public function update(UpdateTariffTiersRequest $request, BuildingType $buildingType): RedirectResponse
    {
        $this->service->replace($buildingType, $request->tiers());

        return to_route('tariffs.index')
            ->with('status', "{$buildingType->name} rates updated. Existing bills keep their original amounts.");
    }
}
