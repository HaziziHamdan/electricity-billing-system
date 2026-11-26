<x-layouts.app title="New record">
    <form method="POST" action="{{ route('usages.store') }}" class="max-w-xl rounded border bg-white p-6">
        @csrf

        @include('usages._form')

        <div class="mt-6 flex items-center gap-3">
            <button type="submit" class="rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                Save
            </button>
            <a href="{{ route('usages.index') }}" class="text-sm text-gray-600 hover:underline">Cancel</a>
        </div>
    </form>
</x-layouts.app>