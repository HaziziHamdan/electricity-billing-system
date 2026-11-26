<x-layouts.app title="Edit record">
    <form method="POST" action="{{ route('usages.update', $usage) }}" class="max-w-xl rounded border bg-white p-6">
        @csrf
        @method('PUT')

        @include('usages._form', ['usage' => $usage])

        <p class="mt-5 text-sm text-gray-500">
            Current bill: <span class="font-medium text-gray-800">RM {{ $usage->bill_amount }}</span>.
            It is recalculated at current rates only if the building type or usability changes.
        </p>

        <div class="mt-6 flex items-center gap-3">
            <button type="submit" class="rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                Update
            </button>
            <a href="{{ route('usages.index') }}" class="text-sm text-gray-600 hover:underline">Cancel</a>
        </div>
    </form>
</x-layouts.app>