<?php
namespace App\Http\Controllers;
use App\Helpers\ResponseHelper;
use App\Http\Requests\DepartmentRequest;
use App\Http\Requests\SetGoalIndicatorTargetRequest;
use App\Http\Requests\SetGoalWeightRequest;
use App\Http\Requests\updateStatusIndicatorRequest;
use App\Services\DepartmentService;
use App\Services\GoalIndicatorService;
class MonitoringGoalController
{
    public function __construct(GoalIndicatorService $goalIndicatorService)
    {
        $this->goalIndicatorService = $goalIndicatorService;
    }
// عرض كل الاهداف
    public function index($id )
    {
        $data = $this->goalIndicatorService->index($id);
        if ($data['code'] === 200) {
            return ResponseHelper::Success($data['data'], $data['message'], $data['code']);
        } else {
            return ResponseHelper::Error($data['data'], $data['message'], $data['code']);
        }
    }
//عرض مؤشرات هدف معين
    public function show( $id)
    {
        $data = $this->goalIndicatorService->show($id);

        if ($data['code'] === 200) {
            return ResponseHelper::Success($data['data'], $data['message'], $data['code']);
        } else {
            return ResponseHelper::Error($data['data'], $data['message'], $data['code']);
        }
    }
//تعديل حالة مؤشر (قبول او رفض)
    public function updateStatus(updateStatusIndicatorRequest $request,$goal_id,$indicator_id)
    {
        $data = $this->goalIndicatorService->updateStatus($request,$goal_id,$indicator_id);

        if ($data['code'] === 200) {
            return ResponseHelper::Success($data['data'], $data['message'], $data['code']);
        } else {
            return ResponseHelper::Error($data['data'], $data['message'], $data['code']);
        }
    }
//عرض  نتائج  مؤشرات الاهداف

    public function showResultIndicators($campaignId,$goal_id)
    {
        $data = $this->goalIndicatorService->details($campaignId,$goal_id);
        if ($data['code'] === 200) {
            return ResponseHelper::Success($data['data'], $data['message'], $data['code']);
        } else {
            return ResponseHelper::Error($data['data'], $data['message'], $data['code']);
        }
    }
// إضافة وزن للهدف
    public function setWeight(
        SetGoalWeightRequest $request,
    ) {
        $data = $this->goalIndicatorService->setWeight($request);

        if ($data['code'] === 200) {
            return ResponseHelper::Success(
                $data['data'],
                $data['message'],
                $data['code']
            );
        }

        return ResponseHelper::Error(
            $data['data'],
            $data['message'],
            $data['code']
        );
    }

// إضافة Target Value للمؤشر
    public function setTargetValue(
        SetGoalIndicatorTargetRequest $request,
    ) {
        $data = $this->goalIndicatorService->setTargetValue(
            $request     );

        if ($data['code'] === 200) {
            return ResponseHelper::Success(
                $data['data'],
                $data['message'],
                $data['code']
            );
        }

        return ResponseHelper::Error(
            $data['data'],
            $data['message'],
            $data['code']
        );
    }

}
