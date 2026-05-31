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

            'survey_id' => $this->id,

            'campaign_id' => $this->campaign_id,

            'title' => $this->title,

            'stage' => $this->stage,

            'status' => $this->status,

            'questions_count' =>
                $this->questions->count(),

            'questions' =>
                SurveyQuestionResource::collection(
                    $this->questions
                ),

            'created_at' =>
                $this->created_at,
        ];
    }
}
