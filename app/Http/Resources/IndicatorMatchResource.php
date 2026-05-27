<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IndicatorMatchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        // هنا تم التعديل للقراءة من الـ pivot الخاص بعلاقة الـ belongsToMany
        return [
            'goal_indicator_id'     => $this->pivot?->id,
            'indicator_id'          => $this->id,
            'indicator_name'        => $this->name,
            'indicator_description' => $this->description,
            'domain'                => $this->domain,
            'type'                  => $this->type,
            'score'                 => $this->pivot?->score ? round($this->pivot->score, 2) : 0,
            'status'                => $this->pivot?->approval_status,
        ];
    }
}
