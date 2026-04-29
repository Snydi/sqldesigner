<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Knuckles\Scribe\Attributes\BodyParam;

#[BodyParam("email", "string", "The user's email address. Must be unique.", example: "user@example.com")]
#[BodyParam("password", "string", "The password. Min 8 chars, mixed case and numbers required.", example: "Secret1!")]
class RegisterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', Password::min(8)->letters()->mixedCase()->numbers()]
        ];
    }
}
