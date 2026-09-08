<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'identity' => [
                'required_without:email',
                'string',
            ],

            'email' => [
                'required_without:identity',
                'string',
            ],

            'password' => [
                'required',
                'string',
            ],
        ];
    }
}