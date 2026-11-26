<?php

namespace App\Http\Controllers;

use App\Data\UsageFilters;
use App\Enums\Month;
use App\Http\Requests\StoreElectricityUsageRequest;
use App\Http\Requests\UpdateElectricityUsageRequest;
use App\Models\BuildingType;
use App\Models\ElectricityUsage;
use App\Models\State;
use App\Services\ElectricityUsageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ElectricityUsageController extends Controller
{
    public function __construct(
        private readonly ElectricityUsageService $service,
    ) {}

    public function index(Request $request): View
    {
        $filters = UsageFilters::fromRequest($request);

        $usages = ElectricityUsage::query()
            ->with(['buildingType', 'state'])
            ->filter($filters)
            ->sorted($filters->sort, $filters->direction)
            ->paginate(15)
            ->withQueryString();

        return view('usages.index', [
            'usages' => $usages,
            'filters' => $filters,
            ...$this->formOptions(),
        ]);
    }

    public function show(ElectricityUsage $usage): View
    {
        $usage->load(['buildingType', 'state']);

        return view('usages.show', ['usage' => $usage]);
    }

    public function create(): View
    {
        return view('usages.create', $this->formOptions());
    }

    public function store(StoreElectricityUsageRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return to_route('usages.index')->with('status', 'Record saved.');
    }

    public function edit(ElectricityUsage $usage): View
    {
        return view('usages.edit', [
            'usage' => $usage,
            ...$this->formOptions(),
        ]);
    }

    public function update(UpdateElectricityUsageRequest $request, ElectricityUsage $usage): RedirectResponse
    {
        $this->service->update($usage, $request->validated());

        return to_route('usages.index')->with('status', 'Record updated.');
    }

    public function destroy(ElectricityUsage $usage): RedirectResponse
    {
        $this->service->delete($usage);

        return to_route('usages.index')->with('status', 'Record deleted.');
    }

    /**
     * Dropdown and radio options shared by the form and the listing filters.
     *
     * @return array<string, mixed>
     */
    private function formOptions(): array
    {
        return [
            'buildingTypes' => BuildingType::query()->orderBy('name')->get(['id', 'code', 'name']),
            'states' => State::query()->orderBy('name')->get(['id', 'name']),
            'months' => Month::cases(),
        ];
    }
}
