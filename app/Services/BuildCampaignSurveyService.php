<?php
namespace App\Services;
use App\Repositories\CampaignSurveyRepository;
use App\Repositories\Campanig_KpiRepository;
use App\Repositories\SurveyQuestionRepository;
use Illuminate\Support\Facades\DB;
class BuildCampaignSurveyService
{
    private CampaignSurveyRepository $CampaignSurveyRepository;
    private Campanig_KpiRepository $campanig_KpiRepository;
    private SurveyQuestionRepository $surveyQuestionRepository;
    public function __construct(
        CampaignSurveyRepository $CampaignSurveyRepository,
        Campanig_KpiRepository $campanig_KpiRepository,
        SurveyQuestionRepository $surveyQuestionRepository,
    ) {
        $this->CampaignSurveyRepository = $CampaignSurveyRepository;
        $this->campanig_KpiRepository = $campanig_KpiRepository;
        $this->surveyQuestionRepository = $surveyQuestionRepository;
    }
    public function build(int $campaignId): array
    {
        try {
            $data = DB::transaction(function () use ($campaignId) {

                $beforeSurvey = $this->CampaignSurveyRepository
                    ->firstOrCreateSurvey($campaignId, 'before', 'Before Campaign Survey');
                $duringSurvey = $this->CampaignSurveyRepository
                    ->firstOrCreateSurvey($campaignId, 'during', 'During Campaign Survey');
                $afterSurvey = $this->CampaignSurveyRepository
                    ->firstOrCreateSurvey($campaignId, 'after', 'After Campaign Survey');
                $goals = $this->campanig_KpiRepository->getGoalWithIndicatorsAndQuestion($campaignId);
                foreach ($goals as $goal) {
                    foreach ($goal->goalIndicators as $goalIndicator) {

                        if ($goalIndicator->approval_status !== 'approved') {
                            continue;}
                        $indicator = $goalIndicator->indicator;
                        if (!$indicator) {
                            continue;
                        }
                        foreach ($indicator->questions as $question) {
                            $phase = $question->pivot->phase;
                            $survey = match ($phase) {
                                'before' => $beforeSurvey,
                                'during' => $duringSurvey,
                                'after' => $afterSurvey,
                                default => null,
                            };

                            if (!$survey) {
                                continue;
                            }

                            $exists = $this->surveyQuestionRepository
                                ->questionExists($survey->id, $question->id);
                            if ($exists) {
                                continue;
                            }
                            $this->surveyQuestionRepository
                                ->createSurveyQuestion(
                                    $survey->id,
                                    $question->id
                                );
                        }
                    }
                }
                return [
                    'before_survey_id' => $beforeSurvey->id,
                    'during_survey_id' => $duringSurvey->id,
                    'after_survey_id' => $afterSurvey->id,
                ];
            });
            return [
                'data' => $data,
                'message' => 'Campaign surveys built successfully',
                'code' => 200,
            ];

        } catch (\Exception $e) {

            return [
                'data' => $e->getMessage(),
                'message' => 'Failed to build surveys',
                'code' => 500,
            ];
        }
    }}
