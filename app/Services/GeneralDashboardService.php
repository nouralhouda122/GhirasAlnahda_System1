<?php

namespace App\Services;

use App\Repositories\GeneralDashboardRepository;
use Carbon\Carbon;
use InvalidArgumentException;

class GeneralDashboardService
{
    public function __construct(
        private GeneralDashboardRepository $generalDashboardRepository
    ) {}
    /**
     * بيانات الصفحة الرئيسية للـ Dashboard
     */
    public function getOverview(): array
    {
        return [
            'volunteers' =>
                $this->generalDashboardRepository->getLatestVolunteers(5),

            'campaigns' =>
                $this->generalDashboardRepository->getLatestCampaigns(5),

            'approval_requests' =>
                $this->generalDashboardRepository->getLatestApprovalRequests(5),
        ];
    }
    /**
     * الحصول على مؤشرات لوحة التحكم الرئيسية
     */
    public function getKpis(
        string $period = 'monthly'
    ): array {

        /*
        |--------------------------------------------------------------------------
        | Current Period
        |--------------------------------------------------------------------------
        */

        [$currentStart, $currentEnd] =
            $this->getPeriod($period);


        /*
        |--------------------------------------------------------------------------
        | Previous Period
        |--------------------------------------------------------------------------
        */

        [$previousStart, $previousEnd] =
            $this->getPreviousPeriod($period);


        /*
        |--------------------------------------------------------------------------
        | Donations
        |--------------------------------------------------------------------------
        */

        $currentDonations =
            $this->generalDashboardRepository
                ->getTotalDonations(
                    $currentStart,
                    $currentEnd
                );

        $previousDonations =
            $this->generalDashboardRepository
                ->getTotalDonations(
                    $previousStart,
                    $previousEnd
                );


        /*
        |--------------------------------------------------------------------------
        | Volunteers
        |--------------------------------------------------------------------------
        */

        $currentVolunteers =
            $this->generalDashboardRepository
                ->getTotalVolunteers(
                    $currentStart,
                    $currentEnd
                );

        $previousVolunteers =
            $this->generalDashboardRepository
                ->getTotalVolunteers(
                    $previousStart,
                    $previousEnd
                );


        /*
        |--------------------------------------------------------------------------
        | Active Campaigns
        |--------------------------------------------------------------------------
        */

        $currentActiveCampaigns =
            $this->generalDashboardRepository
                ->getActiveCampaigns();


        $previousActiveCampaigns =
            $this->generalDashboardRepository
                ->getActiveCampaignsByPeriod(
                    $previousStart,
                    $previousEnd
                );


        /*
        |--------------------------------------------------------------------------
        | Completed Campaigns
        |--------------------------------------------------------------------------
        */

        $currentCompletedCampaigns =
            $this->generalDashboardRepository
                ->getCompletedCampaigns(
                    $currentStart,
                    $currentEnd
                );

        $previousCompletedCampaigns =
            $this->generalDashboardRepository
                ->getCompletedCampaigns(
                    $previousStart,
                    $previousEnd
                );


        /*
        |--------------------------------------------------------------------------
        | Return KPIs
        |--------------------------------------------------------------------------
        */

        return [

            'period' => $period,

            'donations' => [
                'value' => round(
                    $currentDonations,
                    2
                ),

                'growth' => $this->calculateGrowth(
                    $currentDonations,
                    $previousDonations
                ),

                'growth_direction' =>
                    $this->getGrowthDirection(
                        $currentDonations,
                        $previousDonations
                    ),
            ],


            'volunteers' => [
                'value' => $currentVolunteers,

                'growth' => $this->calculateGrowth(
                    $currentVolunteers,
                    $previousVolunteers
                ),

                'growth_direction' =>
                    $this->getGrowthDirection(
                        $currentVolunteers,
                        $previousVolunteers
                    ),
            ],


            'active_campaigns' => [
                'value' => $currentActiveCampaigns,

                'growth' => $this->calculateGrowth(
                    $currentActiveCampaigns,
                    $previousActiveCampaigns
                ),

                'growth_direction' =>
                    $this->getGrowthDirection(
                        $currentActiveCampaigns,
                        $previousActiveCampaigns
                    ),
            ],


            'completed_campaigns' => [
                'value' => $currentCompletedCampaigns,

                'growth' => $this->calculateGrowth(
                    $currentCompletedCampaigns,
                    $previousCompletedCampaigns
                ),

                'growth_direction' =>
                    $this->getGrowthDirection(
                        $currentCompletedCampaigns,
                        $previousCompletedCampaigns
                    ),
            ],
        ];
    }


    /**
     * تحديد الفترة الحالية
     */
    private function getPeriod(
        string $period
    ): array {

        return match ($period) {

            'weekly' => [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek(),
            ],

            'monthly' => [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth(),
            ],

            'yearly' => [
                Carbon::now()->startOfYear(),
                Carbon::now()->endOfYear(),
            ],

            default => throw new InvalidArgumentException(
                'Invalid period. Use weekly, monthly or yearly.'
            ),
        };
    }


    /**
     * تحديد الفترة السابقة للمقارنة
     */
    private function getPreviousPeriod(
        string $period
    ): array {

        return match ($period) {

            'weekly' => [
                Carbon::now()
                    ->subWeek()
                    ->startOfWeek(),

                Carbon::now()
                    ->subWeek()
                    ->endOfWeek(),
            ],

            'monthly' => [
                Carbon::now()
                    ->subMonth()
                    ->startOfMonth(),

                Carbon::now()
                    ->subMonth()
                    ->endOfMonth(),
            ],

            'yearly' => [
                Carbon::now()
                    ->subYear()
                    ->startOfYear(),

                Carbon::now()
                    ->subYear()
                    ->endOfYear(),
            ],

            default => throw new InvalidArgumentException(
                'Invalid period.'
            ),
        };
    }


    /**
     * حساب نسبة النمو
     */
    private function calculateGrowth(
        float|int $current,
        float|int $previous
    ): float {

        if ($previous == 0) {

            return $current > 0
                ? 100
                : 0;
        }

        return round(
            (
                ($current - $previous)
                / $previous
            ) * 100,
            2
        );
    }


    /**
     * تحديد اتجاه النمو
     */
    private function getGrowthDirection(
        float|int $current,
        float|int $previous
    ): string {

        return match (true) {

            $current > $previous => 'up',

            $current < $previous => 'down',

            default => 'stable',
        };
    }/**
 * الإحصائيات التفصيلية للـDashboard
 */
    public function getStatistics(string $period): array
    {
        [$start, $end] = $this->getStatisticsPeriod($period);

        return [
            'period' => $period,

            'volunteers' =>
                $this->generalDashboardRepository
                    ->getVolunteersStatistics(
                        $start,
                        $end,
                        $period
                    ),

            'campaigns' =>
                $this->generalDashboardRepository
                    ->getCampaignsStatistics(
                        $start,
                        $end,
                        $period
                    ),

            'donations' =>
                $this->generalDashboardRepository
                    ->getDonationsStatistics(
                        $start,
                        $end,
                        $period
                    ),
        ];
    }private function getStatisticsPeriod(
    string $period
): array {

    return match ($period) {

        'weekly' => [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth(),
        ],

        'monthly' => [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth(),
        ],

        'yearly' => [
            Carbon::now()->startOfYear(),
            Carbon::now()->endOfYear(),
        ],

        default => [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth(),
        ],
    };
}}
