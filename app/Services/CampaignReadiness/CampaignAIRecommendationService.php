<?php

namespace App\Services\CampaignReadiness;

use App\Models\Campaign;

class CampaignAIRecommendationService
{
    public function __construct(
        private CampaignReadinessService $readinessService
    ) {}

    public function generatePreLaunchRecommendations(
        Campaign $campaign
    ): array {

        $readiness = $this->readinessService->evaluate($campaign);

        return [
            'overall_score' => $readiness['overall_score'],
            'overall_status' => $readiness['overall_status'],
            'recommendations' => [],
        ];
    }

    public function generatePostLaunchRecommendations(
        Campaign $campaign
    ): array {

        return [
            'recommendations' => [],
        ];
    }
}
