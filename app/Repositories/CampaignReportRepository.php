<?php

namespace App\Repositories;
use Carbon\Carbon;
use App\Models\Campaign;

class CampaignReportRepository
{
    /**
     * جلب الحملة مع جميع البيانات المطلوبة للتقرير
     */
    public function getCampaignForReport(int $campaignId): ?Campaign
    {
        return Campaign::query()
            ->with([
                'kpis.goalIndicators.indicator',
                'volunteerProfiles',
                'attendances',
            ])
            ->find($campaignId);
    }

    /**
     * معلومات الحملة الأساسية
     */
    public function getCampaignInformation(
        Campaign $campaign
    ): array {

        return [
            'id' => $campaign->id,

            'title' => $campaign->title,

            'type' => $campaign->type,

            'location' => $campaign->location,

            'leader_id' => $campaign->leader_id,

            'start_date' => $campaign->start_date
                ? Carbon::parse($campaign->start_date)->format('Y-m-d')
                : null,

            'end_date' => $campaign->end_date
                ? Carbon::parse($campaign->end_date)->format('Y-m-d')
                : null,

            'status' => $campaign->status,

            'priority' => $campaign->priority,
        ];
    }
    /**
     * الأداء المالي
     *
     * لا يوجد donors / average_donation
     * لأنه لا يوجد جدول تبرعات مستقل.
     */
    public function getFinancialPerformance(
        Campaign $campaign
    ): array {

        $target = (float) $campaign->target_amount;

        $collected = (float) $campaign->current_amount;

        $remaining = max(
            $target - $collected,
            0
        );

        $achievementPercentage = $target > 0
            ? min(
                round(
                    ($collected / $target) * 100,
                    2
                ),
                100
            )
            : 0;

        return [
            'target' => $target,

            'collected' => $collected,

            'remaining' => $remaining,

            'achievement_percentage' =>
                $achievementPercentage,
        ];
    }

    /**
     * أداء المتطوعين
     */
    public function getVolunteerPerformance(
        Campaign $campaign
    ): array {

        $required =
            (int) $campaign->required_volunteers;

        $registered =
            $campaign->volunteerProfiles->count();

        $active =
            $campaign->attendances
                ->where('is_active_session', true)
                ->count();

        $attendance =
            $campaign->attendances
                ->whereNotNull('check_in_time')
                ->count();

        $hours =
            (int) $campaign->attendances
                ->sum('hours');

        $coveragePercentage = $required > 0
            ? round(
                ($registered / $required) * 100,
                2
            )
            : 100;

        return [
            'required' => $required,

            'registered' => $registered,

            'active' => $active,

            'attendance' => $attendance,

            'hours' => $hours,

            'coverage_percentage' =>
                $coveragePercentage,
        ];
    }

    /**
     * أهداف الحملة ومؤشراتها
     */
    public function getGoals(
        Campaign $campaign
    ): array {

        return $campaign->kpis
            ->map(function ($goal) {

                $indicators =
                    $goal->goalIndicators;

                $totalIndicators =
                    $indicators->count();

                /*
                 * إذا لم يوجد مؤشرات
                 */
                if ($totalIndicators === 0) {

                    $progress = 0;
                } else {

                    $progress = round(
                        $indicators->avg(
                            fn ($indicator) =>
                            (float) $indicator->score
                        ),
                        2
                    );
                }

                /*
                 * تحديد الحالة
                 */
                $status = match (true) {

                    $progress >= 80 =>
                    'excellent',

                    $progress >= 70 =>
                    'good',

                    $progress >= 50 =>
                    'warning',

                    default =>
                    'critical',
                };

                /*
                 * المؤشرات
                 */
                $indicatorData =
                    $indicators
                        ->map(function ($goalIndicator) {

                            return [
                                'id' =>
                                    $goalIndicator
                                        ->indicator
                                        ?->id,

                                'name' =>
                                    $goalIndicator
                                        ->indicator
                                        ?->name,

                                'score' =>
                                    round(
                                        (float)
                                        $goalIndicator->score,
                                        2
                                    ),

                                'status' =>
                                    match (true) {

                                        $goalIndicator->score >= 80 =>
                                        'excellent',

                                        $goalIndicator->score >= 70 =>
                                        'good',

                                        $goalIndicator->score >= 50 =>
                                        'warning',

                                        default =>
                                        'critical',
                                    },
                            ];
                        })
                        ->values()
                        ->toArray();

                return [
                    'id' => $goal->id,

                    'title' =>
                        $goal->goal_text,

                    'progress' =>
                        $progress,

                    'status' =>
                        $status,

                    'weight' =>
                        (float) $goal->weight,

                    'trend_indicator' =>
                        'stable',

                    'indicators' =>
                        $indicatorData,
                ];
            })
            ->values()
            ->toArray();
    }

    /**
     * الملخص التنفيذي
     */
    public function getExecutiveSummary(
        array $goals
    ): array {

        $totalGoals =
            count($goals);

        $achievedGoals =
            collect($goals)
                ->whereIn(
                    'status',
                    ['excellent', 'good']
                )
                ->count();

        $atRiskGoals =
            collect($goals)
                ->whereIn(
                    'status',
                    ['warning', 'critical']
                )
                ->count();

        $campaignProgress =
            $totalGoals > 0
                ? round(
                collect($goals)
                    ->avg('progress'),
                2
            )
                : 0;

        return [
            'campaign_progress' =>
                $campaignProgress,

            'achieved_goals' => [
                'current' =>
                    $achievedGoals,

                'total' =>
                    $totalGoals,
            ],

            'at_risk_goals' => [
                'current' =>
                    $atRiskGoals,

                'total' =>
                    $totalGoals,
            ],
        ];
    }

    /**
     * التأثير الحالي
     */
    public function getImpact(
        array $goals
    ): array {

        $currentScore =
            count($goals) > 0
                ? round(
                collect($goals)
                    ->avg('progress'),
                2
            )
                : 0;

        return [
            'value' =>
                $currentScore,

            'baseline_score' =>
                0,

            'current_score' =>
                $currentScore,

            'status' =>
                match (true) {

                    $currentScore >= 80 =>
                    'good',

                    $currentScore >= 50 =>
                    'warning',

                    default =>
                    'critical',
                },
        ];
    }

    /**
     * Trend
     */
    public function getTrend(
        Campaign $campaign,
        array $goals
    ): array {

        if (!$campaign->created_at) {
            return [];
        }

        $score =
            count($goals) > 0
                ? round(
                collect($goals)
                    ->avg('progress'),
                2
            )
                : 0;

        return [
            [
                'date' =>
                    $campaign->created_at
                        ->format('Y-m'),

                'score' =>
                    $score,

                'phase' =>
                    null,
            ],
        ];
    }

    /**
     * التوصيات الديناميكية
     */
    public function generateRecommendations(
        Campaign $campaign,
        array $volunteers,
        array $goals,
        array $financial
    ): array {

        $recommendations = [];

        /*
        |--------------------------------------------------------------------------
        | 1. المتطوعون
        |--------------------------------------------------------------------------
        */

        if (
            $volunteers['required'] > 0 &&
            $volunteers['coverage_percentage'] < 70
        ) {

            $recommendations[] = [

                'type' => 'volunteers',

                'priority' => 'high',

                'title' =>
                    'زيادة استقطاب المتطوعين',

                'description' =>
                    'نسبة تغطية المتطوعين أقل من المستوى المطلوب.',

                'current' =>
                    $volunteers['registered'],

                'required' =>
                    $volunteers['required'],

                'coverage_percentage' =>
                    $volunteers['coverage_percentage'],

                'action' => [
                    'type' =>
                        'recruit_volunteers',

                    'campaign_id' =>
                        $campaign->id,
                ],
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | 2. الأهداف المعرضة للخطر
        |--------------------------------------------------------------------------
        */

        $atRiskGoals =
            collect($goals)
                ->whereIn(
                    'status',
                    ['warning', 'critical']
                );

        if ($atRiskGoals->isNotEmpty()) {

            $recommendations[] = [

                'type' => 'goals',

                'priority' => 'high',

                'title' =>
                    'مراجعة الأهداف المعرضة للخطر',

                'description' =>
                    'يوجد أهداف لم تحقق المستوى المطلوب.',

                'count' =>
                    $atRiskGoals->count(),

                'action' => [
                    'type' =>
                        'review_goals',

                    'campaign_id' =>
                        $campaign->id,
                ],
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | 3. الحضور
        |--------------------------------------------------------------------------
        */

        if (
            $volunteers['registered'] > 0 &&
            $volunteers['attendance'] === 0
        ) {

            $recommendations[] = [

                'type' => 'attendance',

                'priority' => 'medium',

                'title' =>
                    'تحسين حضور المتطوعين',

                'description' =>
                    'لم يتم تسجيل حضور فعلي للمتطوعين في الحملة.',

                'attendance' =>
                    0,

                'action' => [
                    'type' =>
                        'attendance_review',

                    'campaign_id' =>
                        $campaign->id,
                ],
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | 4. التمويل
        |--------------------------------------------------------------------------
        */

        if (
            $financial['target'] > 0 &&
            $financial['achievement_percentage'] < 50
        ) {

            $recommendations[] = [

                'type' => 'financial',

                'priority' => 'high',

                'title' =>
                    'تعزيز التمويل',

                'description' =>
                    'المبلغ المحقق أقل من 50% من الهدف المالي.',

                'target' =>
                    $financial['target'],

                'collected' =>
                    $financial['collected'],

                'achievement_percentage' =>
                    $financial['achievement_percentage'],

                'action' => [
                    'type' =>
                        'review_fundraising',

                    'campaign_id' =>
                        $campaign->id,
                ],
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | 5. لا توجد مشاكل
        |--------------------------------------------------------------------------
        */

        if (empty($recommendations)) {

            $recommendations[] = [

                'type' => 'performance',

                'priority' => 'low',

                'title' =>
                    'أداء الحملة ضمن المستوى المطلوب',

                'description' =>
                    'لم يتم اكتشاف مؤشرات تستدعي إجراءً تصحيحيًا حاليًا.',

                'action' => [
                    'type' =>
                        'continue_monitoring',

                    'campaign_id' =>
                        $campaign->id,
                ],
            ];
        }

        return $recommendations;
    }
}
