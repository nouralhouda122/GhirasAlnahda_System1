<?php

namespace App\Services;

use App\Models\Indicator;
use App\Models\SurveyAnswer;
use App\Models\SurveyQuestion;

class KpiCalculationService
{
    public function calculate(
        Indicator $indicator,
        int $campaignId
    ): array {

        $trend = $this->calculateTrend(
            $indicator,
            $campaignId
        );

        $score = round(
            collect($trend)->avg(),
            2
        );

        return [

            'indicator_id' => $indicator->id,

            'indicator_name' => $indicator->name,

            'score' => $score,

            'achievement' => $this->achievement(
                $score,
                $indicator->target_value ?? 100
            ),

            'status' => $this->status(
                $score
            ),

            'direction' => $this->direction(
                $trend
            ),

            'trend' => $trend
        ];
    }

    private function calculateTrend(
        Indicator $indicator,
        int $campaignId
    ): array {

        return [

            'before' => $this->phaseScore(
                $indicator,
                $campaignId,
                'before'
            ),

            'during' => $this->phaseScore(
                $indicator,
                $campaignId,
                'during'
            ),

            'after' => $this->phaseScore(
                $indicator,
                $campaignId,
                'after'
            )
        ];
    }

    private function phaseScore(
        Indicator $indicator,
        int $campaignId,
        string $phase
    ): float {

        $questionIds = $indicator
            ->questions()
            ->wherePivot('phase', $phase)
            ->pluck('questions.id');

        if ($questionIds->isEmpty()) {
            return 0;
        }

        $surveyQuestionIds = SurveyQuestion::query()

            ->whereIn(
                'question_id',
                $questionIds
            )

            ->whereHas(
                'survey',
                function ($q) use (
                    $campaignId,
                    $phase
                ) {

                    $q->where(
                        'campaign_id',
                        $campaignId
                    )
                        ->where(
                            'stage',
                            $phase
                        );
                }
            )

            ->pluck('id');

        if ($surveyQuestionIds->isEmpty()) {
            return 0;
        }

        $average = SurveyAnswer::query()

            ->whereIn(
                'survey_question_id',
                $surveyQuestionIds
            )

            ->avg('answer');

        if (!$average) {
            return 0;
        }

        return round(
            ($average / 5) * 100,
            2
        );
    }

    private function achievement(
        float $score,
        float $target
    ): float {

        if ($target <= 0) {
            return $score;
        }

        return round(
            min(
                ($score / $target) * 100,
                100
            ),
            2
        );
    }

    private function status(
        float $score
    ): string {

        return match (true) {

            $score >= 80 => 'excellent',

            $score >= 60 => 'good',

            $score >= 40 => 'warning',

            default => 'critical'
        };
    }

    private function direction(
        array $trend
    ): string {

        if (
            $trend['after']
            >
            $trend['before']
        ) {
            return 'up';
        }

        if (
            $trend['after']
            <
            $trend['before']
        ) {
            return 'down';
        }

        return 'stable';
    }
}
