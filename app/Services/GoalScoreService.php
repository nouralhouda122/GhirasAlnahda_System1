<?php

namespace App\Services;

use App\Models\Campaign_kpi;

class GoalScoreService
{
    public function __construct(
        private IndicatorScoreService $indicatorScoreService
    ) {}

    public function calculate(
        Campaign_kpi $goal,
        int $campaignId
    ): float {

        $goal->loadMissing('indicators');

        $total = 0;
        $weightSum = 0;

        foreach ($goal->indicators as $indicator) {

            $matchScore = $indicator->pivot->score ?? 0;

            if ($matchScore <= 0) {
                continue;
            }

            $indicatorScore = $this->indicatorScoreService->calculate(
                $indicator,
                $campaignId
            );

            $total += $indicatorScore * $matchScore;
            $weightSum += $matchScore;
        }

        return $weightSum
            ? round($total / $weightSum, 2)
            : 0;
    }
}
