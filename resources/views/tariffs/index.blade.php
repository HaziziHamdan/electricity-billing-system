<x-layouts.app>
    <x-page-header title="Tariff rates" description="New and recalculated bills use these rates. Existing bills keep the rates they were calculated with."></x-page-header>

    <div class="grid gap-6 md:grid-cols-2">
        @foreach ($buildingTypes as $type)
            <x-card class="bg-white">
                <div class="flex items-center justify-between px-4 py-3">
                    <h2 class="font-semibold">{{ $type->name }}</h2>
                    <x-button variant="link" :href="route('tariffs.edit', $type)">Edit rates</x-button>
                </div>

                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-600">
                        <tr>
                            <th class="px-4 py-2">Tariff category</th>
                            <th class="px-4 py-2 text-right">Rate (RM/kWh)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @php $from = 1; @endphp
                        @forelse ($type->tariffTiers as $tier)
                            <tr>
                                <td class="px-4 py-2">
                                    {{ $tier->up_to_kwh === null ? $from.' kWh onwards' : $from.' – '.$tier->up_to_kwh.' kWh' }}
                                </td>
                                <td class="px-4 py-2 text-right">{{ $tier->rate }}</td>
                            </tr>
                            @php $from = ($tier->up_to_kwh ?? 0) + 1; @endphp
                        @empty
                            <tr>
                                <td colspan="2" class="px-4 py-4 text-center text-red-600">No tiers configured. Bills cannot be calculated.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </x-card>
        @endforeach
    </div>
</x-layouts.app>