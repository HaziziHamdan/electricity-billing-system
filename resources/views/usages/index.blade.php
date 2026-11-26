@php use App\Enums\Month; @endphp

<x-layouts.app title="Electricity usage records">
    <div class="mb-4 flex justify-end">
        <a href="{{ route('usages.create') }}"
           class="rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
            New record
        </a>
    </div>

    {{-- Filters and sorting are added in the next step. --}}

    <div class="overflow-x-auto rounded border bg-white">
        <table class="min-w-full divide-y text-sm">
            <thead class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-600">
                <tr>
                    <th class="px-4 py-3">No</th>
                    <th class="px-4 py-3">Customer name</th>
                    <th class="px-4 py-3">Building type</th>
                    <th class="px-4 py-3">Month</th>
                    <th class="px-4 py-3 text-right">Usability (kWh)</th>
                    <th class="px-4 py-3">State</th>
                    <th class="px-4 py-3 text-right">Bill (RM)</th>
                    <th class="px-4 py-3"><span class="sr-only">Actions</span></th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($usages as $usage)
                    <tr>
                        <td class="px-4 py-3">{{ $usages->firstItem() + $loop->index }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('usages.show', $usage) }}" class="text-blue-700 hover:underline">
                                {{ $usage->customer_name }}
                            </a>
                        </td>
                        <td class="px-4 py-3">{{ $usage->buildingType->name }}</td>
                        <td class="px-4 py-3">{{ Month::from($usage->month)->label() }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format($usage->usage_kwh) }}</td>
                        <td class="px-4 py-3">{{ $usage->state->name }}</td>
                        <td class="px-4 py-3 text-right">{{ $usage->bill_amount }}</td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-3">
                                <a href="{{ route('usages.edit', $usage) }}" class="text-blue-700 hover:underline">Edit</a>

                                <form method="POST" action="{{ route('usages.destroy', $usage) }}"
                                      onsubmit="return confirm('Delete the record for {{ e($usage->customer_name) }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-700 hover:underline">Delete</button>
                                </form>
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
    </div>

    <div class="mt-4">
        {{ $usages->links() }}
    </div>
</x-layouts.app>