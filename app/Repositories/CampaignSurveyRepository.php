<?php

namespace App\Repositories;

use App\Models\Survey;
use App\Models\Campaign_kpi;
use App\Models\SurveyQuestion;
use Illuminate\Support\Facades\DB;

class CampaignSurveyRepository
{
    public function firstOrCreateSurvey(
        $campaignId,
        $stage,
        $title
    )
    {
        return Survey::firstOrCreate(
            [
                'campaign_id' => $campaignId,
                'stage' => $stage,
            ],
            [
                'title' => $title,
                'status' => 'draft',
            ]
        );
    }
    public function get($campaignId, $stage)
    {
        return Survey::with('surveyQuestions.question.indicators')
            ->where('campaign_id', $campaignId)
            ->where('stage', $stage)
            ->first();
    }
    public function getById($surevyId)
    {
        return Survey::with(['surveyQuestions'])->find($surevyId);
    }
    public function approveSurvey(int $surveyId): bool
    {
        return DB::table('surveys')
            ->where('id', $surveyId)
            ->update([
                'status' => 'active',
                'updated_at' => now()
            ]);
    }

    public function getByIdWithDetails(int $surveyId)
    {
        return Survey::with('surveyQuestions.question.indicators')
            ->where('id', $surveyId)
            ->first();
    }


}
