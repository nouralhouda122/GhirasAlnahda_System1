<?php

namespace App\Services\CampaignReadiness;

use App\Models\Campaign;

class CampaignAIRecommendationService
{
    /**
     * توصيات قبل إطلاق الحملة
     */
    public function generatePreLaunchRecommendations(
        Campaign $campaign
    ): array {

        // هنا لاحقاً نربط:
        // Campaign details
        // + CampaignReadinessService
        // + CampaignRecommendationService
        // + AI

        return [
            'overall_score' => 96,

            'overall_status' => 'Needs Improvement',

            'recommendations' => [
                [
                    'priority' => 'high',
                    'area' => 'Resources',
                    'issue' => 'The campaign is missing 15 required volunteers.',
                    'recommendation' => 'Recruit 15 additional volunteers before launching the campaign.',
                    'reason' => 'The current volunteer capacity is below the required level and may affect campaign implementation.',
                ],

                [
                    'priority' => 'medium',
                    'area' => 'Resources',
                    'issue' => 'Volunteer capacity is currently below the campaign requirement.',
                    'recommendation' => 'Monitor volunteer recruitment progress regularly until the required capacity is reached.',
                    'reason' => 'Continuous monitoring will help ensure that the campaign reaches the required capacity before launch.',
                ],
            ],
        ];
    }

    /**
     * توصيات بعد إطلاق الحملة
     */
    public function generatePostLaunchRecommendations(
        Campaign $campaign
    ): array {

        // هنا ستكون البيانات مختلفة عن مرحلة ما قبل الإطلاق.
        //
        // مثلاً:
        // - نسبة تحقيق الهدف
        // - عدد المتطوعين الحالي
        // - المبلغ المحقق
        // - تقدم الحملة
        // - مؤشرات الأداء
        // - التأخر عن الخطة
        // - نتائج التقييم

        return [
            'recommendations' => [

                [
                    'priority' => 'high',
                    'area' => 'Performance',
                    'issue' => 'Campaign progress is below the expected target.',
                    'recommendation' => 'Review campaign activities and increase implementation efforts.',
                    'reason' => 'The current performance indicates that the campaign may not achieve its planned target within the available timeline.',
                ],

                [
                    'priority' => 'high',
                    'area' => 'Goals',
                    'issue' => 'One or more campaign goals are significantly below the expected achievement level.',
                    'recommendation' => 'Identify the main causes of underperformance and adjust the implementation plan.',
                    'reason' => 'Low goal achievement may indicate that current activities are not producing the expected results.',
                ],

                [
                    'priority' => 'medium',
                    'area' => 'Resources',
                    'issue' => 'Available resources are being utilized below the expected level.',
                    'recommendation' => 'Review resource allocation and ensure that available resources are assigned to priority activities.',
                    'reason' => 'Improving resource utilization can increase campaign efficiency and support better results.',
                ],

                [
                    'priority' => 'medium',
                    'area' => 'Volunteers',
                    'issue' => 'Volunteer participation is lower than expected.',
                    'recommendation' => 'Increase volunteer engagement and follow up with inactive volunteers.',
                    'reason' => 'Low volunteer participation may reduce the campaign capacity to implement planned activities.',
                ],

                [
                    'priority' => 'medium',
                    'area' => 'Timeline',
                    'issue' => 'Campaign activities are progressing slower than planned.',
                    'recommendation' => 'Review delayed activities and establish corrective deadlines for the implementation team.',
                    'reason' => 'Delays may prevent the campaign from achieving its targets within the planned timeframe.',
                ],

                [
                    'priority' => 'low',
                    'area' => 'Monitoring',
                    'issue' => 'Campaign performance requires more frequent monitoring.',
                    'recommendation' => 'Increase the frequency of progress reviews and update performance indicators regularly.',
                    'reason' => 'Regular monitoring helps detect performance gaps early and supports timely corrective actions.',
                ],

            ],
        ];}}
