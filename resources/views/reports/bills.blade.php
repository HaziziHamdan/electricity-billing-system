@php use App\Enums\Month; @endphp

<x-layouts.app>
    <x-page-header title="Bill report"></x-page-header>
    {{-- Current tariff rates, for reference --}}
    <x-card class="mb-6">
        <h2 class="border-b border-gray-400 px-4 py-3 text-sm font-semibold">Current tariff rates</h2>
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-600">
                <tr>
                    <th class="px-4 py-2">Building type</th>
                    <th class="px-4 py-2">Tariff category</th>
                    <th class="px-4 py-2 text-right">Rate (RM/kWh)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($buildingTypes as $type)
                    @php $from = 1; @endphp
                    @foreach ($type->tariffTiers as $tier)
                        <tr>
                            <td class="px-4 py-2">{{ $loop->first ? $type->name : '' }}</td>
                            <td class="px-4 py-2">
                                {{ $tier->up_to_kwh === null ? $from.' kWh onwards' : $from.' – '.$tier->up_to_kwh.' kWh' }}
                            </td>
                            <td class="px-4 py-2 text-right">{{ $tier->rate }}</td>
                        </tr>
                        @php $from = ($tier->up_to_kwh ?? 0) + 1; @endphp
                    @endforeach
                @endforeach
            </tbody>
        </table>
        <p class="border-t px-4 py-2 text-xs text-gray-500">
            Bills below use the rates in effect when each record was calculated.
        </p>
    </x-card>

    {{-- Filters --}}
    <x-card class="mb-6">
        <form method="GET" action="{{ route('reports.bills') }} "class=" flex flex-wrap items-end gap-4 p-4">
            <div>
                <label for="month" class="block text-xs font-medium text-gray-600">Month</label>
                <select id="month" name="month" class="block w-full min-w-44 rounded-md border-gray-300 shadow-sm focus:border-brand-600 focus:ring-brand-600 sm:text-sm">
                    <option value="">All</option>
                    @foreach ($months as $month)
                        <option value="{{ $month->value }}" @selected($filters->month === $month->value)>
                            {{ $month->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="building_type_id" class="block text-xs font-medium text-gray-600">Building type</label>
                <select id="building_type_id" name="building_type_id" class="block w-full min-w-44 rounded-md border-gray-300 shadow-sm focus:border-brand-600 focus:ring-brand-600 sm:text-sm">
                    <option value="">All</option>
                    @foreach ($buildingTypes as $type)
                        <option value="{{ $type->id }}" @selected($filters->buildingTypeId === $type->id)>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <x-button type="submit" variant="secondary">Search</x-button>

            @if ($filters->isFiltered())
                <x-button variant="dark" :href="route('reports.bills')">Reset</x-button>
            @endif
        </form>
    </x-card>

    {{-- Report table --}}
    <x-card class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-400 text-sm">
            <thead class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-600">
                <tr>
                    <th class="px-4 py-3">No</th>
                    <th class="px-4 py-3">Customer name</th>
                    <th class="px-4 py-3">Month</th>
                    <th class="px-4 py-3">Building type</th>
                    <th class="px-4 py-3 text-right">Usability (kWh)</th>
                    <th class="px-4 py-3 text-right">Bill (RM)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($rows as $row)
                    <tr>
                        <td class="px-4 py-3">{{ $rows->firstItem() + $loop->index }}</td>
                        <td class="px-4 py-3">
                            <x-button variant="link" :href="route('usages.show', $row)" class="ml-2 !text-gray-600">
                                {{ $row->customer_name }}
                            </x-button>
                        </td>
                        <td class="px-4 py-3">{{ Month::from($row->month)->label() }}</td>
                        <td class="px-4 py-3">
                            <x-badge :color="$row->buildingType->code->value === 'COMMERCIAL' ? 'blue' : 'amber'">
                                {{ $row->buildingType->name }}
                            </x-badge>
                        </td>
                        <td class="px-4 py-3 text-right">{{ number_format($row->usage_kwh) }}</td>
                        <td class="px-4 py-3 text-right tabular-nums">{{ $row->bill_amount }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">No records match these filters.</td>
                    </tr>
                @endforelse
            </tbody>
            @if ($totals->records > 0)
                <tfoot class="border-t bg-gray-50 font-semibold">
                    <tr>
                        <td class="px-4 py-3" colspan="4">
                            Total ({{ $totals->records }} {{ Str::plural('record', $totals->records) }})
                        </td>
                        <td class="px-4 py-3 text-right">{{ number_format((int) $totals->total_kwh) }}</td>
                        <td class="px-4 py-3 text-right tabular-nums">{{ number_format((float) $totals->total_bill, 2) }}</td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </x-card>

    <div class="mt-4">
        {{ $rows->links() }}
    </div>
</x-layouts.app>