<?php

namespace App\Repositories;

use App\Models\ApprovalRequest;
use App\Models\Campaign;
use App\Models\VolunteerProfile;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class GeneralDashboardRepository
{
    /**
     * آخر 5 متطوعين
     */
    public function getLatestVolunteers(int $limit = 5): array
    {
        return VolunteerProfile::query()
            ->with('user:id,name')
            ->latest('created_at')
            ->limit($limit)
            ->get()
            ->map(function ($volunteer) {

                return [
                    'id' => $volunteer->id,

                    'name' => $volunteer->user?->name,

                    'gender' => $volunteer->gender,

                    'preferred_sector' =>
                        $volunteer->preferred_sector,

                    'preferred_field' =>
                        $volunteer->preferred_field,

                    'created_at' =>
                        $volunteer->created_at?->format('Y-m-d H:i:s'),
                ];

            })
            ->toArray();
    }


    /**
     * آخر 5 حملات
     */
    public function getLatestCampaigns(int $limit = 5): array
    {
        return Campaign::query()
            ->latest('created_at')
            ->limit($limit)
            ->get([
                'id',
                'title',
                'status',
                'type',
                'location',
                'current_amount',
                'target_amount',
                'current_volunteers',
                'required_volunteers',
                'created_at',
            ])
            ->map(function ($campaign) {

                return [
                    'id' => $campaign->id,

                    'title' => $campaign->title,

                    'status' => $campaign->status,

                    'type' => $campaign->type,

                    'location' => $campaign->location,

                    'donations' =>
                        (float) $campaign->current_amount,

                    'target_amount' =>
                        (float) $campaign->target_amount,

                    'volunteers' => [
                        'current' =>
                            (int) $campaign->current_volunteers,

                        'required' =>
                            (int) $campaign->required_volunteers,
                    ],

                    'created_at' =>
                        $campaign->created_at?->format('Y-m-d H:i:s'),
                ];

            })
            ->toArray();
    }


    /**
     * آخر 5 طلبات موافقة
     *
     * الطلب يمكن أن يكون لحملة أو دورة
     * أو أي Model يستخدم MorphTo.
     */
    public function getLatestApprovalRequests(
        int $limit = 5
    ): array {

        return ApprovalRequest::query()
            ->with([
                'approvable',
                'requestedBy:id,name',
            ])
            ->latest('created_at')
            ->limit($limit)
            ->get()
            ->map(function ($request) {

                $approvable = $request->approvable;

                return [
                    'id' => $request->id,

                    'type' => $request->type,

                    'status' => $request->status,

                    'notes' => $request->notes,

                    'requested_by' => [
                        'id' => $request->requestedBy?->id,
                        'name' => $request->requestedBy?->name,
                    ],

                    'approvable' => [
                        'id' => $approvable?->id,

                        'type' => $approvable
                            ? class_basename($approvable)
                            : null,

                        'title' => $approvable?->title,
                    ],

                    'created_at' => $request->created_at
                        ?->format('Y-m-d H:i:s'),
                ];

            })
            ->toArray();
    }
    /*
    |--------------------------------------------------------------------------
    | Dashboard KPIs
    |--------------------------------------------------------------------------
    */

    /**
     * إجمالي التبرعات خلال فترة معينة
     */
    public function getTotalDonations(
        Carbon $start,
        Carbon $end
    ): float {

        return (float) Campaign::query()
            ->whereBetween('created_at', [$start, $end])
            ->sum('current_amount');
    }

    /**
     * عدد المتطوعين خلال فترة معينة
     */
    public function getTotalVolunteers(
        Carbon $start,
        Carbon $end
    ): int {

        return VolunteerProfile::query()
            ->whereBetween('created_at', [$start, $end])
            ->count();
    }

    /**
     * عدد الحملات النشطة حاليًا
     */
    public function getActiveCampaigns(): int
    {
        return Campaign::query()
            ->where('status', 'ongoing')
            ->count();
    }

    /**
     * عدد الحملات المنجزة خلال فترة معينة
     */
    public function getCompletedCampaigns(
        Carbon $start,
        Carbon $end
    ): int {

        return Campaign::query()
            ->where('status', 'completed')
            ->whereBetween('updated_at', [$start, $end])
            ->count();
    }

    /**
     * عدد الحملات النشطة التي تم إنشاؤها خلال فترة معينة
     */
    public function getActiveCampaignsByPeriod(
        Carbon $start,
        Carbon $end
    ): int {

        return Campaign::query()
            ->where('status', 'ongoing')
            ->whereBetween('created_at', [$start, $end])
            ->count();
    }


    /*
    |--------------------------------------------------------------------------
    | Detailed Statistics
    |--------------------------------------------------------------------------
    */

    /**
     * إحصائيات المتطوعين
     *
     * weekly  = أسابيع الشهر الحالي
     * monthly = أيام الشهر الحالي
     * yearly  = أشهر السنة الحالية
     */
    public function getVolunteersStatistics(
        Carbon $start,
        Carbon $end,
        string $period
    ): array {

        return match ($period) {

            'weekly' => $this->getWeeklyVolunteersStatistics(
                $start,
                $end
            ),

            'monthly' => $this->getMonthlyVolunteersStatistics(
                $start,
                $end
            ),

            'yearly' => $this->getYearlyVolunteersStatistics(
                $start,
                $end
            ),

            default => [],
        };
    }


    /**
     * إحصائيات الحملات
     */
    public function getCampaignsStatistics(
        Carbon $start,
        Carbon $end,
        string $period
    ): array {

        return match ($period) {

            'weekly' => $this->getWeeklyCampaignsStatistics(
                $start,
                $end
            ),

            'monthly' => $this->getMonthlyCampaignsStatistics(
                $start,
                $end
            ),

            'yearly' => $this->getYearlyCampaignsStatistics(
                $start,
                $end
            ),

            default => [],
        };
    }


    /**
     * إحصائيات التبرعات
     */
    public function getDonationsStatistics(
        Carbon $start,
        Carbon $end,
        string $period
    ): array {

        return match ($period) {

            'weekly' => $this->getWeeklyDonationsStatistics(
                $start,
                $end
            ),

            'monthly' => $this->getMonthlyDonationsStatistics(
                $start,
                $end
            ),

            'yearly' => $this->getYearlyDonationsStatistics(
                $start,
                $end
            ),

            default => [],
        };
    }


    /*
    |--------------------------------------------------------------------------
    | Weekly Statistics
    |--------------------------------------------------------------------------
    */

    /**
     * المتطوعون - أسبوعي
     *
     * الأسبوع هنا داخل الشهر الحالي.
     */
    private function getWeeklyVolunteersStatistics(
        Carbon $start,
        Carbon $end
    ): array {

        $weeks = $this->getMonthWeeks($start);

        return collect($weeks)
            ->map(function ($week) {

                $value = VolunteerProfile::query()
                    ->whereBetween(
                        'created_at',
                        [
                            $week['start']->copy()->startOfDay(),
                            $week['end']->copy()->endOfDay()
                        ]
                    )
                    ->count();

                return [
                    'label' => $week['label'],
                    'value' => (int) $value,
                ];
            })
            ->values()
            ->toArray();
    }


    /**
     * الحملات - أسبوعي
     */
    private function getWeeklyCampaignsStatistics(
        Carbon $start,
        Carbon $end
    ): array {

        $weeks = $this->getMonthWeeks($start);

        return collect($weeks)
            ->map(function ($week) {

                $value = Campaign::query()
                    ->whereBetween(
                        'created_at',
                        [
                            $week['start']->copy()->startOfDay(),
                            $week['end']->copy()->endOfDay()
                        ]
                    )
                    ->count();

                return [
                    'label' => $week['label'],
                    'value' => (int) $value,
                ];
            })
            ->values()
            ->toArray();
    }


    /**
     * التبرعات - أسبوعي
     *
     * ملاحظة:
     * التبرعات محسوبة من current_amount في campaigns.
     */
    private function getWeeklyDonationsStatistics(
        Carbon $start,
        Carbon $end
    ): array {

        $weeks = $this->getMonthWeeks($start);

        return collect($weeks)
            ->map(function ($week) {

                $value = Campaign::query()
                    ->whereBetween(
                        'created_at',
                        [
                            $week['start']->copy()->startOfDay(),
                            $week['end']->copy()->endOfDay()
                        ]
                    )
                    ->sum('current_amount');

                return [
                    'label' => $week['label'],
                    'value' => (float) $value,
                ];
            })
            ->values()
            ->toArray();
    }


    /*
    |--------------------------------------------------------------------------
    | Monthly Statistics
    |--------------------------------------------------------------------------
    */

    /**
     * المتطوعون - شهري
     *
     * كل يوم من الشهر الحالي.
     */
    private function getMonthlyVolunteersStatistics(
        Carbon $start,
        Carbon $end
    ): array {

        $days = CarbonPeriod::create(
            $start->copy()->startOfDay(),
            $end->copy()->startOfDay()
        );

        return collect($days)
            ->map(function (Carbon $day) {

                $dayStart = $day->copy()->startOfDay();
                $dayEnd = $day->copy()->endOfDay();

                $value = VolunteerProfile::query()
                    ->whereBetween(
                        'created_at',
                        [$dayStart, $dayEnd]
                    )
                    ->count();

                return [
                    'label' => $day->format('Y-m-d'),
                    'value' => (int) $value,
                ];
            })
            ->values()
            ->toArray();
    }


    /**
     * الحملات - شهري
     */
    private function getMonthlyCampaignsStatistics(
        Carbon $start,
        Carbon $end
    ): array {

        $days = CarbonPeriod::create(
            $start->copy()->startOfDay(),
            $end->copy()->startOfDay()
        );

        return collect($days)
            ->map(function (Carbon $day) {

                $dayStart = $day->copy()->startOfDay();
                $dayEnd = $day->copy()->endOfDay();

                $value = Campaign::query()
                    ->whereBetween(
                        'created_at',
                        [$dayStart, $dayEnd]
                    )
                    ->count();

                return [
                    'label' => $day->format('Y-m-d'),
                    'value' => (int) $value,
                ];
            })
            ->values()
            ->toArray();
    }


    /**
     * التبرعات - شهري
     */
    private function getMonthlyDonationsStatistics(
        Carbon $start,
        Carbon $end
    ): array {

        $days = CarbonPeriod::create(
            $start->copy()->startOfDay(),
            $end->copy()->startOfDay()
        );

        return collect($days)
            ->map(function (Carbon $day) {

                $dayStart = $day->copy()->startOfDay();
                $dayEnd = $day->copy()->endOfDay();

                $value = Campaign::query()
                    ->whereBetween(
                        'created_at',
                        [$dayStart, $dayEnd]
                    )
                    ->sum('current_amount');

                return [
                    'label' => $day->format('Y-m-d'),
                    'value' => (float) $value,
                ];
            })
            ->values()
            ->toArray();
    }


    /*
    |--------------------------------------------------------------------------
    | Yearly Statistics
    |--------------------------------------------------------------------------
    */

    /**
     * المتطوعون - سنوي
     *
     * كل أشهر السنة الحالية.
     */
    private function getYearlyVolunteersStatistics(
        Carbon $start,
        Carbon $end
    ): array {

        $months = $this->getYearMonths($start);

        return collect($months)
            ->map(function (Carbon $month) {

                $monthStart = $month->copy()->startOfMonth();
                $monthEnd = $month->copy()->endOfMonth();

                $value = VolunteerProfile::query()
                    ->whereBetween(
                        'created_at',
                        [$monthStart, $monthEnd]
                    )
                    ->count();

                return [
                    'label' => $month->format('Y-m'),
                    'value' => (int) $value,
                ];
            })
            ->values()
            ->toArray();
    }


    /**
     * الحملات - سنوي
     */
    private function getYearlyCampaignsStatistics(
        Carbon $start,
        Carbon $end
    ): array {

        $months = $this->getYearMonths($start);

        return collect($months)
            ->map(function (Carbon $month) {

                $monthStart = $month->copy()->startOfMonth();
                $monthEnd = $month->copy()->endOfMonth();

                $value = Campaign::query()
                    ->whereBetween(
                        'created_at',
                        [$monthStart, $monthEnd]
                    )
                    ->count();

                return [
                    'label' => $month->format('Y-m'),
                    'value' => (int) $value,
                ];
            })
            ->values()
            ->toArray();
    }


    /**
     * التبرعات - سنوي
     */
    private function getYearlyDonationsStatistics(
        Carbon $start,
        Carbon $end
    ): array {

        $months = $this->getYearMonths($start);

        return collect($months)
            ->map(function (Carbon $month) {

                $monthStart = $month->copy()->startOfMonth();
                $monthEnd = $month->copy()->endOfMonth();

                $value = Campaign::query()
                    ->whereBetween(
                        'created_at',
                        [$monthStart, $monthEnd]
                    )
                    ->sum('current_amount');

                return [
                    'label' => $month->format('Y-m'),
                    'value' => (float) $value,
                ];
            })
            ->values()
            ->toArray();
    }


    /*
    |--------------------------------------------------------------------------
    | Period Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * إنشاء أسابيع الشهر الحالي.
     *
     * مثال:
     *
     * 2026-08-01 - 2026-08-02
     * 2026-08-03 - 2026-08-09
     * 2026-08-10 - 2026-08-16
     * 2026-08-17 - 2026-08-23
     * 2026-08-24 - 2026-08-30
     * 2026-08-31 - 2026-08-31
     */
    private function getMonthWeeks(
        Carbon $date
    ): array {

        $monthStart = $date->copy()->startOfMonth();
        $monthEnd = $date->copy()->endOfMonth();

        $weeks = [];

        $currentStart = $monthStart->copy();

        while ($currentStart->lte($monthEnd)) {

            /*
             * الأسبوع يبدأ يوم الاثنين
             */
            if ($currentStart->isSameDay($monthStart)) {

                $currentEnd = $currentStart
                    ->copy()
                    ->endOfWeek(Carbon::SUNDAY);

            } else {

                $currentEnd = $currentStart
                    ->copy()
                    ->endOfWeek(Carbon::SUNDAY);
            }

            /*
             * لا نخرج خارج الشهر
             */
            if ($currentEnd->gt($monthEnd)) {
                $currentEnd = $monthEnd->copy();
            }

            $weeks[] = [
                'start' => $currentStart->copy(),
                'end' => $currentEnd->copy(),
                'label' =>
                    $currentStart->format('Y-m-d')
                    . ' - ' .
                    $currentEnd->format('Y-m-d'),
            ];

            $currentStart = $currentEnd
                ->copy()
                ->addDay();
        }

        return $weeks;
    }


    /**
     * إنشاء أشهر السنة.
     */
    private function getYearMonths(
        Carbon $date
    ): array {

        $yearStart = $date->copy()->startOfYear();

        $months = [];

        for ($i = 0; $i < 12; $i++) {

            $months[] = $yearStart
                ->copy()
                ->addMonths($i);
        }

        return $months;
    }
}
