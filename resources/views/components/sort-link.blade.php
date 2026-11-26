@props(['field', 'filters'])

@php
    /** @var \App\Data\UsageFilters $filters */
    $active = $filters->sort?->value === $field;
    $nextDirection = $active ? $filters->direction->opposite() : \App\Enums\SortDirection::Asc;

    $url = request()->fullUrlWithQuery([
        'sort' => $field,
        'direction' => $nextDirection->value,
        'page' => null,   // back to page 1 when the order changes
    ]);
@endphp

<a href="{{ $url }}" @class(['inline-flex items-center gap-1 hover:text-blue-700', 'text-blue-700' => $active])>
    {{ $slot }}
    @if ($active)
        <span aria-hidden="true">{{ $filters->direction === \App\Enums\SortDirection::Asc ? '▲' : '▼' }}</span>
    @else
        <span aria-hidden="true" class="text-gray-300">↕</span>
    @endif
</a>