<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Knuckles\Scribe\Attributes\BodyParam;

#[BodyParam("email", "string", "The sender's email address.", required: false, example: "user@example.com")]
#[BodyParam("message", "string", "The feedback message.", example: "I love this tool!")]
class FeedbackRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => 'nullable|email|max:255',
            'message' => 'required|string|max:5000',
        ];
    }
}