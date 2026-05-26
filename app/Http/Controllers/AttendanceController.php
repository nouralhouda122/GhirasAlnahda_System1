<?php


namespace App\Http\Controllers;


use App\Helpers\ResponseHelper;
use App\Http\Requests\AttendanceRequest;
use App\Http\Requests\CampaingRequest;
use App\Services\AttendanceService;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;

class AttendanceController
{
    protected $attendanceService;
    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }
    public function leaderCheckIn(AttendanceRequest $request,$id)
    {
        $data=$this->attendanceService->leaderCheckIn($request,$id);
        if($data['code']===200){
            return ResponseHelper::Success($data['user'], $data['message'], $data['code']);
        } else {
            return ResponseHelper::Error($data['user'], $data['message'], $data['code']);
        }
    }
    public function leaderCheckOut($id)
    {
        $data=$this->attendanceService->leaderCheckOut($id);
        if($data['code']===200){
            return ResponseHelper::Success($data['user'], $data['message'], $data['code']);
        } else {
            return ResponseHelper::Error($data['user'], $data['message'], $data['code']);
        }
    }
//عرض حضور مستخدم
    public function volunteerAttendances()
    {
        $data=$this->attendanceService->index(Auth::user());
        if($data['code']===200){
            return ResponseHelper::Success($data['user'], $data['message'], $data['code']);
        } else {
            return ResponseHelper::Error($data['user'], $data['message'], $data['code']);
        }
    }
//عرض حضور حملة
    public function campaignAttendances($Campanig_id)
    {
        $data=$this->attendanceService->show($Campanig_id);
        if($data['code']===200){
            return ResponseHelper::Success($data['user'], $data['message'], $data['code']);
        } else {
            return ResponseHelper::Error($data['user'], $data['message'], $data['code']);
        }
    }



}
