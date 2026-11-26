@props(['variant' => 'primary', 'href' => null, 'type' => 'button'])

@php
    $classes = 'inline-flex items-center justify-center gap-2 rounded-md px-4 py-2 text-sm font-medium transition
                focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:opacity-50 '
        . match ($variant) {
            'primary' => 'bg-brand-600 text-white hover:bg-brand-700 focus-visible:ring-brand-600',
            'secondary' => 'border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 focus-visible:ring-gray-400',
            'danger' => 'border border-red-200 bg-white text-red-700 hover:bg-red-50 focus-visible:ring-red-500',
            'link' => 'px-0 py-0 text-brand-700 hover:underline',
            'dark' => 'bg-gray-800 text-white hover:bg-gray-900 focus-visible:ring-gray-800',
        };
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</button>
@endif