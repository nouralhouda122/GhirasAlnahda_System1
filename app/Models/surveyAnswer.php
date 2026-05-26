<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class surveyAnswer extends Model
{
    protected $fillable = [
        'survey_id',
        'survey_question_id',
        'user_id',
        'answer'
    ];

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    public function question()
    {
        return $this->belongsTo(SurveyQuestion::class, 'survey_question_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
