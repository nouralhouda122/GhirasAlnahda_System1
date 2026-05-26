<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchCampaignRequest extends FormRequest
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
            'title' => 'nullable|string|max:255|min:2',
            'type' => 'nullable|in:relief,awareness,training,field,development,charity',
            'status' => 'nullable|in:pending_approval,approved,rejected,ongoing,completed,archived',
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'title.min' => 'Campaign title must be at least 2 characters',
            'title.max' => 'Campaign title cannot exceed 255 characters',
            'type.in' => 'Invalid campaign type',
            'status.in' => 'Invalid campaign status',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'title' => 'campaign title',
            'type' => 'campaign type',
            'status' => 'campaign status',
        ];
    }
}
