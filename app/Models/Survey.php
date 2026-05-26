<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    protected $fillable = [
        'campaign_id',
        'indicator_id',
        'stage',
        'title',
        'status'
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function indicator()
    {
        return $this->belongsTo(Indicator::class);
    }

    public function questions()
    {
        return $this->hasMany(surveyQuestion::class);
    }

    public function answers()
    {
        return $this->hasMany(surveyAnswer::class);
    }
}
