<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SurveyQuestionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        // 🛑 الوصول لموديل السؤال الأساسي المرتبط بسجل الربط الحالي
        $question = $this->question;

        return [
            // بيانات من جدول الربط (survey_questions)
            'survey_question_id' => $this->id,
            'order'              => $this->order,

            // جلب البيانات الأساسية من موديل السؤال (مع الحماية إذا كان فارغاً)
            'question_id'   => $question ? $question->id : null,
            'question_text' => $question ? $question->question_text : '',
            'type'          => $question ? $question->type : '',
            'scale'         => $question ? $question->scale : null,

            // جلب مؤشرات الأداء من موديل السؤال الأساسي بحماية كاملة تمنع خطأ الـ map
            'indicators' => ($question && $question->indicators)
                ? $question->indicators->map(function ($indicator) {
                    return [
                        'indicator_id'          => $indicator->id,
                        'indicator_name'        => $indicator->name,
                        'indicator_description' => $indicator->description,
                        'phase'                 => $indicator->pivot ? $indicator->pivot->phase : null,
                    ];
                })
                : [],

            'created_at' => $this->created_at,
        ];
    }
}
