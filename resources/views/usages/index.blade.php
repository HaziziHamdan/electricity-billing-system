@php use App\Enums\Month; @endphp

<x-layouts.app>
    <x-page-header title="Electricity usage records" description="Search, sort and manage customer usage.">
        <x-slot:actions>
            <x-button :href="route('usages.create')">New record</x-button>
        </x-slot:actions>
    </x-page-header>

    <x-card class="mb-4 p-4">
        <form method="GET" action="{{ route('usages.index') }} "class="mb-4 flex flex-wrap items-end gap-4 bg-white p-4">
            {{-- Keep the current sort when filtering --}}
            @if ($filters->sort)
                <input type="hidden" name="sort" value="{{ $filters->sort->value }}">
                <input type="hidden" name="direction" value="{{ $filters->direction->value }}">
            @endif

            <div>
                <label for="filter_building_type" class="block text-xs font-medium text-gray-600">Building type</label>
                <select id="filter_building_type" name="building_type_id"
                        class="mt-1 rounded border border-gray-300 px-3 py-2 text-sm">
                    <option value="">All</option>
                    @foreach ($buildingTypes as $type)
                        <option value="{{ $type->id }}" @selected($filters->buildingTypeId === $type->id)>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="filter_state" class="block text-xs font-medium text-gray-600">State</label>
                <select id="filter_state" name="state_id" class="mt-1 rounded border border-gray-300 px-3 py-2 text-sm">
                    <option value="">All</option>
                    @foreach ($states as $state)
                        <option value="{{ $state->id }}" @selected($filters->stateId === $state->id)>
                            {{ $state->name }}
                        </option>
                    @endforeach
                </select>
            </div>

           <x-button type="submit" variant="secondary">Search</x-button>

            @if ($filters->isFiltered() || $filters->sort)
                <x-button variant="dark" :href="route('usages.index')">Reset</x-button>
            @endif

            <p class="ml-auto self-center text-sm text-gray-500">
                {{ $usages->total() }} {{ Str::plural('record', $usages->total()) }}
            </p>
        </form>
    </x-card>

    <x-card class="overflow-x-auto bg-white">
        <table class="min-w-full divide-y divide-gray-400 text-sm">
            <thead class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-600">
                <tr>
                    <th class="px-4 py-3">No</th>
                    <th class="px-4 py-3">Customer name</th>
                    <th class="px-4 py-3"
                        @if ($filters->sort?->value === 'building_type') aria-sort="{{ $filters->direction === \App\Enums\SortDirection::Asc ? 'ascending' : 'descending' }}" @endif>
                        <x-sort-link field="building_type" :filters="$filters">Building type</x-sort-link>
                    </th>
                    <th class="px-4 py-3">Month</th>
                    <th class="px-4 py-3 text-right"
                        @if ($filters->sort?->value === 'usage') aria-sort="{{ $filters->direction === \App\Enums\SortDirection::Asc ? 'ascending' : 'descending' }}" @endif>
                        <x-sort-link field="usage" :filters="$filters">Usability (kWh)</x-sort-link>
                    </th>
                    <th class="px-4 py-3">State</th>
                    <th class="px-4 py-3 text-right">Bill (RM)</th>
                    <th class="px-4 py-3"><span class="sr-only">Actions</span></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse ($usages as $usage)
                    <tr>
                        <td class="px-4 py-3">{{ $usages->firstItem() + $loop->index }}</td>
                        <td class="px-4 py-3">
                            <x-button variant="link" :href="route('usages.show', $usage)" class="ml-2 !text-gray-600">
                                {{ $usage->customer_name }}
                            </x-button>
                        </td>
                        <td class="px-4 py-3">
                            <x-badge :color="$usage->buildingType->code->value === 'COMMERCIAL' ? 'blue' : 'amber'">
                                {{ $usage->buildingType->name }}
                            </x-badge>
                        </td>
                        <td class="px-4 py-3">{{ Month::from($usage->month)->label() }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format($usage->usage_kwh) }}</td>
                        <td class="px-4 py-3">{{ $usage->state->name }}</td>
                        <td class="px-4 py-3 text-right tabular-nums">{{ $usage->bill_amount }}</td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-4">
                                <x-button variant="link" :href="route('usages.edit', $usage)">Edit</x-button>
                                <x-delete-button :action="route('usages.destroy', $usage)" :name="$usage->customer_name" />
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-500">No records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-card>

    <div class="mt-4">
        {{ $usages->links() }}
    </div>
</x-layouts.app>