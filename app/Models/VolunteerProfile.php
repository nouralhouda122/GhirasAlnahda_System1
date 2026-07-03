<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VolunteerProfile extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * الحملات التي انضم إليها المتطوع
     */
   public function campaigns()
{
    return $this->belongsToMany(
        Campaign::class,
        'campaign_volunteer',
        'volunteer_profile_id',
        'campaign_id'
    )
    ->withPivot('status')
    ->withTimestamps();
}

    /**
     * المستخدم المرتبط بالمتطوع
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * طلبات الفرق التي أنشأها المتطوع
     */
    public function createdTeamRequests()
    {
        return $this->hasMany(
            TeamRequest::class,
            'creator_volunteer_profile_id'
        );
    }

    /**
     * الدعوات التي استلمها المتطوع
     */
    public function teamInvitations()
    {
        return $this->hasMany(
            TeamRequestMember::class,
            'volunteer_profile_id'
        );
    }
}