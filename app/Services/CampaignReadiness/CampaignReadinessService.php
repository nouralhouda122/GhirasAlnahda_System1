<?php

namespace App\Services\CampaignReadiness;

use App\Models\Campaign;
use App\Services\CampaignReadiness\Assessments\TimelineAssessment;
use App\Services\CampaignReadiness\Assessments\BudgetAssessment;
use App\Services\CampaignReadiness\Assessments\GoalAssessment;
use App\Services\CampaignReadiness\Assessments\ResourcesAssessment;
use App\Services\CampaignReadiness\Assessments\LeadershipAssessment;
use App\Services\CampaignReadiness\Assessments\LocationAssessment;
use App\Services\CampaignReadiness\Assessments\EvaluationAssessment;
use App\DTOs\AssessmentResult;

class CampaignReadinessService
{
    public function __construct(
        private TimelineAssessment $timelineAssessment,
        private BudgetAssessment $budgetAssessment,
        private GoalAssessment $goalsAssessment,
        private ResourcesAssessment $resourcesAssessment,
        private LeadershipAssessment $leadershipAssessment,
        private LocationAssessment $locationAssessment,
        private EvaluationAssessment $evaluationAssessment,
    ) {}

    public function evaluate(Campaign $campaign): array
    {
        $timeline = $this->timelineAssessment->evaluate($campaign);
        $budget = $this->budgetAssessment->evaluate($campaign);
        $goals = $this->goalsAssessment->evaluate($campaign);
        $resources = $this->resourcesAssessment->evaluate($campaign);
        $leadership = $this->leadershipAssessment->evaluate($campaign);
        $location = $this->locationAssessment->evaluate($campaign);
        $evaluation = $this->evaluationAssessment->evaluate($campaign);

        $sections = [
            'timeline' => $timeline,
            'budget' => $budget,
            'goals' => $goals,
            'resources' => $resources,
            'leadership' => $leadership,
            'location' => $location,
            'evaluation' => $evaluation,
        ];

        $overallScore = $this->calculateOverallScore(
            array_values($sections)
        );

        $overallStatus = $this->determineOverallStatus(
            $sections
        );

        $decision = $this->buildDecision($sections);

        return [
            'overall_score' => $overallScore,
            'overall_status' => $overallStatus,
            'decision' => $decision,
            'sections' => $sections,
        ];
    }

    private function buildDecision(array $sections): array
    {
        $criticalIssues = [];

        foreach ($sections as $section) {

            if ($section->score < 50) {

                foreach ($section->weaknesses as $weakness) {
                    $criticalIssues[] = $weakness;
                }
            }
        }

        return [
            'status' => $this->determineOverallStatus($sections),
            'critical_issues' => $criticalIssues,
        ];
    }

    private function determineOverallStatus(array $sections): string
    {
        $scores = array_map(
            fn (AssessmentResult $result) => $result->score,
            $sections
        );

        if (empty($scores)) {
            return 'Not Ready';
        }

        if (min($scores) < 50) {
            return 'Not Ready';
        }

        if (min($scores) < 80) {
            return 'Needs Improvement';
        }

        return 'Ready';
    }

    private function calculateOverallScore(array $results): int
    {
        if (empty($results)) {
            return 0;
        }

        $total = array_sum(
            array_map(
                fn (AssessmentResult $result) => $result->score,
                $results
            )
        );

        return (int) round(
            $total / count($results)
        );
    }
}
