<?php

namespace App\Services\CampaignReadiness\Assessments;

use App\DTOs\AssessmentResult;
use App\Models\Campaign;

class ResourcesAssessment
{
    public function evaluate(Campaign $campaign): AssessmentResult
    {
        $strengths = [];
        $weaknesses = [];

        $required = (int) ($campaign->required_volunteers ?? 0);
        $available = (int) ($campaign->available_volunteers ?? 0);

        if ($required <= 0) {
            $strengths[] = 'No additional volunteer capacity is required.';

            return new AssessmentResult(
                score: 100,
                status: 'Ready',
                strengths: $strengths,
                weaknesses: []
            );
        }

        if ($available >= $required) {

            $strengths[] = 'Required volunteer capacity is available.';

            $score = 100;

        } else {

            $missing = $required - $available;

            $weaknesses[] =
                "Campaign is missing {$missing} required volunteers.";

            $score = (int) round(
                ($available / $required) * 100
            );
        }

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
