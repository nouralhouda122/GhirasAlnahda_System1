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
                'required_if:status,banned',
                'nullable',
                'string',
                'max:255'
            ],

        ];
    }
    public function messages(): array
    {
        return [
            'status.required' => 'Status is required',
            'status.in' => 'Status must be active or banned',

        ];
    }
}
