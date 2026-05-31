<?php
namespace App\Http\Controllers;
use App\Helpers\ResponseHelper;
use App\Http\Requests\SurveyStageRequest;
use App\Services\CampaignSurveyService;
class CampaignSurveyController extends Controller
{
    private CampaignSurveyService $campaignSurveyService;
    public function __construct(
        CampaignSurveyService $campaignSurveyService) {
        $this->campaignSurveyService = $campaignSurveyService;}
    public function show($campaignId,SurveyStageRequest $phase): \Illuminate\Http\JsonResponse
    {
        $data = $this->campaignSurveyService->showByStage($campaignId, $phase);
        if ($data['code'] === 200) {
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
