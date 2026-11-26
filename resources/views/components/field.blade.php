@props(['label', 'for', 'name' => null, 'hint' => null])

<div {{ $attributes }}>
    <label for="{{ $for }}" class="block text-sm font-medium text-gray-700">{{ $label }}</label>
    <div class="mt-1">{{ $slot }}</div>
    @if ($hint)
        <p class="mt-1 text-xs text-gray-500">{{ $hint }}</p>
    @endif
    @error($name ?? $for)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>