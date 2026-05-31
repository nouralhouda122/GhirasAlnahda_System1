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
        return [

            'question_id' => $this->id,

            'question_text' => $this->question_text,

            'type' => $this->type,
            'scale' => $this->scale,
            'indicators' => $this->indicators
                ->map(function ($indicator) {
                    return [
                        'indicator_id' => $indicator->id,
                        'indicator_name' => $indicator->name,
                        'indicator_description' => $indicator->description,
                        'phase' => $indicator->pivot->phase,
                    ];
                }),
            'created_at' => $this->created_at,
        ];
    }
}

