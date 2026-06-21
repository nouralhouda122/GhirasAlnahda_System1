<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TeamRequestMember extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'responded_at' => 'datetime',
    ];

    public function teamRequest()
    {
        return $this->belongsTo(TeamRequest::class);
    }

    public function volunteerProfile()
    {
        return $this->belongsTo(VolunteerProfile::class);
    }
}