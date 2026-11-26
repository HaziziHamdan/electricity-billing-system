<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateTariffTiersRequest extends FormRequest
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
            'tiers' => ['required', 'array', 'min:1', 'max:10'],
            'tiers.*.up_to_kwh' => ['nullable', 'integer', 'min:1', 'max:1000000'],
            'tiers.*.rate' => ['required', 'numeric', 'decimal:0,4', 'gt:0', 'max:9999.9999'],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return [
            'tiers.*.up_to_kwh' => 'upper limit',
            'tiers.*.rate' => 'rate',
        ];
    }

    /**
     * Cross-row rules that per-field validation cannot express.
     *
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->isNotEmpty()) {
                    return;   // only check ordering once every row is individually valid
                }

                /** @var list<array{up_to_kwh?: int|string|null}> $tiers */
                $tiers = array_values($this->input('tiers', []));
                $lastIndex = count($tiers) - 1;
                $previousCap = 0;

                foreach ($tiers as $i => $tier) {
                    $upTo = $tier['up_to_kwh'] ?? null;
                    $row = $i + 1;

                    if ($upTo === null) {
                        if ($i !== $lastIndex) {
                            $validator->errors()->add("tiers.$i.up_to_kwh", "Tier {$row}: only the last tier can have no upper limit.");
                        }

                        continue;
                    }

                    if ($i === $lastIndex) {
                        $validator->errors()->add("tiers.$i.up_to_kwh", "Tier {$row}: the last tier must have no upper limit, so every kWh is billed.");
                    }

                    if ((int) $upTo <= $previousCap) {
                        $validator->errors()->add("tiers.$i.up_to_kwh", "Tier {$row}: upper limit must be greater than {$previousCap} kWh.");
                    }

                    $previousCap = (int) $upTo;
                }
            },
        ];
    }

    /**
     * Validated tiers, normalised for the service.
     *
     * @return list<array{up_to_kwh: int|null, rate: string}>
     */
    public function tiers(): array
    {
        /** @var array<int, array{up_to_kwh?: int|string|null, rate: int|float|string}> $tiers */
        $tiers = $this->validated('tiers');

        return array_values(array_map(
            fn (array $tier) => [
                'up_to_kwh' => isset($tier['up_to_kwh']) ? (int) $tier['up_to_kwh'] : null,
                'rate' => (string) $tier['rate'],
            ],
            $tiers,
        ));
    }
}
