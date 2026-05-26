<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'check_in_time' => $this->check_in_time,
            'check_out_time' => $this->check_out_time,

            'hours' => round($this->hours, 2),

            'is_leader' => (bool) $this->is_leader,
            'is_active_session' => (bool) $this->is_active_session,

            'volunteer' => $this->whenLoaded('volunteer', function () {
                return [
                    'id' => $this->volunteer->id,
                    'name' => $this->volunteer->name,
                ];
            }),

            'campaign' => $this->whenLoaded('campaign', function () {
                return [
                    'id' => $this->campaign->id,
                    'title' => $this->campaign->title,
                ];
            }),
        ];
    }
}
