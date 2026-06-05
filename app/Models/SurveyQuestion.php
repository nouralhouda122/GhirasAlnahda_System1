<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SurveyQuestion extends Model
{
    use HasFactory;

    protected $guarded = [];

    // العلاقة العكسية مع الاستبيان
    public function survey()
    {
        return $this->belongsTo(Survey::class, 'survey_id');
    }

    // 🛑 العلاقة الأساسية للوصول لبيانات السؤال (تأكد من وجودها هكذا)
    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id', 'id');
    }
}
