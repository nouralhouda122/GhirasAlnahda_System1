<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApprovalStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'required|in:approved,rejected,pending',
            'notes'  => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Status is required',
            'status.in' => 'Status must be approved, rejected, or pending',
            'notes.max' => 'Notes must not exceed 1000 characters',
        ];
    }
}
