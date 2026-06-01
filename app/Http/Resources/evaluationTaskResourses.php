<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class evaluationTaskResourses extends JsonResource
{
    /**
     * تحويل كائن مهمة التقييم إلى مصفوفة قابلة للتحويل إلى JSON
     */
    public function toArray(Request $request): array
    {
        return [
            'task_id'      => $this->task_id ?? $this->id, // يدعم جلب الكل (Join) أو جلب عنصر مفرد
            'task_title'   => $this->task_title ?? $this->title,
            'task_stage'   => $this->task_stage ?? $this->stage,
            'task_status'  => $this->task_status ?? $this->status,
            'due_date'     => $this->due_date,

            // بيانات الحملة المرتبطة تفصيلياً
            'campaign' => [
                'id'    => $this->campaign_id,
                'title' => $this->campaign_title ?? null, // يظهر الاسم في حال عمل جلب الكل مع الـ Join
            ],

            // بيانات موظف المراقبة والتقييم المسؤول
            'evaluator' => [
                'id'   => $this->evaluator_id,
                'name' => $this->evaluator_name ?? null,
            ],

            // بيانات الاستبيان المربوط بالمهمة
            'survey' => [
                'id'    => $this->survey_id,
                'title' => $this->survey_title ?? null,
            ],

            'created_at'   => $this->created_at ?? null,
        ];
    }
}
