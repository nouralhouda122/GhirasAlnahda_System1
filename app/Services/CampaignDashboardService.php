<?php

namespace App\Services;

use App\Repositories\CampaingRepository;
use App\Repositories\Campanig_KpiRepository;

class CampaignDashboardService
{
    public function __construct(
        private CampaingRepository $campaignRepository,
        private Campanig_KpiRepository $goalRepository,
        private GoalScoreService $goalScoreService,
        private CampaignScoreService $campaignScoreService,
        private StatusService $statusService,
        private CampaignImprovementService $campaignImpactService,
private CampaignEvaluationService $CampaignEvaluationService,
    ) {}

    public function dashboard(int $campaignId): array
    {
        $campaign = $this->campaignRepository->getById($campaignId);

        if (!$campaign) {
            return [
                'status' => 0,
                'message' => 'Campaign not found',
                'code' => 404
            ];
        }

        $goals = $this->goalRepository->getCampaignGoals($campaignId);
        $totalGoalsCount = $goals->count();

        $goalResults = [];
        $achievedCount = 0;
        $atRiskCount = 0;

        foreach ($goals as $goal) {
            $progress = $this->goalScoreService->calculate($goal, $campaignId);
            $status = $this->statusService->getStatus($progress);

            if ($progress >= 80) {
                $achievedCount++;
            } else {
                $atRiskCount++;
            }

            $goalResults[] = [
                'id' => $goal->id,
                'title' => $goal->goal_text,
                'progress' => round($progress, 2),
                'status' => $status,
                'weight' => $goal->weight,
                'trend_indicator' => $progress >= 60 ? 'up' : 'stable'
            ];
        }

        return [
            'status' => 1,
            'data' => [
                'summary' => [
                    'campaign_progress' => round($this->campaignScoreService->calculate($campaignId), 2),
                    'achieved_goals' => ['current' => $achievedCount, 'total' => $totalGoalsCount],
                    'at_risk_goals' => ['current' => $atRiskCount, 'total' => $totalGoalsCount],
                ],
                'impact' => $this->campaignImpactService->calculate($campaignId),
                'monthly_momentum' => [
                    'percentage_change' => 14.0,
                    'direction' => 'up'
                ],
                'trend' => $this->CampaignEvaluationService->getTrend($campaignId),
                'goals' => $goalResults,
            ],
            'message' => 'success',
            'code' => 200
        ];
    }}
