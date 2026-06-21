<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IndicatorMatchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [

            'goal_indicator_id' => $this->id,
            'indicator_id' => $this->indicator_id,
            'indicator_name' => $this->indicator?->name,
            'indicator_description' => $this->indicator?->description,
            'domain' => $this->indicator?->domain,
            'type' => $this->indicator?->type,
            'target_value' => $this->target_value,

            'score' => round($this->score, 2),
            'status' => $this->approval_status,
        ];
    }
}
