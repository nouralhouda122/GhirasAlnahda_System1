<?php

namespace App\Services\CampaignReadiness\Assessments;

use App\DTOs\AssessmentResult;
use App\Models\Campaign;

class BudgetAssessment
{
    public function evaluate(Campaign $campaign): AssessmentResult
    {
        $strengths = [];
        $weaknesses = [];

        $financialTarget = $campaign->financial_target ?? null;

        if ($financialTarget === null || $financialTarget <= 0) {
            $weaknesses[] = 'Campaign financial target is not defined.';
        } else {
            $strengths[] = 'Campaign financial target is defined.';
        }

        $score = empty($weaknesses) ? 100 : 0;

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
