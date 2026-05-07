<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Knuckles\Scribe\Attributes\BodyParam;

#[BodyParam("username", "string", "The admin username.", example: "admin")]
#[BodyParam("password", "string", "The admin password.", example: "secret")]
class AdminLoginRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'username' => 'required|string',
            'password' => 'required|string',
        ];
    }
}
