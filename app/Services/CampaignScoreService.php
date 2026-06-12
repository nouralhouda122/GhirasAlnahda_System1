<?php

namespace App\Services;

use App\Models\Campaign_kpi;

class CampaignScoreService
{
    public function __construct(
        private GoalScoreService $goalScoreService
    ) {}

    public function calculate(int $campaignId): float
    {
        $goals = Campaign_kpi::where('campaign_id', $campaignId)->get();

        $total = 0;
        $weightSum = 0;

        foreach ($goals as $goal) {

            $score = $this->goalScoreService->calculate(
                $goal,
                $campaignId
            );

            $weight = $goal->weight ?? 1;

            $total += $score * $weight;
            $weightSum += $weight;
        }

        return $weightSum
            ? round($total / $weightSum, 2)
            : 0;
    }
}
