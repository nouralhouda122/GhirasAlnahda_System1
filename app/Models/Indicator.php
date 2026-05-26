<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Indicator extends Model
{
    protected $fillable = [
        'name',
        'description',
        'domain',
        'campaign_type',
        'operation',
        'table_name',
        'column_name',
        'formula',
        'needs_survey',
        'is_computable',
        'base_weight',
        'priority',
        'tags'
    ];

    protected $casts = [
        'tags' => 'array',
        'needs_survey' => 'boolean',
        'is_computable' => 'boolean',

    ];
    public function goals()
    {
        return $this->belongsToMany(
            \App\Models\Campaign_kpi::class,
            'goal_indicator',
            'indicator_id',
            'campaign_kpi_id'
        );
    }
}
