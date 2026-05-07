<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminSendEmailRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'subject' => 'required|string|max:255',
            'body'    => 'required|string|max:10000',
        ];
    }
}
