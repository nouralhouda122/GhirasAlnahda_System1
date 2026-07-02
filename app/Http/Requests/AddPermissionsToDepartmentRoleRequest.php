<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddPermissionsToDepartmentRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'department_id' => [
                'required',
                'integer',
                'exists:departments,id',
            ],

            'role_id' => [
                'required',
                'integer',
                'exists:roles,id',
            ],


            'permission_ids' => [
                'required',
                'array',
                'min:1',
            ],

            'permission_ids.*' => [
                'required',
                'integer',
                'exists:permissions,id',
                'distinct',
            ],

        ];
    }
}
