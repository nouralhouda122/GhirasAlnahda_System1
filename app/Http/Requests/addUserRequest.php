<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class addUserRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'phone' => ['required', 'unique:users,phone'],
            'department_id' => ['required', 'exists:departments,id'],
            'role' => ['required', 'exists:roles,name'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // يمكنك تجهيز البيانات هنا
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
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a valid string.',
            'name.max' => 'The name may not be greater than 255 characters.',

            'email.required' => 'The email field is required.',
            'email.email' => 'The email format is invalid.',
            'email.unique' => 'This email is already taken.',

            'password.required' => 'The password field is required.',
            'password.string' => 'The password must be a valid string.',
            'password.min' => 'The password must be at least 8 characters.',

            'phone.required' => 'The phone number is required.',
            'phone.unique' => 'This phone number is already taken.',

            'department_id.required' => 'The department is required.',
            'department_id.exists' => 'The selected department is invalid.',

            'role.required' => 'The role is required.',
            'role.exists' => 'The selected role is invalid.',
        ];
    }
}
