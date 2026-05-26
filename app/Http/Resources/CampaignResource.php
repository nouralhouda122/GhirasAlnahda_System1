<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CampaignResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'type' => $this->type,
            'location' =>$this->location,
            'status' => $this->status,
            'priority' => $this->priority,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'image' => $this->image,
            'volunteer_progress' => $this->required_volunteers > 0
                ? round(($this->current_volunteers / $this->required_volunteers) * 100, 1)
                : 0,
            'donation_progress' => $this->target_amount > 0
                ? round(($this->current_amount / $this->target_amount) * 100, 1)
                : 0,
            'has_evaluation' => (bool) $this->has_evaluation,
        ];
    }
}
