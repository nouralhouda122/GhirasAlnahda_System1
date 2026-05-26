<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class searchUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'nullable|string|min:2|max:255',
            'role' => 'nullable|string|exists:roles,name',
        ];
    }

    public function messages(): array
    {
        return [
            'name.min' => 'The name must be at least 2 characters.',
            'name.string' => 'The name must be a valid string.',
            'role.string' => 'The role must be a valid string.',
            'role.exists' => 'The selected role does not exist.',
        ];
    }
}
