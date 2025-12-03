<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DiagramRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => [
                'string',
                'max:255',
                Rule::unique('diagrams')->where(function ($query) { //TODO move this validation to a Policy
                    return $query->where('user_id', auth()->id());
                })
            ],
        ];
    }
}
