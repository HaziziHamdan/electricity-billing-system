<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreElectricityUsageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:150'],
            'building_type_id' => ['required', 'integer', Rule::exists('building_types', 'id')],
            'month' => ['required', 'integer', 'between:1,12'],
            'usage_kwh' => ['required', 'integer', 'min:0', 'max:1000000'],
            'state_id' => ['required', 'integer', Rule::exists('states', 'id')],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return [
            'customer_name' => 'customer name',
            'building_type_id' => 'building type',
            'usage_kwh' => 'usability (kWh)',
            'state_id' => 'state',
        ];
    }
}
