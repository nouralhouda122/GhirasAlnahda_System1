<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign_kpi extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function campaign()
    {
        return $this->belongsTo(
            Campaign::class
        );
    }
    public function indicators()
    {
        return $this->belongsToMany(
            Indicator::class,
            'goal_indicators',
            'campaign_kpi_id',
            'indicator_id'
        )
            ->withPivot([
                'score',
                'ranking',
                'approval_status',
        'target_value'
            ]);

    }    public function goalIndicators()
    {
        return $this->hasMany(
            GoalIndicator::class,
            'campaign_kpi_id'
        );
    }}
