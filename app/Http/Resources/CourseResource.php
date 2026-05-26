<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'duration_hours' => $this->duration_hours,
            'cost' => $this->cost,
            'required_points' => $this->required_points,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'instructor' => [
                'id' => $this->instructor?->id,
                'name' => $this->instructor?->name,
                'phone' => $this->instructor?->phone,
                'email' => $this->instructor?->email,
                'specializations' => $this->instructor?->specializations?->pluck('name'),
            ],
            'skills' => $this->skills->pluck('skill_name'),
           'schedule' => $this->schedules->map(function ($schedule) {
                return [
                    'day' => $schedule->day,
                    'from_time' => $schedule->from_time,
                    'to_time' => $schedule->to_time,
                ];
            }),
            'created_at' => $this->created_at,
        ];
    }
}
