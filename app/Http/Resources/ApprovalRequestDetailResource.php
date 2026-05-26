<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApprovalRequestDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'status' => $this->status,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'approvable' => $this->getApprovableDetails(),
            'requested_by' => [
                'id' => $this->requestedBy->id ?? null,
                'name' => $this->requestedBy->name ?? null,
                'email' => $this->requestedBy->email ?? null,
                'role' => $this->requestedBy->roles->first()->name ?? null,
            ],

        ];
    }

    private function getApprovableDetails()
    {
        return match ($this->approvable_type) {
            'course' => [
                'id' => $this->approvable->id,
                'title' => $this->approvable->title,
                'description' => $this->approvable->description ?? null,
                'skills' => $this->approvable->skills?->pluck('skill_name'),
                'schedules' => $this->approvable->schedules?->map(function ($schedule) {
                    return [
                        'day' => $schedule->day,
                        'from' => $schedule->from_time,
                        'to' => $schedule->to_time,
                    ];
                }),
                'instructor' => [
                    'id' => $this->approvable->instructor->id ?? null,
                    'name' => $this->approvable->instructor->name ?? null,
                    'email' => $this->approvable->instructor->email ?? null,
                ],
            ],

            default => null,
        };
    }
}
