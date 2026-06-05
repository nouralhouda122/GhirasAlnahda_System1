<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GoalIndicatorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'goal_id' => $this->id,
            'goal_text' => $this->goal_text,
            'indicators' => IndicatorMatchResource::collection(
                $this->goalIndicators
            ),
        ];
    }
}
