<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CampaingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'location' => 'required|string',
            'description' => 'required|string',
            'type' => 'required|in:relief,awareness,training,field,development,charity',
            'priority' => 'required|in:low,medium,high',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'required|integer|min:1|max:100000',
            'required_volunteers' => 'required|integer|min:0',
            'target_amount' => 'required|numeric|min:0',
            'has_evaluation' => 'required|boolean',
            'goals' => 'required_if:has_evaluation,1|array|min:1',
            'goals.*' => 'required|string|max:255',
            'image' => 'nullable|array|min:1',
            'image.*.' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'video' => 'nullable|array|min:1',
            'video.*.' => 'nullable|file|mimes:mp4,mov,avi|max:10240',
        ];
    }}
