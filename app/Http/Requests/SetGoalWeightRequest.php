<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SetGoalWeightRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'goal_id' => [
                'required',
                'exists:campaign_kpis,id'
            ],

            'weight' => [
                'required',
                'numeric',
                'min:0.1'
            ]
        ];
    }
}
