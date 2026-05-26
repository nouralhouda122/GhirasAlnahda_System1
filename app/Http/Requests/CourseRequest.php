<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_hours' => 'required|integer|min:1',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'cost' => 'required|numeric|min:0',
            'required_points' => 'required|numeric|min:1',
            'instructor_id' => 'nullable|exists:users,id',
            'instructor' => 'nullable|array',
            'instructor.name' => 'required_with:instructor|string|max:255',
            'instructor.email' => 'required_with:instructor|email|unique:users,email',
            'instructor.specializations' => 'nullable|array',
            'instructor.specializations.*' => 'string|max:255',
            'instructor.bio' => 'nullable|string',

            'skills' => 'nullable|array',
            'skills.*' => 'string|max:255',
            'schedule' => 'nullable|array',

            'schedule.*.day' => 'required_with:schedule|string|max:20',
            'schedule.*.from_time' => 'required_with:schedule|date_format:H:i',
            'schedule.*.to_time' => 'required_with:schedule|date_format:H:i|after:schedule.*.from_time',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Course title is required.',
            'start_date.after_or_equal' => 'Start date must be today or later.',
            'end_date.after_or_equal' => 'End date must be after start date.',

            'instructor.email.unique' => 'This instructor email already exists.',

            'schedule.*.to_time.after' => 'End time must be after start time.',
        ];
    }
}
