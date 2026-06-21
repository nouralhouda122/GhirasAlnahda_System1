<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\SurveyQuestion;
class Survey extends Model
{
    use HasFactory;

    protected $guarded = [];


    public function campaign()
    {
        return $this->belongsTo(
            Campaign::class
        );
    }

    public function surveyQuestions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(
            SurveyQuestion::class,
            'survey_id'
        );
    }
    public function answers()
    {
        return $this->hasMany(
            SurveyAnswer::class
        );
    }
    public function evaluationTasks()
    {
        return $this->hasMany(EvaluationTask::class);
    }
}
