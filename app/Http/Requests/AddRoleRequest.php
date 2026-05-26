<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class AddRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required','string'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
    }

    /**
     * Get the validation rules that apply to the request.
     */

    /**
     * Get custom messages for validation errors.
     */

    public function messages(): array
    {
        return [

            'role.required' => 'The role is required.',
            'role.exists' => 'The selected role is invalid.',
        ];
    }
}
