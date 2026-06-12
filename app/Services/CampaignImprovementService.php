<?php

namespace App\Services;

use App\Repositories\CampaignEvaluationRepository;

class CampaignImprovementService
{
    public function __construct(
        private CampaignEvaluationRepository $campaignEvaluationRepository,
        private CampaignScoreService $campaignScoreService
    ) {}


    public function calculate(int $campaignId): array
    {
        $baseline = $this->campaignEvaluationRepository
            ->getBaseline($campaignId);

        if (!$baseline) {
            return [
                'value' => 0,
                'baseline_score' => null,
                'current_score' => null,
                'status' => 'no_baseline'
            ];
        }

        $currentScore = $this->campaignScoreService
            ->calculate($campaignId);

        $impact = $currentScore - $baseline->score;

        return [
            'value' => round($impact, 2),

            'baseline_score' => (float) $baseline->score,

            'current_score' => round($currentScore, 2),

            'status' => $this->getStatus($impact),
        ];
    }

    private function getStatus(float $impact): string
    {
        return match (true) {

            $impact >= 20 => 'excellent',

            $impact >= 10 => 'good',

            $impact >= 0 => 'stable',

            default => 'negative',
        };
    }
}
