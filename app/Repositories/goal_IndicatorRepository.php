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

    public function updateTargetValue(
        int $goalId,
        int $indicatorId,
        float $targetValue
    ): bool {

        return goalIndicator::query()

            ->where('campaign_kpi_id', $goalId)

            ->where('indicator_id', $indicatorId)

            ->update([
                'target_value' => $targetValue
            ]);
    }
    public function findByIds(
        int $goalId,
        int $indicatorId
    ): ?GoalIndicator {

        return goalIndicator::query()

            ->where('campaign_kpi_id', $goalId)

            ->where('indicator_id', $indicatorId)

            ->first();
    }
}
