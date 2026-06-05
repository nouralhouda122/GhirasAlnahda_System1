<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class updateQuestionToSurveyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * قواعد التحقق المرنة للتعديل (Sometimes)
     */
    public function rules(): array
    {
        return [
            // أصبحت sometimes لكي لا يجبر الفرونت إند على إرسالها إن لم تتغير
            'question_text' => 'sometimes|string|min:5|max:1000',
            'type'          => 'sometimes|string|in:rating,text,multiple_choice',

            // الـ scale مطلوب فقط إذا تم إرسال النوع وكان نوعه rating
            'scale'         => 'required_if:type,rating|nullable|integer|min:2|max:10',

            'indicator_id'  => 'sometimes|integer|exists:indicators,id',
            'phase'         => 'sometimes|string|in:before,during,after',
            'order'         => 'nullable|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'question_text.min'     => 'يجب أن يكون نص السؤال 5 أحرف على الأقل.',
            'type.in'               => 'نوع السؤال المختار غير مدعوم في النظام.',
            'scale.required_if'     => 'مقياس التقييم (Scale) مطلوب عندما يتم تحويل نوع السؤال إلى تقييم.',
            'indicator_id.exists'   => 'المؤشر المحدد غير موجود في قاعدة البيانات.',
            'phase.in'              => 'المرحلة المختارة غير صالحة.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'status'  => 0,
                'data'    => '',
                'message' => $validator->errors()->first()
            ], 422)
        );
    }
}
