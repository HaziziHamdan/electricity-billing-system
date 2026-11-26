@php use App\Enums\Month; @endphp

<x-layouts.app>
    <x-page-header title="Record details"></x-page-header>

    <div class="max-w-2xl space-y-6">
        <x-card class="grid grid-cols-3 gap-x-4 gap-y-3 rounded border bg-white p-6 text-sm">
            <dt class="text-gray-500">Customer name</dt>
            <dd class="col-span-2 font-medium">{{ $usage->customer_name }}</dd>

            <dt class="text-gray-500">Building type</dt>
            <dd class="col-span-2">{{ $usage->buildingType->name }}</dd>

            <dt class="text-gray-500">Month</dt>
            <dd class="col-span-2">{{ Month::from($usage->month)->label() }}</dd>

            <dt class="text-gray-500">Usability</dt>
            <dd class="col-span-2">{{ number_format($usage->usage_kwh) }} kWh</dd>

            <dt class="text-gray-500">State</dt>
            <dd class="col-span-2">{{ $usage->state->name }}</dd>

            <dt class="text-gray-500">Recorded</dt>
            <dd class="col-span-2">{{ $usage->created_at?->format('d M Y, h:i A') }}</dd>
        </x-card>

        <x-card>
            <h2 class="border-b border-gray-400 px-6 py-3 font-semibold">Bill breakdown</h2>

            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-600">
                    <tr>
                        <th class="px-6 py-2">Tier</th>
                        <th class="px-6 py-2 text-right">kWh</th>
                        <th class="px-6 py-2 text-right">Rate (RM/kWh)</th>
                        <th class="px-6 py-2 text-right tabular-nums">Amount (RM)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @php $from = 1; @endphp
                    @forelse ($usage->tariff_snapshot as $line)
                        <tr>
                            <td class="px-6 py-2">
                                {{ $line['up_to_kwh'] === null ? $from.' kWh onwards' : $from.' – '.$line['up_to_kwh'].' kWh' }}
                            </td>
                            <td class="px-6 py-2 text-right">{{ number_format($line['units']) }}</td>
                            <td class="px-6 py-2 text-right">{{ $line['rate'] }}</td>
                            <td class="px-6 py-2 text-right">{{ $line['amount'] }}</td>
                        </tr>
                        @php $from = ($line['up_to_kwh'] ?? 0) + 1; @endphp
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">No usage, so nothing to bill.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="border-t border-gray-400 bg-gray-50 font-semibold">
                    <tr>
                        <td class="px-6 py-3" colspan="3">Total</td>
                        <td class="px-6 py-3 text-right">RM {{ $usage->bill_amount }}</td>
                    </tr>
                </tfoot>
            </table>

            <p class="border-t border-gray-400 px-6 py-3 text-xs text-gray-500">
                Rates shown are those in effect when this bill was calculated.
            </p>
        </x-card>

        <div class="flex items-center gap-3">
            <x-button :href="route('usages.edit', $usage)">Edit</x-button>

            <x-delete-button variant="danger" :action="route('usages.destroy', $usage)" :name="$usage->customer_name" />

            <x-button variant="link" :href="route('usages.index')" class="!text-gray-600">Back to records</x-button>
        </div>
    </div>
</x-layouts.app>