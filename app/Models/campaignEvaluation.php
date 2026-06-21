<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class campaignEvaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'score',
        'phase',
        'evaluated_at',
    ];

    protected $casts = [
        'evaluated_at' => 'datetime',
        'score' => 'decimal:2',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
}
