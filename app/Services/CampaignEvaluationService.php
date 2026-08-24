<?php

namespace App\Services;

use App\Models\Survey;
use App\Models\SurveyAnswer;
use Illuminate\Support\Collection;

class CampaignEvaluationService
{
    /*
    |--------------------------------------------------------------------------
    | Default Values
    |--------------------------------------------------------------------------
    */

    private const DEFAULT_BEFORE_SCORE = 30;
    private const DEFAULT_DURING_SCORE = 40;
    private const DEFAULT_AFTER_SCORE  = 50;

    /**
     * Get baseline score.
     *
     * before = baseline
     */
    public function getBaseline(int $campaignId): float
    {
        $trend = collect($this->getTrend($campaignId));

        $before = $this->getPhaseScore(
            $trend,
            'before'
        );

        return round(
            $before ?? self::DEFAULT_BEFORE_SCORE,
            2
        );
    }

    /**
     * Get latest evaluation.
     *
     * Priority:
     * after -> during -> before
     */
    public function getLatestEvaluation(int $campaignId): float
    {
        $trend = collect($this->getTrend($campaignId));

        $after = $this->getPhaseScore(
            $trend,
            'after'
        );

        if ($after !== null) {
            return round($after, 2);
        }

        $during = $this->getPhaseScore(
            $trend,
            'during'
        );

        if ($during !== null) {
            return round($during, 2);
        }

        $before = $this->getPhaseScore(
            $trend,
            'before'
        );

        return round(
            $before ?? self::DEFAULT_BEFORE_SCORE,
            2
        );
    }

    /**
     * Get trend directly from surveys.
     *
     * Nothing is saved in campaign_evaluations.
     */
    public function getTrend(int $campaignId): array
    {
        $surveys = Survey::query()
            ->where('campaign_id', $campaignId)
            ->whereIn(
                'stage',
                ['before', 'during', 'after']
            )
            ->with([
                'surveyQuestions.question'
            ])
            ->orderBy('created_at')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | No surveys
        |--------------------------------------------------------------------------
        */

        if ($surveys->isEmpty()) {
            return $this->defaultTrend();
        }

        $trend = [];

        foreach ($surveys as $survey) {

            $date = $this->getSurveyDate($survey);

            $surveyQuestionIds = $survey
                ->surveyQuestions
                ->pluck('id');

            /*
            |--------------------------------------------------------------------------
            | No questions
            |--------------------------------------------------------------------------
            */

            if ($surveyQuestionIds->isEmpty()) {

                $trend[] = [
                    'date' => $date,
                    'score' => $this->getDefaultScoreByPhase(
                        $survey->stage
                    ),
                    'phase' => $survey->stage,
                ];

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Get answers
            |--------------------------------------------------------------------------
            */

            $answers = SurveyAnswer::query()
                ->whereIn(
                    'survey_question_id',
                    $surveyQuestionIds
                )
                ->with([
                    'surveyQuestion.question'
                ])
                ->get();

            /*
            |--------------------------------------------------------------------------
            | No answers
            |--------------------------------------------------------------------------
            */

            if ($answers->isEmpty()) {

                $trend[] = [
                    'date' => $date,
                    'score' => $this->getDefaultScoreByPhase(
                        $survey->stage
                    ),
                    'phase' => $survey->stage,
                ];

                continue;
            }

            $scores = [];

            foreach ($answers as $answer) {

                $question = $answer
                    ->surveyQuestion
                    ?->question;

                if (!$question) {
                    continue;
                }

                $scores[] = $this->normalizeAnswer(
                    $answer->answer,
                    $question
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Calculate score
            |--------------------------------------------------------------------------
            */

            if (empty($scores)) {

                $score = $this->getDefaultScoreByPhase(
                    $survey->stage
                );

            } else {

                $score = round(
                    collect($scores)->avg(),
                    2
                );
            }

            $trend[] = [
                'date' => $date,
                'score' => $score,
                'phase' => $survey->stage,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | If somehow trend is empty
        |--------------------------------------------------------------------------
        */

        if (empty($trend)) {
            return $this->defaultTrend();
        }

        /*
        |--------------------------------------------------------------------------
        | Sort by date
        |--------------------------------------------------------------------------
        */

        usort(
            $trend,
            function ($a, $b) {

                return strcmp(
                    $a['date'],
                    $b['date']
                );
            }
        );

        /*
        |--------------------------------------------------------------------------
        | Make sure before / during / after exist
        |--------------------------------------------------------------------------
        |
        | إذا كانت بعض المراحل غير موجودة في قاعدة البيانات
        | نضيفها بقيمة افتراضية.
        |
        */

        $trend = $this->completeMissingPhases($trend);

        return $trend;
    }

    /**
     * Complete missing phases with default values.
     */
    private function completeMissingPhases(array $trend): array
    {
        $hasBefore = collect($trend)
            ->contains('phase', 'before');

        $hasDuring = collect($trend)
            ->contains('phase', 'during');

        $hasAfter = collect($trend)
            ->contains('phase', 'after');

        /*
        |--------------------------------------------------------------------------
        | Base dates
        |--------------------------------------------------------------------------
        */

        $beforeDate = now()
            ->subMonth(2)
            ->format('Y-m');

        $duringDate = now()
            ->subMonth()
            ->format('Y-m');

        $afterDate = now()
            ->format('Y-m');

        /*
        |--------------------------------------------------------------------------
        | Add missing before
        |--------------------------------------------------------------------------
        */

        if (!$hasBefore) {

            $trend[] = [
                'date' => $beforeDate,
                'score' => self::DEFAULT_BEFORE_SCORE,
                'phase' => 'before',
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Add missing during
        |--------------------------------------------------------------------------
        */

        if (!$hasDuring) {

            $trend[] = [
                'date' => $duringDate,
                'score' => self::DEFAULT_DURING_SCORE,
                'phase' => 'during',
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Add missing after
        |--------------------------------------------------------------------------
        */

        if (!$hasAfter) {

            $trend[] = [
                'date' => $afterDate,
                'score' => self::DEFAULT_AFTER_SCORE,
                'phase' => 'after',
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Sort again
        |--------------------------------------------------------------------------
        */

        usort(
            $trend,
            function ($a, $b) {

                return strcmp(
                    $a['date'],
                    $b['date']
                );
            }
        );

        return $trend;
    }

    /**
     * Default trend when there are no surveys.
     */
    private function defaultTrend(): array
    {
        return [

            [
                'date' => now()
                    ->subMonth(2)
                    ->format('Y-m'),

                'score' => self::DEFAULT_BEFORE_SCORE,

                'phase' => 'before',
            ],

            [
                'date' => now()
                    ->subMonth()
                    ->format('Y-m'),

                'score' => self::DEFAULT_DURING_SCORE,

                'phase' => 'during',
            ],

            [
                'date' => now()
                    ->format('Y-m'),

                'score' => self::DEFAULT_AFTER_SCORE,

                'phase' => 'after',
            ],

        ];
    }

    /**
     * Default score according to phase.
     */
    private function getDefaultScoreByPhase(
        ?string $phase
    ): float {

        return match ($phase) {

            'before' =>
            self::DEFAULT_BEFORE_SCORE,

            'during' =>
            self::DEFAULT_DURING_SCORE,

            'after' =>
            self::DEFAULT_AFTER_SCORE,

            default =>
            0,
        };
    }

    /**
     * Calculate campaign impact.
     *
     * impact = current - baseline
     */
    public function getImpact(int $campaignId): array
    {
        $trend = collect(
            $this->getTrend($campaignId)
        );

        /*
        |--------------------------------------------------------------------------
        | Baseline
        |--------------------------------------------------------------------------
        */

        $baseline = $this->getPhaseScore(
            $trend,
            'before'
        );

        if ($baseline === null) {

            $baseline =
                self::DEFAULT_BEFORE_SCORE;
        }

        /*
        |--------------------------------------------------------------------------
        | Current
        |--------------------------------------------------------------------------
        |
        | after -> during -> before
        |--------------------------------------------------------------------------
        */

        $current = $this->getPhaseScore(
            $trend,
            'after'
        );

        if ($current === null) {

            $current = $this->getPhaseScore(
                $trend,
                'during'
            );
        }

        if ($current === null) {

            $current = $baseline;
        }

        /*
        |--------------------------------------------------------------------------
        | Calculate impact
        |--------------------------------------------------------------------------
        */

        $impact = round(
            $current - $baseline,
            2
        );

        return [

            'value' => $impact,

            'baseline_score' => round(
                (float) $baseline,
                2
            ),

            'current_score' => round(
                (float) $current,
                2
            ),

            'status' => $this->getImpactStatus(
                $impact
            ),
        ];
    }

    /**
     * Monthly momentum.
     *
     * Compare latest two trend points.
     */
    public function getMonthlyMomentum(
        int $campaignId
    ): array {

        $trend = collect(
            $this->getTrend($campaignId)
        );

        if ($trend->count() < 2) {

            return [
                'percentage_change' => 0,
                'direction' => 'stable',
            ];
        }

        $previous = (float) $trend
            ->get(
                $trend->count() - 2
            )['score'];

        $current = (float) $trend
            ->get(
                $trend->count() - 1
            )['score'];

        /*
        |--------------------------------------------------------------------------
        | Previous = 0
        |--------------------------------------------------------------------------
        */

        if ($previous == 0) {

            if ($current > 0) {

                return [
                    'percentage_change' => 100,
                    'direction' => 'up',
                ];
            }

            return [
                'percentage_change' => 0,
                'direction' => 'stable',
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Percentage
        |--------------------------------------------------------------------------
        */

        $change = round(
            (($current - $previous) / $previous) * 100,
            2
        );

        return [

            'percentage_change' => abs(
                $change
            ),

            'direction' => match (true) {

                $change > 0 =>
                'up',

                $change < 0 =>
                'down',

                default =>
                'stable',
            },
        ];
    }

    /**
     * Normalize answer to 0-100.
     */
    private function normalizeAnswer(
        mixed $answer,
        $question
    ): float {

        return match ($question->type) {

            'rating' => $this->normalizeRating(
                $answer,
                $question->scale
            ),

            'yes_no' => in_array(
                strtolower(
                    trim((string) $answer)
                ),
                [
                    'yes',
                    '1',
                    'true',
                    'نعم',
                ],
                true
            )
                ? 100
                : 0,

            default => 0,
        };
    }

    /**
     * Normalize rating.
     */
    private function normalizeRating(
        mixed $answer,
        ?int $scale
    ): float {

        $scale = $scale ?: 5;

        if ($scale <= 0) {
            return 0;
        }

        $value = (float) $answer;

        /*
        | لا نسمح بتجاوز 100
        */

        return round(
            min(
                max(
                    ($value / $scale) * 100,
                    0
                ),
                100
            ),
            2
        );
    }

    /**
     * Get score for specific phase.
     */
    private function getPhaseScore(
        Collection $trend,
        string $phase
    ): ?float {

        /*
        | آخر قيمة للمرحلة وليس أول قيمة.
        |
        | هذا مهم إذا كان عندك أكثر من Survey
        | لنفس المرحلة.
        */

        $item = $trend
            ->filter(
                fn ($item) =>
                    ($item['phase'] ?? null) === $phase
            )
            ->last();

        if (!$item) {
            return null;
        }

        return (float) (
            $item['score'] ?? 0
        );
    }

    /**
     * Impact status.
     */
    private function getImpactStatus(
        float $impact
    ): string {

        return match (true) {

            $impact >= 20 =>
            'excellent',

            $impact >= 10 =>
            'good',

            $impact > 0 =>
            'improved',

            $impact == 0 =>
            'stable',

            default =>
            'negative',
        };
    }

    /**
     * Get survey date safely.
     */
    private function getSurveyDate(
        $survey
    ): string {

        if ($survey->created_at) {

            return $survey
                ->created_at
                ->format('Y-m');
        }

        if ($survey->updated_at) {

            return $survey
                ->updated_at
                ->format('Y-m');
        }

        return now()->format('Y-m');
    }
}
