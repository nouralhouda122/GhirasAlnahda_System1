<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Services\CampaignReadiness\CampaignAIRecommendationService;
use Illuminate\Http\JsonResponse;

class CampaignAIRecommendationController extends Controller
{
    public function __construct(
        private CampaignAIRecommendationService $aiRecommendationService
    ) {}

    /**
     * توصيات الحملة قبل الإطلاق
     */
    public function preLaunchRecommendations(
        Campaign $campaign
    ): JsonResponse {

        $result = $this->aiRecommendationService
            ->generatePreLaunchRecommendations($campaign);

        return response()->json([
            'status' => 1,

            'data' => [
                'overall_score' =>
                    $result['overall_score'],

                'overall_status' =>
                    $result['overall_status'],

                'recommendations' =>
                    $result['recommendations'],
            ],
        ]);
    }

    /**
     * توصيات الحملة بعد الإطلاق
     */
    public function postLaunchRecommendations(
        Campaign $campaign
    ): JsonResponse {

        $result = $this->aiRecommendationService
            ->generatePostLaunchRecommendations($campaign);

        return response()->json([
            'status' => 1,

            'data' => [
                'recommendations' =>
                    $result['recommendations'],
            ],
        ]);
    }
}
