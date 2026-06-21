<?php

namespace App\Services;

use App\Repositories\CampaignEvaluationRepository;

class CampaignEvaluationService
{
    public function __construct(
        private CampaignEvaluationRepository $campaignEvaluationRepository
    ) {}

    public function save(
        int $campaignId,
        float $score,
        string $phase
    ): array {

        $evaluation = $this->campaignEvaluationRepository->create([
            'campaign_id' => $campaignId,
            'score' => $score,
            'phase' => $phase,
            'evaluated_at' => now(),
        ]);

        return [
            'data' => $evaluation,
            'message' => 'Campaign evaluation saved successfully',
            'code' => 201
        ];
    }

    public function getBaseline(
        int $campaignId
    ) {
        return $this->campaignEvaluationRepository
            ->getBaseline($campaignId);
    }

    /**
     * Get latest evaluation
     */
    public function getLatestEvaluation(
        int $campaignId
    ) {
        return $this->campaignEvaluationRepository
            ->getLatestEvaluation($campaignId);
    }

    /**
     * Get trend data
     */
    public function getTrend(int $campaignId): array
    {
        $evaluations = $this->campaignEvaluationRepository
            ->getTrend($campaignId);

        return $evaluations->map(function ($item) {

            return [
                'date' => $item->evaluated_at->format('Y-m'),
                'score' => (float) $item->score,
                'phase' => $item->phase,
            ];

        })->toArray();
    }
}
