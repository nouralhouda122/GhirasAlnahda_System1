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

}
