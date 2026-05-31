<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Question extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function indicators()
    {
        return $this->belongsToMany(
            Indicator::class,
            'indicator_survey_question',
            'question_id',
            'indicator_id'
        )
            ->withPivot('phase')
            ->withTimestamps();
    }
    public function surveyQuestions()
{
    return $this->hasMany(
        SurveyQuestion::class
    );
}}
