<?php


namespace App\Repositories;


use App\Models\surveyQuestion;

class SurveyQuestionRepository
{
    public function questionExists(
        $surveyId,
        $questionText
    )
    {
        return SurveyQuestion::where(
            'survey_id',
            $surveyId
        )
            ->where(
                'question_text',
                $questionText
            )
            ->exists();
    }

    public function createSurveyQuestion(
        $surveyId,
        $question
    )
    {
        return SurveyQuestion::create([

            'survey_id' => $surveyId,

            'question_text' =>
                $question->question_text,

            'type' => $question->type,

            'scale' => $question->scale,

            'order' => $question->order,
        ]);
    }

}
