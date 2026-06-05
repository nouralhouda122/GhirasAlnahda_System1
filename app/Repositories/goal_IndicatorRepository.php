<?php


namespace App\Repositories;


use App\Models\goalIndicator;

class goal_IndicatorRepository
{

    public function updateStatus(array $data,$goal_indicator)
    {
        $goal_indicator->update($data);

    }

    public function getById($goal_id, $indicator_id)
    {
        return goalIndicator::where('indicator_id',$indicator_id)->where('campaign_kpi_id',$goal_id)->first();
    }
}
