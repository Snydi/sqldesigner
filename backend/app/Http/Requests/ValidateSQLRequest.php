<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidateSQLRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'sql' => ['required', 'string'],
            'db_type' => ['nullable', 'in:mysql,postgresql'],
        ];
    }
}
