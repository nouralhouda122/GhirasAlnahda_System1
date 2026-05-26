<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ];
    }

    public function messages(): array
    {
        return [
            'latitude.required' => 'Latitude is required',
            'longitude.required' => 'Longitude is required',
            'latitude.numeric' => 'Latitude must be a valid number',
            'longitude.numeric' => 'Longitude must be a valid number',
        ];
    }
}
