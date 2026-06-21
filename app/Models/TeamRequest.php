<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TeamRequest extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function creator()
    {
        return $this->belongsTo(
            VolunteerProfile::class,
            'creator_volunteer_profile_id'
        );
    }

    public function members()
    {
        return $this->hasMany(TeamRequestMember::class);
    }
}