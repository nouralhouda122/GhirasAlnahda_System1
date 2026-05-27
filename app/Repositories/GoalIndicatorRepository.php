<?php

namespace App\Repositories;

use App\Models\Campaign_kpi;
use App\Models\GoalIndicator;

class GoalIndicatorRepository
{

    /*
    |--------------------------------------------------------------------------
    | GET ALL GOALS WITH INDICATORS
    |--------------------------------------------------------------------------
    */

    public function getAllGoalsWithIndicators($campanig_id)
    {
        return Campaign_kpi::with([
            'goalIndicators.indicator'
        ])
            ->where('campaign_id',$campanig_id)->get();
    }

    /*
    |--------------------------------------------------------------------------
    | GET SINGLE GOAL WITH INDICATORS
    |--------------------------------------------------------------------------
    */

    public function getGoalWithIndicators($goalId)
    {
        return Campaign_kpi::with([
            'goalIndicators.indicator'
        ])
            ->find($goalId);
    }

    /*
    |--------------------------------------------------------------------------
    | FIND GOAL INDICATOR
    |--------------------------------------------------------------------------
    */

    public function findGoalIndicator($id)
    {
        return GoalIndicator::with([
            'indicator',
            'goal'
        ])->find($id);
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE INDICATOR STATUS
    |--------------------------------------------------------------------------
    */

    public function updateStatus($goalIndicator, array $data)
    {
        $goalIndicator->update($data);

        return $goalIndicator->fresh([
            'indicator',
            'goal'
        ]);
    }
}
