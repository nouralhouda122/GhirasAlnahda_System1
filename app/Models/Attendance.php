<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $guarded = [];

    protected $casts = [
        'check_in_time' => 'datetime:Y-m-d H:i:s',
        'check_out_time' => 'datetime:Y-m-d H:i:s',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function volunteer()
    {
        return $this->belongsTo(User::class, 'volunteer_id');
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
}
