<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VolunteerProfile extends Model
{
    protected $guarded=[];
    use HasFactory;
    public function campaigns()
    {
        return $this->belongsToMany(
            \App\Models\Campaign::class,
            'campaign_volunteer',
            'volunteer_profile_id',
            'campaign_id'
        );
    }
    
public function user()
{
    return $this->belongsTo(User::class, 'user_id');
}
public function createdTeamRequests()
{
    return $this->hasMany(
        TeamRequest::class,
        'creator_volunteer_profile_id'
    );
}

public function teamInvitations()
{
    return $this->hasMany(
        TeamRequestMember::class,
        'volunteer_profile_id'
    );
}

}
