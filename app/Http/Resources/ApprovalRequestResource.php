<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApprovalRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'type' => $this->type,

            'status' => $this->status,
            'notes' => $this->notes,

            'title' => $this->getTitle(),

            'requested_by' => [
                'id' => $this->requestedBy->id ?? null,
                'name' => $this->requestedBy->name ?? null,
                'email' => $this->requestedBy->email ?? null,
                'role' => $this->requestedBy->roles->first()->name ?? null,
            ],

            'created_at' => $this->created_at,
        ];
    }

    private function getTitle()
    {
        return match ($this->approvable_type) {
            'course' => $this->approvable->title ?? null,
            default => null,
        };
    }
}
