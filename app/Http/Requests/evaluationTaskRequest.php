<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;

class evaluationTaskRequest extends FormRequest
{
    /**
     * تحديد ما إذا كان المستخدم مخولاً لإجراء هذا الطلب
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * قواعد التحقق الصارمة لإنشاء مهمة التقييم
     */
    public function rules(): array
    {
        return [
            'title'        => 'required|string|min:5|max:255',
            'campaign_id'  => 'required|integer|exists:campaigns,id',

            // 👈 التحقق المزدوج: أن يكون مستخدماً موجوداً، وأن يكون دوره Evaluation Officer (رقم 6 في قاعدة بياناتك)
            'evaluator_id' => [
                'required',
                'integer',
                'exists:users,id',
                function ($attribute, $value, $fail) {
                    $hasCorrectRole = DB::table('model_has_roles')
                        ->where('model_id', $value)
                        ->where('model_type', 'App\Models\User') // تأكد من مطابقة الـ Namespace لموديل اليوزر عندك
                        ->where('role_id', 6) // 6 هو معرف دور Evaluation Officer بناءً على الداتابيز لديك
                        ->exists();

                    if (!$hasCorrectRole) {
                        $fail('المستخدم المحدد يجب أن يكون موظف مراقبة وتقييم (Evaluation Officer) حصراً.');
                    }
                }
            ],

            'survey_id'    => 'required|integer|exists:surveys,id',
            'stage'        => 'required|string|in:before,during,after',
            'due_date'     => 'nullable|date|after_or_equal:today',
        ];
    }

    /**
     * تخصيص رسائل الخطأ لتظهر باللغة العربية بوضوح للواجهة
     */
    public function messages(): array
    {
        return [
            'title.required'         => 'عنوان مهمة التقييم مطلوب.',
            'title.min'              => 'يجب أن يكون عنوان المهمة 5 أحرف على الأقل.',
            'campaign_id.required'   => 'يجب تحديد الحملة المرتبطة بهذه المهمة.',
            'campaign_id.exists'     => 'الحملة المحددة غير موجودة بالنظام.',
            'evaluator_id.required'  => 'يجب تحديد الموظف المسؤول عن التقييم.',
            'evaluator_id.exists'    => 'الموظف (المقيّم) المحدد غير موجود في قائمة المستخدمين.',
            'survey_id.required'     => 'يجب اختيار الاستبيان المراد تعيينه للمهمة.',
            'survey_id.exists'       => 'الاستبيان المحدد غير موجود في قاعدة البيانات.',
            'stage.required'         => 'مرحلة التقييم (قبل، أثناء، بعد) حقل إلزامي.',
            'stage.in'               => 'المرحلة الزمنية المختارة للمهمة غير صالحة.',
            'due_date.date'          => 'صيغة تاريخ الاستحقاق غير صحيحة.',
            'due_date.after_or_equal'=> 'تاريخ نهاية المهمة لا يمكن أن يكون في الماضي، يجب أن يكون اليوم أو تاريخاً مستقبلياً.',
        ];
    }

    /**
     * توحيد استجابة الفشل (Validation Response) لتطابق الـ Standard المعتمد لديك
     */
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
