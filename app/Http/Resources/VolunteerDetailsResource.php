<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VolunteerDetailsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'status' => $this->status,
            'image' => $this->image_url,

            'profile' => [
                'age' => $this->volunteerProfile?->age,
                'gender' => $this->volunteerProfile?->gender,
                'current_address' => $this->volunteerProfile?->current_address,

                'cv' => $this->volunteerProfile?->cv_path
                    ? asset('storage/' . $this->volunteerProfile->cv_path)
                    : null,
                'preferred_sector' => $this->volunteerProfile?->preferred_sector,
                'preferred_field' => $this->volunteerProfile?->preferred_field,

                'weekly_hours_capacity' => $this->volunteerProfile?->weekly_hours_capacity,

                'total_hours' => $this->volunteerProfile?->totalHours,
                'points' => $this->volunteerProfile?->pointsBalance,

                'is_team_leader' => $this->volunteerProfile?->isTeamLeader,
            ],
        ];
    }
}
