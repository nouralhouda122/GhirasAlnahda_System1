<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoalIndicator extends Model
{
    use HasFactory;
    protected $guarded=[];
    public function indicator()
    {
        return $this->belongsTo(
            Indicator::class,
            'indicator_id'
        );
    }public function goal()
{
    return $this->belongsTo(
Campaign_kpi::class,
        'campaign_kpi_id'
    );
}
}
