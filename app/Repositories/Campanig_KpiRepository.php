<?php

namespace App\Repositories;

use App\Models\Campaign_kpi;
use App\Models\GoalIndicator;

class Campanig_KpiRepository
{
    public function getAllGoalsWithIndicators($campanig_id)
    {
        return Campaign_kpi::with([
            'goalIndicators.indicator'
        ])
            ->where('campaign_id',$campanig_id)->get();
    }
    public function getCampaignGoals(
        int $campaignId
    )
    {
        return Campaign_kpi::with('indicators')
            ->where(
                'campaign_id',
                $campaignId
            )
            ->get();
    }

    public function findGoalWithIndicators($goalId)
    {
        return Campaign_kpi::with([
            'goalIndicators.indicator'
        ])
            ->find($goalId);
    }
    public function getGoalWithIndicatorsAndQuestion($campaignId)
    {
       return   Campaign_kpi::with([
            'goalIndicators.indicator.questions'
        ])
            ->where('campaign_id', $campaignId)
            ->get();
    }
}
