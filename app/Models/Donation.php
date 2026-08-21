<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use HasFactory;


  protected $guarded = [];
    protected $casts = [
        'amount' => 'decimal:2',
    ];

    // 1. علاقة التبرع بالمتطوع/المستخدم الذي تبرع
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 2. علاقة التبرع بالحملة المراد التبرع لها
    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
}
