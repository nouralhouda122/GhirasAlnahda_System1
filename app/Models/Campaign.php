<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Campaign extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function Campaign_kpis(){
        return $this->hasMany(Campaign_kpi::class);
    }
    public function kpis()
    {
        return $this->hasMany(Campaign_kpi::class);
    }
    public function getImageAttribute($value)
    {
        if (empty($value)) {
            return [];
        }

        $images = json_decode($value, true);

        if (is_array($images) && !empty($images)) {
            return array_map(function($image) {
                return Storage::url($image);
            }, $images);
        }

        if (is_string($value) && !empty($value)) {
            return [Storage::url($value)];
        }

        return [];
    }

    public function getVideoAttribute($value)
    {
        if (empty($value)) {
            return [];
        }

        $videos = json_decode($value, true);

        if (is_array($videos) && !empty($videos)) {
            return array_map(function($video) {
                return Storage::url($video);
            }, $videos);
        }

        if (is_string($value) && !empty($value)) {
            return [Storage::url($value)];
        }

        return [];
    }
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
    public function goals()
    {
        return $this->hasMany(
            Campaign_kpi::class,
            'campaign_id'
        );
    }
    public function volunteerProfiles()
{
    return $this->belongsToMany(
        VolunteerProfile::class,
        'campaign_volunteer',
        'campaign_id',
        'volunteer_profile_id'
    )
    ->withPivot('status')
    ->withTimestamps();
}
    
    
    }
