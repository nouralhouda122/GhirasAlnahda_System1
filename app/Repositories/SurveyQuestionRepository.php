<?php

namespace App\Repositories;

use App\Models\Question;
use App\Models\SurveyQuestion;
use Illuminate\Support\Facades\DB;

class SurveyQuestionRepository
{

    /**
     * تحديث الترتيب داخل جدول survey_questions
     */
    public function updateSurveyPivot(int $surveyId, int $questionId, int $order): int
    {
        return DB::table('survey_questions')
            ->where('survey_id', $surveyId)
            ->where('question_id', $questionId)
            ->update([
                'order'      => $order,
                'updated_at' => now()
            ]);
    }    /**
     * تحديث المؤشر والمرحلة داخل جدول indicator_survey_question
     */
    public function updateIndicatorPivot(int $questionId, array $data): int
    {
        // إضافة توقيت التحديث تلقائياً للمصفوفة القادمة
        $data['updated_at'] = now();

        return DB::table('indicator_survey_question')
            ->where('question_id', $questionId)
            ->update($data);
    }    public function questionExists(
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
    public function create(array $data): Question
    {
        return Question::create($data);
    }

    /**
     * جلب سؤال محدد بواسطة الـ ID
     */
    public function find($questionId)
    {
        return Question::find($questionId);
    }

    /**
     * تحديث بيانات سؤال
     */
    public function update(int $questionId, array $data): bool
    {
        return DB::table('questions')
            ->where('id', $questionId)
            ->update($data);
    }    public function attachToSurvey(int $surveyId, int $questionId, int $order = 1): bool
    {
        return DB::table('survey_questions')->insert([
            'survey_id'   => $surveyId,
            'question_id' => $questionId,
            'order'       => $order,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }

    /**
     * ربط السؤال بالمؤشر والمرحلة في جدول indicator_survey_question
     */
    public function attachToIndicator(int $indicatorId, int $questionId, string $phase): bool
    {
        return DB::table('indicator_survey_question')->insert([
            'indicator_id' => $indicatorId,
            'question_id'  => $questionId,
            'phase'        => $phase,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);
    }
    public function deleteQuestion(int $questionId): bool
    {
        return DB::table('questions')
            ->where('id', $questionId)
            ->delete();
    }

    /**
     * فك ارتباط السؤال بالاستبيان (حذفه من جدول survey_questions)
     */
    public function deleteSurveyPivot(int $surveyId, int $questionId): int
    {
        return DB::table('survey_questions')
            ->where('survey_id', $surveyId)
            ->where('question_id', $questionId)
            ->delete();
    }

    /**
     * فك ارتباط السؤال بالمؤشر (حذفه من جدول indicator_survey_question)
     */
    public function deleteIndicatorPivot(int $questionId): int
    {
        return DB::table('indicator_survey_question')
            ->where('question_id', $questionId)
            ->delete();
    }
}
