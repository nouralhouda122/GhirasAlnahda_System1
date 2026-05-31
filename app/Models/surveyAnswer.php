<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SurveyAnswer extends Model
{
    use HasFactory;

    protected $guarded = [];

    /*
    |-----------------------------------------
    | الاستبيان
    |-----------------------------------------
    */
    public function survey()
    {
        return $this->belongsTo(
            Survey::class
        );
    }

    /*
    |-----------------------------------------
    | السؤال
    |-----------------------------------------
    */
    public function question()
    {
        return $this->belongsTo(
            SurveyQuestion::class,
            'survey_question_id'
        );
    }

    /*
    |-----------------------------------------
    | المستخدم
    |-----------------------------------------
    */
    public function user()
    {
        return $this->belongsTo(
            User::class
        );
    }
}
