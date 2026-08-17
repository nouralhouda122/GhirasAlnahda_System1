<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'unique:users,email',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
            ],

            'phone' => [
                'required',
                'string',
                'unique:users,phone',
            ],

            'department_id' => [
                'required',
                'integer',
                'exists:departments,id',
            ],

            'role' => [
                'required',
                'integer',
                'exists:roles,id',
            ],

        ];
    }

    public function messages(): array
    {
        return [

            'name.required' => 'The name field is required.',
            'name.max' => 'The name may not be greater than 255 characters.',

            'email.required' => 'The email field is required.',
            'email.email' => 'Invalid email format.',
            'email.unique' => 'This email is already taken.',

            'password.required' => 'The password field is required.',
            'password.min' => 'The password must be at least 8 characters.',

            'phone.required' => 'The phone field is required.',
            'phone.unique' => 'This phone number is already taken.',

            'department_id.required' => 'The department is required.',
            'department_id.exists' => 'The selected department does not exist.',

            'role.required' => 'The role is required.',
            'role.exists' => 'The selected role does not exist.',

        ];
    }
}
