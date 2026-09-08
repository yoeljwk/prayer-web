<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePrayerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content' => [
                'required',
                'string',
                'min:3',
                'max:3000',
            ],

            'visibility' => [
                'required',
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
                'required_if:visibility,group',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'content.required' =>
                'Permohonan doa tidak boleh kosong.',

            'content.max' =>
                'Permohonan doa maksimal 3000 karakter.',

            'visibility.required' =>
                'Pilih siapa yang dapat melihat doa ini.',

            'prayer_group_id.required_if' =>
                'Pilih komunitas untuk doa grup.',
        ];
    }
}