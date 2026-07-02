<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddPointTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [

            'volunteer_id' => [
                'required',
                'exists:users,id'
            ],

            'campaign_id' => [
                'nullable',
                'exists:campaigns,id'
            ],

            'points' => [
                'required',
                'integer',
                'min:1'
            ],

            'type' => [
                'required',
                'string',
                'max:255'
            ],

            'reason' => [
                'nullable',
                'string',
                'max:255'
            ],

            'description' => [
                'nullable',
                'string'
            ]
        ];
    }

    public function messages(): array
    {
        return [

            'volunteer_id.required' =>
                'Volunteer is required.',

            'volunteer_id.exists' =>
                'Selected volunteer does not exist.',

            'campaign_id.exists' =>
                'Selected campaign does not exist.',

            'points.required' =>
                'Points value is required.',

            'points.integer' =>
                'Points must be an integer.',

            'points.min' =>
                'Points must be greater than zero.',

            'type.required' =>
                'Transaction type is required.'
        ];
    }
}
