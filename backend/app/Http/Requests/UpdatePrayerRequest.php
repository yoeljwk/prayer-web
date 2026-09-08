<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePrayerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content' => [
                'sometimes',
                'required',
                'string',
                'min:3',
                'max:3000',
            ],

            'visibility' => [
                'sometimes',
                Rule::in([
                    'public',
                    'group',
                    'private',
                ]),
            ],

            'is_anonymous' => [
                'sometimes',
                'boolean',
            ],

            'prayer_group_id' => [
                'nullable',
                'integer',
                'exists:prayer_groups,id',
            ],

            'status' => [
                'sometimes',
                Rule::in(['active', 'answered']),
            ],

            'answered_at' => [
                'nullable',
                'date',
            ],
        ];
    }
}