@props(['color' => 'gray'])

@php
    $colors = [
        'gray' => 'bg-gray-100 text-gray-700',
        'blue' => 'bg-blue-50 text-blue-700 ring-blue-600/20',
        'amber' => 'bg-amber-50 text-amber-800 ring-amber-600/20',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset '.$colors[$color]]) }}>
    {{ $slot }}
</span>