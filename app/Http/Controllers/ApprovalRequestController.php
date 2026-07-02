<?php
namespace App\Http\Controllers;
use App\Helpers\ResponseHelper;
use App\Http\Requests\ApprovalStatusRequest;
use App\Http\Requests\AttendanceRequest;
use App\Http\Requests\UpdateCampanigRequest;
use App\Services\ApprovalRequestService;
use App\Services\AttendanceService;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;

class ApprovalRequestController
{
    protected $attendanceService;
    public function __construct(ApprovalRequestService $approvalRequestService)
    {
        $this->approvalRequestService = $approvalRequestService;
    }
    public function showAll( )
    {
        $data=$this->approvalRequestService->show();
        if($data['code']===200){
            return ResponseHelper::Success($data['user'], $data['message'], $data['code']);
        } else {
            return ResponseHelper::Error($data['user'], $data['message'], $data['code']);
        }
    }
    public function updateStatus(ApprovalStatusRequest $request,$id)
    {
        $data=$this->approvalRequestService->updateStatus($id,$request);
        if($data['code']===200){
            return ResponseHelper::Success($data['user'], $data['message'], $data['code']);
        } else {
            return ResponseHelper::Error($data['user'], $data['message'], $data['code']);
        }
    }
    public function indexDetail($id)
    {
        $data=$this->approvalRequestService->indexDetail($id);
        if($data['code']===200){
            return ResponseHelper::Success($data['user'], $data['message'], $data['code']);
        } else {
            return ResponseHelper::Error($data['user'], $data['message'], $data['code']);
        }
    }



}
