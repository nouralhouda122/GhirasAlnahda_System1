<?php


namespace App\Http\Controllers;
use App\Helpers\ResponseHelper;
use App\Services\PointTransactionService;
use Illuminate\Support\Facades\Auth;

class PointTransactionController
{
    protected $CourseService;

    public function __construct(PointTransactionService $pointTransactionService)
    {
        $this->pointTransactionService = $pointTransactionService;
    }
    //عرض سجل نقاط مستخدم
    public function showPointForUser()
    {
        $data=$this->pointTransactionService->index(Auth::user());
        if($data['code']===200){
            return ResponseHelper::Success($data['user'], $data['message'], $data['code']);
        } else {
            return ResponseHelper::Error($data['user'], $data['message'], $data['code']);
        }
    }
//عرض سجل نقاط متطوع ما
    public function showPointForVolunteer($user_id)
    {
        $data=$this->pointTransactionService->show($user_id);
        if($data['code']===200){
            return ResponseHelper::Success($data['user'], $data['message'], $data['code']);
        } else {
            return ResponseHelper::Error($data['user'], $data['message'], $data['code']);
        }
    }

}
