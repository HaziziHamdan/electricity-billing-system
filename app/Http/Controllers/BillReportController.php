<?php

namespace App\Http\Controllers;

use App\Data\UsageFilters;
use App\Enums\Month;
use App\Models\BuildingType;
use App\Models\ElectricityUsage;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BillReportController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): View
    {
        $filters = UsageFilters::fromRequest($request);

        $query = ElectricityUsage::query()->filter($filters);

        $rows = (clone $query)
            ->with('buildingType')
            ->orderBy('month')
            ->orderBy('customer_name')
            ->orderBy('id')
            ->paginate(20)
            ->withQueryString();

        // Totals cover ALL matching records, not just the current page.
        // toBase() still applies the soft-delete scope.
        $totals = (clone $query)
            ->toBase()
            ->selectRaw('COUNT(*) AS records, COALESCE(SUM(usage_kwh), 0) AS total_kwh, COALESCE(SUM(bill_amount), 0) AS total_bill')
            ->first();

        return view('reports.bills', [
            'rows' => $rows,
            'totals' => $totals,
            'filters' => $filters,
            'months' => Month::cases(),
            'buildingTypes' => BuildingType::query()->with('tariffTiers')->orderBy('name')->get(),
        ]);
    }
}
