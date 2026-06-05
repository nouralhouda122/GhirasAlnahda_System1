<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign_kpi extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function Campaign() {
        return $this->belongsTo(Campaign::class);
    }
    public function goalIndicators()
    {
        return $this->hasMany(
            GoalIndicator::class,
            'campaign_kpi_id'
        );
    }}
