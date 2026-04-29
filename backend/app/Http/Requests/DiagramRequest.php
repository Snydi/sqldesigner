<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Knuckles\Scribe\Attributes\BodyParam;

#[BodyParam("name", "string", "The diagram name. Must be unique per user.", required: false, example: "My ERD")]
#[BodyParam("db_type", "string", "The database type. Allowed: mysql, postgresql.", required: false, example: "postgresql")]
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
            'db_type' => ['sometimes', 'string', 'in:mysql,postgresql'],
        ];
    }
}
