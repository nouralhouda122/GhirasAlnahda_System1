<?php

namespace App\Repositories;

use App\Models\CampaignEvaluation;

class CampaignEvaluationRepository
{
    public function create(array $data): CampaignEvaluation
    {
        return CampaignEvaluation::create($data);
    }

    public function getBaseline(int $campaignId): ?CampaignEvaluation
    {
        return CampaignEvaluation::query()
            ->where('campaign_id', $campaignId)
            ->where('phase', 'before')
            ->orderBy('evaluated_at')
            ->first();
    }

    public function getLatestEvaluation(int $campaignId): ?CampaignEvaluation
    {
        return CampaignEvaluation::query()
            ->where('campaign_id', $campaignId)
            ->latest('evaluated_at')
            ->first();
    }

    public function getLatestMonthlyEvaluation(
        int $campaignId
    ): ?CampaignEvaluation {

        return CampaignEvaluation::query()
            ->where('campaign_id', $campaignId)
            ->where('phase', 'monthly')
            ->latest('evaluated_at')
            ->first();
    }

    public function getPreviousMonthlyEvaluation(
        int $campaignId
    ): ?CampaignEvaluation {

        return CampaignEvaluation::query()
            ->where('campaign_id', $campaignId)
            ->where('phase', 'monthly')
            ->orderByDesc('evaluated_at')
            ->skip(1)
            ->first();
    }

    public function getTrend(int $campaignId)
    {
        return CampaignEvaluation::query()
            ->where('campaign_id', $campaignId)
            ->orderBy('evaluated_at')
            ->get();
    }
}
