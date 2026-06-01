<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Helpers\ResponseHelper;

class addQuestionToSurveyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'question_text' => 'required|string|max:500',
            'type'          => 'required|string|in:rating,text,multiple_choice',
            'scale'         => 'required_if:type,rating|integer|min:2|max:10',
            'order'         => 'nullable|integer|min:1',

            'indicator_id'  => 'required|exists:indicators,id',
            'phase'         => 'required|string|in:before,during,after',
        ];
    }


    public function messages(): array
    {
        return [
            'question_text.required' => 'نص السؤال مطلوب ولا يمكن تركه فارغاً.',
            'type.required'          => 'نوع السؤال مطلوب (مثل: rating).',
            'type.in'                => 'نوع السؤال المختار غير مدعوم.',
            'scale.required_if'      => 'مقياس التقييم مطلوب عندما يكون نوع السؤال من نوع تقييم (rating).',
            'indicator_id.required'  => 'يجب ربط هذا السؤال بمؤشر أداء محدد.',
            'indicator_id.exists'    => 'المؤشر المختار غير موجود في قاعدة البيانات.',
            'phase.required'         => 'مرحلة الاستبيان مطلوبة.',
            'phase.in'               => 'المرحلة يجب أن تكون حصراً: before, during, أو after.',
        ];
    }


    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            ResponseHelper::Error(
                $validator->errors()->toArray(),
                'بيانات المدخلات غير صالحة، يرجى التحقق من الأخطاء.',
                422
            )
        );
    }
}
