<?php

namespace App\Repositories;

use App\Models\Survey;
use App\Models\Campaign_kpi;
use App\Models\SurveyQuestion;

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
    public function get($campaignId,$stage)
    {
        return Survey::with([
            'questions'
        ])
            ->where('campaign_id', $campaignId)
            ->where('stage', $stage)
            ->first();

    }
}
