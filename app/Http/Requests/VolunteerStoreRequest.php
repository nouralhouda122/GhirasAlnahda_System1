<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VolunteerStoreRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'age' => 'required|integer|min:15',
            'gender' => 'required|in:male,female',
            'current_address' => 'required|string|max:255',
            'cv' => 'required|file|mimes:pdf|max:2048',
            'preferred_sector' => 'required|in:relief,educational,medical,administrative',
            'preferred_field' => 'required|in:food_distribution,psychological_support,teaching,data_entry,media_marketing,logistics,first_aid',
            'weekly_hours_capacity' => 'required|integer|min:1|max:168',
            'message_title' => 'nullable|string|max:255',
            'message_content' => 'nullable|string',
        ];
    }
}
