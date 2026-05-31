<?php

namespace App\Services;

use App\Http\Resources\CampaignSurveyResource;
use App\Repositories\CampaignSurveyRepository;
use App\Repositories\CampaingRepository;

class CampaignSurveyService
{
    private CampaignSurveyRepository $CampaignSurveyRepository;
    private CampaingRepository    $CampaignRepository;

    public function __construct(
        CampaignSurveyRepository $CampaignSurveyRepository,
        CampaingRepository    $CampaignRepository,
    ) {
        $this->CampaignSurveyRepository = $CampaignSurveyRepository;
        $this->CampaignRepository = $CampaignRepository;
    }

    public function showByStage($campaignId, $stage)
    {
        $campaign = $this->CampaignRepository->getById($campaignId);

        if (!$campaign) {
            return ['data' => '', 'message' => 'Campaign not found', 'code' => 404];
        }

        $survey = $this->CampaignSurveyRepository->get($campaignId, $stage);

        if (!$survey) {
            return ['data' => '', 'message' => 'Survey not found', 'code' => 404];
        }

        if ($survey->surveyQuestions->isEmpty()) {
            return ['data' => [], 'message' => 'No questions found', 'code' => 200];
        }

        return [
            'data' => new CampaignSurveyResource($survey),
            'message' => 'success',
            'code' => 200
        ];
    }

    public function addQuestionToSurvey($surevyId, \App\Http\Requests\addQuestionToSurveyRequest $request)
    {
    }

    public function updateQuestionToSurvey($surevyId, $questionId, \App\Http\Controllers\updateQuestionToSurveyRequest $request)
    {
    }

    public function DeleteQuestionToSurvey($surevyId, $questionId)
    {
    }
}
