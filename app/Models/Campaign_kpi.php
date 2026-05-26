<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign_kpi extends Model
{
    use HasFactory;
    protected $guarded=[];
    public function Campaign(){
        return $this->belongsTo(Campaign::class);
    }
    public function indicators()
    {
        return $this->belongsToMany(
            \App\Models\Indicator::class,
            'goal_indicator',
            'campaign_kpi_id',
            'indicator_id'
        );
    }

}
