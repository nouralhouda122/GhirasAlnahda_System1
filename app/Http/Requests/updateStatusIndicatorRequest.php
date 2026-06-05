<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class updateStatusIndicatorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'approval_status' => [
                'required',
                'string',
                Rule::in(['pending', 'approved', 'rejected']),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'approval_status.required' => 'حالة الاعتماد مطلوبة ولا يمكن تركها فارغة.',
            'approval_status.string'   => 'يجب أن تكون حالة الاعتماد عبارة عن نص.',
            'approval_status.in'       => 'الحالة المرسلة غير صحيحة، يجب أن تكون: pending، approved، أو rejected.',
        ];
    }
}
