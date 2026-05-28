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

    public function indicators()
    {
        return $this->belongsToMany(
            \App\Models\Indicator::class,
            'goal_indicators',
            'campaign_kpi_id',
            'indicator_id'
        )->withPivot('id', 'score', 'approval_status'); // جلب بيانات الجدول الوسيط
    }

    public function goalIndicators()
    {
        return $this->hasMany(
            GoalIndicator::class, // تعديل حالة الأحرف لاسم الموديل
            'campaign_kpi_id'
        );
    }
}
