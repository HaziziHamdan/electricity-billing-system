@php
    /** @var \App\Models\ElectricityUsage|null $usage */
    $usage ??= null;
    $input = 'mt-1 block w-full rounded border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500';
@endphp

<div class="space-y-5">
    {{-- Customer name --}}
    <div>
        <label for="customer_name" class="block text-sm font-medium">Customer name</label>
        <input type="text" id="customer_name" name="customer_name" maxlength="150" required
               value="{{ old('customer_name', $usage?->customer_name) }}"
               class="{{ $input }} border">
        @error('customer_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Building type --}}
    <fieldset>
        <legend class="block text-sm font-medium">Building type</legend>
        <div class="mt-2 flex gap-6">
            @foreach ($buildingTypes as $type)
                <label class="inline-flex items-center gap-2 text-sm">
                    <input type="radio" name="building_type_id" value="{{ $type->id }}" required
                           @checked((string) old('building_type_id', $usage?->building_type_id) === (string) $type->id)>
                    {{ $type->name }}
                </label>
            @endforeach
        </div>
        @error('building_type_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </fieldset>

    {{-- Month --}}
    <div>
        <label for="month" class="block text-sm font-medium">Month</label>
        <select id="month" name="month" required class="{{ $input }} border">
            <option value="">Please select</option>
            @foreach ($months as $month)
                <option value="{{ $month->value }}"
                        @selected((string) old('month', $usage?->month) === (string) $month->value)>
                    {{ $month->label() }}
                </option>
            @endforeach
        </select>
        @error('month') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Usability (kWh) --}}
    <div>
        <label for="usage_kwh" class="block text-sm font-medium">Usability (kWh)</label>
        <input type="number" id="usage_kwh" name="usage_kwh" min="0" max="1000000" step="1" required
               placeholder="159"
               value="{{ old('usage_kwh', $usage?->usage_kwh) }}"
               class="{{ $input }} border">
        @error('usage_kwh') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- State --}}
    <div>
        <label for="state_id" class="block text-sm font-medium">State</label>
        <select id="state_id" name="state_id" required class="{{ $input }} border">
            <option value="">Please select</option>
            @foreach ($states as $state)
                <option value="{{ $state->id }}"
                        @selected((string) old('state_id', $usage?->state_id) === (string) $state->id)>
                    {{ $state->name }}
                </option>
            @endforeach
        </select>
        @error('state_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
</div>
