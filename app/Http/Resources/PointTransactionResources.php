<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PointTransactionResources extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'points' => $this->points,
            'type' => $this->type,
            'reason' => $this->reason,
            'description' => $this->description,
            'awarded_by' => $this->whenLoaded('awardedBy', function () {
                return [
                    'id' => $this->volunteer->id,
                    'name' => $this->volunteer->name,
                ];
            }),

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
