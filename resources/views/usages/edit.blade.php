<x-layouts.app title="Edit record">
    <x-card class="max-w-xl">
        <form method="POST" action="{{ route('usages.update', $usage) }}" class="p-6">
            @csrf
            @method('PUT')

            @include('usages._form', ['usage' => $usage])

            <p class="mt-5 text-sm text-gray-500">
                Current bill: <span class="font-medium text-gray-800">RM {{ $usage->bill_amount }}</span>.
                It is recalculated at current rates only if the building type or usability changes.
            </p>

            <div class="mt-6 flex items-center gap-3">
                <x-button type="submit">Update</x-button>
                <x-button variant="link" :href="route('usages.index')" class="!text-gray-600">Cancel</x-button>
            </div>
        </form>
    </x-card>
</x-layouts.app>