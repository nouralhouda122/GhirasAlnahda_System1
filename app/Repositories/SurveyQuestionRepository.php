<?php

namespace App\Repositories;

use App\Models\SurveyQuestion;

class SurveyQuestionRepository
{
    /**
     * هل السؤال موجود داخل الاستبيان؟
     */
    public function questionExists(
        $surveyId,
        $questionId
    )
    {
        return SurveyQuestion::where('survey_id', $surveyId)
            ->where('question_id', $questionId)
            ->exists();
    }

    /**
     * إنشاء ربط سؤال داخل استبيان
     */
    public function createSurveyQuestion(
        $surveyId,
        $questionId
    )
    {
        return SurveyQuestion::create([
            'survey_id' => $surveyId,
            'question_id' => $questionId,
        ]);
    }
}
