<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Services\CampaignDashboardService;

class CampaignEvaluationController extends Controller
{
    public function __construct(
        private CampaignDashboardService $dashboardService
    ) {}

    public function dashboard(int $campaignId)
    {
        $data = $this->dashboardService
            ->dashboard($campaignId);

        if ($data['code'] === 200) {

            return ResponseHelper::Success(
                $data['data'],
                $data['message'],
                $data['code']
            );
        }

        return ResponseHelper::Error(
            [],
            $data['message'],
            $data['code']
        );
    }
}
