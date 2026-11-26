@props(['title' => null])

<div {{ $attributes->merge(['class' => 'rounded-lg border border-gray-200 bg-white shadow-sm']) }}>
    @if ($title)
        <div class="border-b border-gray-200 px-5 py-3">
            <h2 class="text-sm font-semibold text-gray-900">{{ $title }}</h2>
        </div>
    @endif
    {{ $slot }}
</div>