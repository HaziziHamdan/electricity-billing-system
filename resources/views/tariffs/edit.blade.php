@php
    // After a validation error, show what the user typed; otherwise the saved tiers.
    $initialTiers = array_values(old('tiers', $buildingType->tariffTiers
        ->map(fn ($tier) => ['up_to_kwh' => $tier->up_to_kwh, 'rate' => $tier->rate])
        ->all()));

    $tierErrors = collect($errors->getMessages())
        ->filter(fn ($messages, $key) => str_starts_with($key, 'tiers'))
        ->flatten();
@endphp

<x-layouts.app :title="'Edit '.$buildingType->name.' rates'">
    <form method="POST" action="{{ route('tariffs.update', $buildingType) }}"
          x-data="{ tiers: @js($initialTiers) }"
          class="max-w-2xl rounded border bg-white p-6">
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
                            <input type="number" min="1" step="1" placeholder="No limit"
                                   :name="`tiers[${i}][up_to_kwh]`" x-model="tier.up_to_kwh"
                                   class="w-32 rounded border border-gray-300 px-2 py-1">
                        </td>
                        <td class="py-2 pr-4">
                            <input type="number" min="0.0001" step="0.0001" required
                                   :name="`tiers[${i}][rate]`" x-model="tier.rate"
                                   class="w-32 rounded border border-gray-300 px-2 py-1">
                        </td>
                        <td class="py-2">
                            <button type="button" x-show="tiers.length > 1" @click="tiers.splice(i, 1)"
                                    class="text-red-700 hover:underline">Remove</button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>

        <button type="button" @click="tiers.push({ up_to_kwh: null, rate: '' })"
                class="mt-3 text-sm text-blue-700 hover:underline">
            + Add tier
        </button>

        <p class="mt-4 text-xs text-gray-500">
            Each tier starts where the previous one ends. Leave "Up to" empty on the last tier so all usage above it is billed.
            Saving does not change existing bills.
        </p>

        <div class="mt-6 flex items-center gap-3">
            <button type="submit" class="rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                Save rates
            </button>
            <a href="{{ route('tariffs.index') }}" class="text-sm text-gray-600 hover:underline">Cancel</a>
        </div>
    </form>
</x-layouts.app>