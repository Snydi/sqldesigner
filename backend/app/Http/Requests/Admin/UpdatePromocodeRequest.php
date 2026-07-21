<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\Promocode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePromocodeRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge(['code' => strtoupper((string) $this->input('code'))]);
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        /** @var Promocode $promocode */
        $promocode = $this->route('promocode');

        return [
            'code' => ['required', 'string', 'max:32', 'regex:/^[A-Za-z0-9-]+$/', Rule::unique('promocodes', 'code')->ignore($promocode)],
            'duration_months' => ['required', 'integer', 'in:1,2,3,6'],
        ];
    }
}
