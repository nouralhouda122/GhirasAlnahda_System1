<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CampaignSurveyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'survey_id'   => $this->id,
            'campaign_id' => $this->campaign_id,
            'title'       => $this->title,
            'stage'       => $this->stage,
            'status'      => $this->status,

            // 🛑 التعديل هنا: استخدام surveyQuestions بدلاً من questions
            'questions_count' => $this->surveyQuestions ? $this->surveyQuestions->count() : 0,

            // 🛑 والتعديل هنا أيضاً لتمرير الكولكشن الصحيح
            'questions' => SurveyQuestionResource::collection(
                $this->surveyQuestions
            ),

            'created_at' => $this->created_at,
        ];
    }
}
