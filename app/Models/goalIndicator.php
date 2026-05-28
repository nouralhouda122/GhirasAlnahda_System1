<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class goalIndicator extends Model
{
    use HasFactory;
    protected $guarded=[];
    public function indicator()
    {
        return $this->belongsTo(
            Indicator::class
        );
    }public function goal()
{
    return $this->belongsTo(
Campaign_kpi::class,
        'campaign_kpi_id'
    );
}
}
