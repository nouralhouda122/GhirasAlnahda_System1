<?php

namespace App\Services\CampaignReadiness\Assessments;

use App\DTOs\AssessmentResult;
use App\Models\Campaign;

class LocationAssessment
{
    public function evaluate(Campaign $campaign): AssessmentResult
    {
        $strengths = [];
        $weaknesses = [];

        $location = $campaign->location ?? null;
        $latitude = $campaign->latitude ?? null;
        $longitude = $campaign->longitude ?? null;
        $radius = $campaign->location_radius ?? null;

        if ($location) {
            $strengths[] = 'Campaign location is defined.';
        } else {
            $weaknesses[] = 'Campaign location is not defined.';
        }

        if ($latitude !== null && $longitude !== null) {
            $strengths[] =
                'Campaign geographic coordinates are defined.';
        } else {
            $weaknesses[] =
                'Campaign geographic coordinates are not defined.';
        }

        if ($radius !== null && $radius > 0) {
            $strengths[] =
                'Campaign location radius is defined.';
        } else {
            $weaknesses[] =
                'Campaign location radius is not defined.';
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
