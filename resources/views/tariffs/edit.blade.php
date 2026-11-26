@php
    // After a validation error, show what the user typed; otherwise the saved tiers.
    $initialTiers = array_values(old('tiers', $buildingType->tariffTiers
        ->map(fn ($tier) => ['up_to_kwh' => $tier->up_to_kwh, 'rate' => $tier->rate])
        ->all()));

    $tierErrors = collect($errors->getMessages())
        ->filter(fn ($messages, $key) => str_starts_with($key, 'tiers'))
        ->flatten();
@endphp

<x-layouts.app>
    <x-page-header :title="'Edit '.$buildingType->name.' rates'"></x-page-header>

    <x-card class="max-w-2xl">
        <form method="POST" action="{{ route('tariffs.update', $buildingType) }} "x-data="{ tiers: @js($initialTiers) }" class="p-6">
            @csrf
            @method('PUT')

            @if ($tierErrors->isNotEmpty())
                <ul class="mb-4 list-inside list-disc rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    @foreach ($tierErrors as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            @endif

            <table class="min-w-full text-sm">
                <thead class="text-left text-xs uppercase tracking-wide text-gray-600">
                    <tr>
                        <th class="py-2 pr-4">From (kWh)</th>
                        <th class="py-2 pr-4">Up to (kWh)</th>
                        <th class="py-2 pr-4">Rate (RM/kWh)</th>
                        <th class="py-2"><span class="sr-only">Remove</span></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(tier, i) in tiers" :key="i">
                        <tr>
                            <td class="py-2 pr-4 text-gray-500"
                                x-text="i === 0 ? 1 : (Number(tiers[i - 1].up_to_kwh) || 0) + 1"></td>
                            <td class="py-2 pr-4">
                                <input type="number" min="1" step="1" placeholder="No limit" :name="`tiers[${i}][up_to_kwh]`" x-model="tier.up_to_kwh" class="w-32 rounded border border-gray-300 px-2 py-1">
                            </td>
                            <td class="py-2 pr-4">
                                <input type="number" min="0.0001" step="0.0001" required :name="`tiers[${i}][rate]`" x-model="tier.rate" class="w-32 rounded border border-gray-300 px-2 py-1">
                            </td>
                            <td class="py-2">
                                {{-- <button type="button" x-show="tiers.length > 1" @click="tiers.splice(i, 1)" class="text-red-700 hover:underline">Remove</button> --}}
                                <x-button variant="link" class="!text-red-700" x-show="tiers.length > 1" x-on:click="tiers.splice(i, 1)">
                                    Remove
                                </x-button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>

            <x-button variant="link" class="mt-3" x-on:click="tiers.push({ up_to_kwh: null, rate: '' })">
                + Add tier
            </x-button>

            <p class="mt-4 text-xs text-gray-500">
                Each tier starts where the previous one ends. Leave "Up to" empty on the last tier so all usage above it is billed.
                Saving does not change existing bills.
            </p>

            <div class="mt-6 flex items-center gap-3">
                <x-button type="submit">Save rates</x-button>
                <x-button variant="link" :href="route('tariffs.index')" class="!text-gray-600">Cancel</x-button>
            </div>
        </form>
    </x-card>
</x-layouts.app>