@props(['href', 'active' => false])

<a href="{{ $href }}"
   @if ($active) aria-current="page" @endif
   {{ $attributes->class([
       'inline-flex items-center rounded-md px-3 py-2 text-sm font-medium transition whitespace-nowrap',
       'focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-600',
       'bg-brand-50 text-brand-700' => $active,
       'text-gray-600 hover:bg-gray-100 hover:text-gray-900' => ! $active,
   ]) }}>
    {{ $slot }}
</a>