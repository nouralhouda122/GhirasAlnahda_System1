<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class surveyQuestion extends Model
{
    protected $fillable = [
        'survey_id',
        'question_text',
        'type',
        'scale',
        'order'
    ];

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    public function answers()
    {
        return $this->hasMany(surveyAnswer::class);
    }
}
