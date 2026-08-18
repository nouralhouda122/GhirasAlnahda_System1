<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GeneralDashboardDetailsRequest extends FormRequest
{
    /**
     * تحديد صلاحية تنفيذ الطلب
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * قواعد التحقق
     */
    public function rules(): array
    {
        return [
            'type' => [
                'required',
                'in:campaigns,volunteers,donations',
            ],

            'start_date' => [
                'required',
                'date',
            ],

            'end_date' => [
                'required',
                'date',
                'after_or_equal:start_date',
            ],
        ];
    }

    /**
     * رسائل التحقق
     */
    public function messages(): array
    {
        return [
            'type.required' =>
                'Statistics type is required.',

            'type.in' =>
                'Statistics type must be campaigns, volunteers, or donations.',

            'start_date.required' =>
                'Start date is required.',

            'start_date.date' =>
                'Start date must be a valid date.',

            'end_date.required' =>
                'End date is required.',

            'end_date.date' =>
                'End date must be a valid date.',

            'end_date.after_or_equal' =>
                'End date must be after or equal to start date.',
        ];
    }
}
