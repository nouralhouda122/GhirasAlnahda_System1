<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCampanigRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'title' => 'sometimes|string|max:255',

            'location' => 'sometimes|string|max:255',

            'description' => 'sometimes|string',

            'type' => 'sometimes|in:relief,awareness,training,field,development,charity',

            'priority' => 'sometimes|in:low,medium,high',

            'start_date' => 'sometimes|date',

            'end_date' => 'sometimes|date',

            'latitude' => 'sometimes|numeric|between:-90,90',

            'longitude' => 'sometimes|numeric|between:-180,180',

            'radius' => 'sometimes|integer|min:1|max:100000',

            'required_volunteers' => 'sometimes|integer|min:0',

            'target_amount' => 'sometimes|numeric|min:0',

            'image' => 'sometimes|array',

            'image.*' => 'image|mimes:jpg,jpeg,png|max:2048',

            'video' => 'sometimes|array',

            'video.*' => 'file|mimes:mp4,mov,avi|max:10240',
        ];
    }
}
