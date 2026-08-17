<?php


namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'status' => $this->status,

            'role' => $this->departmentRole?->role
                ? [$this->departmentRole->role->name]
                : [],

            'department' => [
                'id' => $this->departmentRole?->department?->id,
                'name' => $this->departmentRole?->department?->name,
            ],

            'created_at' => $this->created_at?->format('Y-m-d H:i'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i'),
        ];
    }}
