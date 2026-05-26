<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VolunteerListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'name' => $this->name,
            'image' => $this->image,

            'age' => $this->volunteerProfile?->age,
            'gender' => $this->volunteerProfile?->gender,

            'city' => $this->volunteerProfile?->current_address,

            'preferred_sector' => $this->volunteerProfile?->preferred_sector,
            'preferred_field' => $this->volunteerProfile?->preferred_field,

            'is_team_leader' => $this->volunteerProfile?->isTeamLeader,

            'points' => $this->volunteerProfile?->pointsBalance,
        ];
    }
}
