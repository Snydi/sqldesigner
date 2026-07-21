<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class GeneratePromocodeRequest extends FormRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return ['duration_months' => ['required', 'integer', 'in:1,2,3,6']];
    }
}
