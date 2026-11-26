<x-layouts.app title="New record">
    <x-card class="max-w-xl">
        <form method="POST" action="{{ route('usages.store') }}" class="p-6">
            @csrf

            @include('usages._form')

            <div class="mt-6 flex items-center gap-3">
                <x-button type="submit">Save</x-button>
                <x-button variant="link" :href="route('usages.index')" class="!text-gray-600">Cancel</x-button>
            </div>
        </form>
    </x-card>
</x-layouts.app>