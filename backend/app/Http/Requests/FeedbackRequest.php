<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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