<?php

namespace App\Services\CampaignReadiness\Assessments;

use App\DTOs\AssessmentResult;
use App\Models\Campaign;

class GoalAssessment
{
    public function evaluate(Campaign $campaign): AssessmentResult
    {
        $strengths = [];
        $weaknesses = [];

        $goals = $campaign->goals ?? collect();

        if ($goals instanceof \Illuminate\Support\Collection) {
            $goals = $goals->all();
        }

        if (empty($goals)) {
            return new AssessmentResult(
                score: 0,
                status: 'Not Ready',
                strengths: [],
                weaknesses: [
                    'Campaign has no defined goals.'
                ]
            );
        }

        $strengths[] = 'Campaign has defined goals.';

        $hasClearGoals = true;
        $allHaveTargets = true;

        foreach ($goals as $goal) {

            $name = is_array($goal)
                ? ($goal['name'] ?? null)
                : ($goal->name ?? null);

            $target = is_array($goal)
                ? ($goal['target_value'] ?? null)
                : ($goal->target_value ?? null);

            if (!$name) {
                $hasClearGoals = false;
            }

            if ($target === null) {
                $allHaveTargets = false;
            }
        }

        if ($hasClearGoals) {
            $strengths[] = 'Campaign goals are clearly defined.';
        } else {
            $weaknesses[] = 'Some campaign goals are not clearly defined.';
        }

        if ($allHaveTargets) {
            $strengths[] = 'All campaign goals have target values.';
        } else {
            $weaknesses[] = 'Some campaign goals are missing target values.';
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
