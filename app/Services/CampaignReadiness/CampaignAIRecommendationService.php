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
            ],
        ];
    }
}
