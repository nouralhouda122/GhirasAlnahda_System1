<?php

namespace App\Services\CampaignReadiness\Assessments;

use App\DTOs\AssessmentResult;
use App\Models\Campaign;

class LeadershipAssessment
{
    public function evaluate(Campaign $campaign): AssessmentResult
    {
        $strengths = [];
        $weaknesses = [];

        $leaderId = $campaign->leader_id ?? null;

        if (!$leaderId) {

            $weaknesses[] =
                'Campaign leader is not assigned.';

        } else {

            $strengths[] =
                'Campaign leader is assigned.';

            if (method_exists($campaign, 'leader')) {

                $leader = $campaign->leader;

                if ($leader) {
                    $strengths[] =
                        'Assigned campaign leader exists.';
                } else {
                    $weaknesses[] =
                        'Assigned campaign leader could not be found.';
                }
            }
        }

        $total = count($strengths) + count($weaknesses);

        $score = $total > 0
            ? (int) round((count($strengths) / $total) * 100)
            : 0;

        return new AssessmentResult(
            score: $score,
            status: $this->status($score),
            strengths: $strengths,
            weaknesses: $weaknesses
        );
    }

    private function status(int $score): string
    {
        return match (true) {
            $score < 50 => 'Not Ready',
            $score < 80 => 'Needs Improvement',
            default => 'Ready',
        };
    }
}
