<?php
namespace App\Http\Controllers;
use App\Helpers\ResponseHelper;
use App\Http\Requests\addQuestionToSurveyRequest;
use App\Http\Requests\SurveyStageRequest;
use App\Services\CampaignSurveyService;
class CampaignSurveyController extends Controller
{
    private CampaignSurveyService $campaignSurveyService;
    public function __construct(
        CampaignSurveyService $campaignSurveyService) {
        $this->campaignSurveyService = $campaignSurveyService;}
    public function show($campaignId,SurveyStageRequest $request): \Illuminate\Http\JsonResponse
    {
        $data = $this->campaignSurveyService->showByStage(
            $campaignId,
            $request->stage
        );        if ($data['code'] === 200) {
            return ResponseHelper::Success(
                $data['data'],
                $data['message'],
                $data['code']
            );}
        return ResponseHelper::Error(
            $data['data'],
            $data['message'],
            $data['code']
        );
    }
     ////اضافة سؤال لاستبيان //////
    public function addQuestionToSurvey($surevyId,addQuestionToSurveyRequest $request): \Illuminate\Http\JsonResponse
    {
        $data = $this->campaignSurveyService->addQuestionToSurvey(
            $surevyId,
            $request
        );        if ($data['code'] === 200) {
        return ResponseHelper::Success(
            $data['data'],
            $data['message'],
            $data['code']
        );}
        return ResponseHelper::Error(
            $data['data'],
            $data['message'],
            $data['code']
        );
    }

    //تعديل سؤال ب استبيان
    public function updateQuestionToSurvey($surevyId,$questionId,updateQuestionToSurveyRequest $request): \Illuminate\Http\JsonResponse
    {
        $data = $this->campaignSurveyService->updateQuestionToSurvey(
            $surevyId,
            $questionId,
            $request
        );        if ($data['code'] === 200) {
        return ResponseHelper::Success(
            $data['data'],
            $data['message'],
            $data['code']
        );}
        return ResponseHelper::Error(
            $data['data'],
            $data['message'],
            $data['code']
        );
    }

    // حذف سؤال من استبيان
    public function DeleteQuestionToSurvey($surevyId,$questionId): \Illuminate\Http\JsonResponse
    {
        $data = $this->campaignSurveyService->DeleteQuestionToSurvey(
            $surevyId,
            $questionId
        );        if ($data['code'] === 200) {
        return ResponseHelper::Success(
            $data['data'],
            $data['message'],
            $data['code']
        );}
        return ResponseHelper::Error(
            $data['data'],
            $data['message'],
            $data['code']
        );
    }

    //اعتماد استبيان
    public function approvalSurvey($campaignId,SurveyStageRequest $request): \Illuminate\Http\JsonResponse
    {
        $data = $this->campaignSurveyService->showByStage(
            $campaignId,
            $request->stage
        );        if ($data['code'] === 200) {
        return ResponseHelper::Success(
            $data['data'],
            $data['message'],
            $data['code']
        );}
        return ResponseHelper::Error(
            $data['data'],
            $data['message'],
            $data['code']
        );
    }

}
