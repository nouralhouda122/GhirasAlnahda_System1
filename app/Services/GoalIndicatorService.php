<?php

namespace App\Services;

use App\Http\Resources\GoalIndicatorResource;
use App\Http\Resources\IndicatorMatchResource;
use App\Repositories\CampaingRepository;
use App\Repositories\GoalIndicatorRepository;
use Illuminate\Http\Resources\Json\JsonResource;

class GoalIndicatorService
{
    protected $goalIndicatorRepository;
    protected $CampaignRepository;

    public function __construct(
        GoalIndicatorRepository $goalIndicatorRepository,
        CampaingRepository $CampaignRepository,
    ) {
        $this->goalIndicatorRepository = $goalIndicatorRepository;
        $this->CampaignRepository = $CampaignRepository;

    }
    public function index($id)
    {
        $campanig = $this->CampaignRepository->getById($id);
        if (!$campanig) {
            return [
                'data' => '',
                'message' => 'Campaign not found',
                'code' => 404];
        }
        $goals = $this->goalIndicatorRepository
            ->getAllGoalsWithIndicators($campanig->id);
        if ($goals->isEmpty()) {
            return [
                'data' => [],
                'message' => 'No goals found',
                'code' => 200
            ];
        }

        return [
            'data' => GoalIndicatorResource::collection($goals),
            'message' => 'success',
            'code' => 200
        ];
    }


    public function show($goalId)
    {
        // جلب الهدف مع مؤشراته عبر الـ Eager Loading
        $goal = $this->goalIndicatorRepository
            ->getGoalWithIndicators($goalId); // تأكد أن الـ repository يجلب علاقة indicators

        if (!$goal) {
            return [
                'data' => '',
                'message' => 'Goal not found',
                'code' => 404
            ];
        }

        if ($goal->indicators->isEmpty()) {
            return [
                'data' => [],
                'message' => 'No indicators found for this goal',
                'code' => 200
            ];
        }

        // التعديل هنا: نرسل مجموعة المؤشرات (collection) وليس الهدف نفسه
        return [
            'data' => IndicatorMatchResource::collection($goal->indicators),
            'message' => 'success',
            'code' => 200
        ];
    }    /*
    |--------------------------------------------------------------------------
    | UPDATE INDICATOR STATUS
    |--------------------------------------------------------------------------
    */

    public function updateStatus($request, $id)
    {
        $goalIndicator = $this->goalIndicatorRepository
            ->findGoalIndicator($id);

        if (!$goalIndicator) {

            return [
                'data' => '',
                'message' => 'Goal indicator not found',
                'code' => 404
            ];
        }

        $updated = $this->goalIndicatorRepository
            ->updateStatus(
                $goalIndicator,
                [
                    'approval_status' => $request['approval_status'],
                    'approved_by_monitor' => auth()->id(),
                    'approved_at' => now(),
                ]
            );

        return [
            'data' => new GoalIndicatorResource($updated),
            'message' => 'Indicator status updated successfully',
            'code' => 200
        ];
    }
}
