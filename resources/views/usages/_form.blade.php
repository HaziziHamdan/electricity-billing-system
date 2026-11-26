@php
    /** @var \App\Models\ElectricityUsage|null $usage */
    $usage ??= null;
    $input = 'block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-600 focus:ring-brand-600 sm:text-sm';
@endphp

<div class="space-y-5">
    <x-field label="Customer name" for="customer_name">
        <input type="text" id="customer_name" name="customer_name" maxlength="150" required value="{{ old('customer_name', $usage?->customer_name) }}" class="{{ $input }}">
    </x-field>

    {{-- Radio group: a fieldset with a legend, not a single label/input pair --}}
    <fieldset>
        <legend class="block text-sm font-medium text-gray-700">Building type</legend>
        <div class="mt-2 flex gap-6">
            @foreach ($buildingTypes as $type)
                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                    <input type="radio" name="building_type_id" value="{{ $type->id }}" required
                           class="border-gray-300 text-brand-600 focus:ring-brand-600"
                           @checked((string) old('building_type_id', $usage?->building_type_id) === (string) $type->id)>
                    {{ $type->name }}
                </label>
            @endforeach
        </div>
        @error('building_type_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </fieldset>

    <x-field label="Month" for="month">
        <select id="month" name="month" required class="{{ $input }}">
            <option value="">Please select</option>
            @foreach ($months as $month)
                <option value="{{ $month->value }}"
                        @selected((string) old('month', $usage?->month) === (string) $month->value)>
                    {{ $month->label() }}
                </option>
            @endforeach
        </select>
    </x-field>

    <x-field label="Usability (kWh)" for="usage_kwh" hint="Whole kWh only, e.g. 159.">
        <input type="number" id="usage_kwh" name="usage_kwh" min="0" max="1000000" step="1" required
               placeholder="159"
               value="{{ old('usage_kwh', $usage?->usage_kwh) }}"
               class="{{ $input }}">
    </x-field>

    <x-field label="State" for="state_id">
        <select id="state_id" name="state_id" required class="{{ $input }}">
            <option value="">Please select</option>
            @foreach ($states as $state)
                <option value="{{ $state->id }}"
                        @selected((string) old('state_id', $usage?->state_id) === (string) $state->id)>
                    {{ $state->name }}
                </option>
            @endforeach
        </select>
    </x-field>
</div>