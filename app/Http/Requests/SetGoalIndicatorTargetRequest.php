<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SetGoalIndicatorTargetRequest extends FormRequest
{
    public function rules(): array
    {
        return [

            'goal_id' => [
                'required',
                'exists:campaign_kpis,id'
            ],

            'indicator_id' => [
                'required',
                'exists:indicators,id'
            ],

            'target_value' => [
                'required',
                'numeric',
                'min:0'
            ]
        ];
    }
}
