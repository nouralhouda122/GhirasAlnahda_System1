<?php

namespace App\Services;

use App\Repositories\CampaignReportRepository;
use Carbon\Carbon;

class CampaignReportService
{
    public function __construct(
        private CampaignReportRepository $repository
    ) {}

    /**
     * إنشاء التقرير الكامل للحملة
     */
    public function generateReport(
        int $campaignId
    ): array {

        /*
        |--------------------------------------------------------------------------
        | جلب الحملة
        |--------------------------------------------------------------------------
        */

        $campaign =
            $this->repository
                ->getCampaignForReport(
                    $campaignId
                );

        if (!$campaign) {

            return [
                'code' => 404,

                'data' => null,

                'message' =>
                    'Campaign not found',
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | البيانات الأساسية
        |--------------------------------------------------------------------------
        */

        $campaignData =
            $this->repository
                ->getCampaignInformation(
                    $campaign
                );

        /*
        |--------------------------------------------------------------------------
        | Financial
        |--------------------------------------------------------------------------
        */

        $financial =
            $this->repository
                ->getFinancialPerformance(
                    $campaign
                );

        /*
        |--------------------------------------------------------------------------
        | Volunteers
        |--------------------------------------------------------------------------
        */

        $volunteers =
            $this->repository
                ->getVolunteerPerformance(
                    $campaign
                );

        /*
        |--------------------------------------------------------------------------
        | Goals
        |--------------------------------------------------------------------------
        */

        $goals =
            $this->repository
                ->getGoals(
                    $campaign
                );

        /*
        |--------------------------------------------------------------------------
        | Executive Summary
        |--------------------------------------------------------------------------
        */

        $executiveSummary =
            $this->repository
                ->getExecutiveSummary(
                    $goals
                );

        /*
        |--------------------------------------------------------------------------
        | Impact
        |--------------------------------------------------------------------------
        */

        $impact =
            $this->repository
                ->getImpact(
                    $goals
                );

        /*
        |--------------------------------------------------------------------------
        | Trend
        |--------------------------------------------------------------------------
        */

        $trend =
            $this->repository
                ->getTrend(
                    $campaign,
                    $goals
                );

        /*
        |--------------------------------------------------------------------------
        | Recommendations
        |--------------------------------------------------------------------------
        */

        $recommendations =
            $this->repository
                ->generateRecommendations(
                    $campaign,
                    $volunteers,
                    $goals,
                    $financial
                );

        /*
        |--------------------------------------------------------------------------
        | التقرير النهائي
        |--------------------------------------------------------------------------
        */

        return [

            'code' => 200,

            'data' => [

                'report_date' =>
                    Carbon::now()
                        ->format('Y-m-d'),

                'campaign' =>
                    $campaignData,

                'executive_summary' => [

                    'summary' =>
                        $executiveSummary,

                    'impact' =>
                        $impact,

                ],

                'financial' =>
                    $financial,

                'volunteers' =>
                    $volunteers,

                'goals' =>
                    $goals,

                'trend' =>
                    $trend,

                'recommendations' =>
                    $recommendations,
            ],

            'message' =>
                'Campaign report generated successfully',
        ];
    }
}
