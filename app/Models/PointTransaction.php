<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointTransaction extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function volunteer()
    {
        return $this->belongsTo(User::class, 'volunteer_id');
    }
    public function awardedBy()
    {
        return $this->belongsTo(User::class, 'awarded_by');
    }
    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

}
