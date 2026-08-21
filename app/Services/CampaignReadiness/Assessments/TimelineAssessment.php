<?php

namespace App\Services\CampaignReadiness\Assessments;

use App\DTOs\AssessmentResult;
use App\Models\Campaign;

class TimelineAssessment
{
    public function evaluate(Campaign $campaign): AssessmentResult
    {
        $strengths = [];
        $weaknesses = [];
        $score = 100;

        if (!$campaign->start_date) {
            $score -= 30;

            $weaknesses[] = 'Campaign start date is not defined.';
        } else {
            $strengths[] = 'Campaign start date is defined.';
        }

        if (!$campaign->end_date) {
            $score -= 30;

            $weaknesses[] = 'Campaign end date is not defined.';
        } else {
            $strengths[] = 'Campaign end date is defined.';
        }

        if (
            $campaign->start_date &&
            $campaign->end_date &&
            $campaign->start_date > $campaign->end_date
        ) {
            $score -= 40;

            $weaknesses[] = 'Campaign timeline is not logically ordered.';
        } elseif (
            $campaign->start_date &&
            $campaign->end_date
        ) {
            $strengths[] = 'Campaign timeline is logically ordered.';
        }

        $score = max(0, min(100, $score));

        $status = match (true) {
            $score < 50 => 'Not Ready',
            $score < 80 => 'Needs Improvement',
            default => 'Ready',
        };

        return new AssessmentResult(
            score: $score,
            status: $status,
            strengths: $strengths,
            weaknesses: $weaknesses,
        );
    }
}
