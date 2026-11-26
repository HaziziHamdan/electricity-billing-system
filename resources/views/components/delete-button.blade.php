@props(['action', 'name', 'variant' => 'link'])

<form method="POST" action="{{ $action }}"
      onsubmit="return confirm(@js('Delete the record for '.$name.'?'));">
    @csrf
    @method('DELETE')
    <x-button type="submit" :variant="$variant === 'link' ? 'link' : 'danger'"
              @class(['!text-red-700' => $variant === 'link'])>
        Delete
    </x-button>
</form>