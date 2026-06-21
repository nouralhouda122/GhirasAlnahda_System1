<?php

namespace App\Services;

use App\Models\Survey;
use App\Models\SurveyAnswer;
use App\Models\SurveyQuestion;
use Illuminate\Support\Facades\DB;

class SurveyScoringService
{
    public function __construct(
        private AnswerNormalizationService $normalizer
    ) {}

    public function calculateIndicatorScore(
        $indicator,
        int $campaignId
    ): float {

        $weights = [
            'before' => 0.2,
            'during' => 0.3,
            'after'  => 0.5,
        ];

        $total = 0;
        $weightSum = 0;

        foreach ($weights as $phase => $weight) {

            $score = $this->calculatePhaseScore(
                $indicator->id,
                $campaignId,
                $phase
            );

            $total += $score * $weight;
            $weightSum += $weight;
        }

        return $weightSum
            ? round($total / $weightSum, 2)
            : 0;
    }

    public function calculatePhaseScore(
        int $indicatorId,
        int $campaignId,
        string $phase
    ): float {

        $survey = Survey::where('campaign_id', $campaignId)
            ->where('stage', $phase)
            ->first();

        if (!$survey) return 0;

        $questionIds = DB::table('indicator_survey_question')
            ->where('indicator_id', $indicatorId)
            ->where('phase', $phase)
            ->pluck('question_id');

        if ($questionIds->isEmpty()) return 0;

        $surveyQuestionIds = SurveyQuestion::where('survey_id', $survey->id)
            ->whereIn('question_id', $questionIds)
            ->pluck('id');

        if ($surveyQuestionIds->isEmpty()) return 0;

        $answers = SurveyAnswer::with('surveyQuestion.question')
            ->whereIn('survey_question_id', $surveyQuestionIds)
            ->get();

        if ($answers->isEmpty()) return 0;

        $scores = $answers->map(function ($answer) {
            $question = $answer->surveyQuestion->question;

            if (!$question) return 0;

            return $this->normalizer->normalize(
                $answer->answer,
                $question
            );
        });

        return round($scores->avg(), 2);
    }
}
