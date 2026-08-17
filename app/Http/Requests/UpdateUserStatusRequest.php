<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:active,banned'],

            'ban_reason' => [
                'nullable',
                'string',
                'max:255'
            ],

        ];
    }
    public function messages(): array
    {
        return [
            'status.in' => 'Status must be active or banned',

        ];
    }
}
