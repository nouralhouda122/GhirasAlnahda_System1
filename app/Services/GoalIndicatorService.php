<?php
namespace App\Services;
use App\Http\Resources\GoalIndicatorResource;
use App\Http\Resources\IndicatorMatchResource;
use App\Repositories\CampaingRepository;
use App\Repositories\Campanig_KpiRepository;
use App\Repositories\goal_IndicatorRepository;
use App\Repositories\IndicatorRepository;
class GoalIndicatorService
{
    protected $goalIndicatorRepository;
    protected $CampaignRepository;
    protected  $indicatorRepository;
    protected  $goal_IndicatorRepository;
    protected  $campaignSurveyService;
    private $goalRepository;
        private IndicatorScoreService $indicatorScoreService;
        private  $statusService;
private  $Goal_IndicatorRepository;
    public function __construct(
        Campanig_KpiRepository $goalIndicatorRepository,
        CampaingRepository $CampaignRepository,
        IndicatorRepository $indicatorRepository,
        goal_IndicatorRepository $goal_IndicatorRepository,
        BuildCampaignSurveyService $campaignSurveyService,
        Campanig_KpiRepository $goalRepository,
        IndicatorScoreService $indicatorScoreService,
        StatusService $statusService,
goal_IndicatorRepository $Goal_IndicatorRepository,
    ) {
        $this->goalIndicatorRepository = $goalIndicatorRepository;
        $this->goalRepository = $goalRepository;
        $this->indicatorScoreService = $indicatorScoreService;
        $this->statusService = $statusService;
        $this->Goal_IndicatorRepository = $Goal_IndicatorRepository;

        $this->CampaignRepository = $CampaignRepository;
        $this->indicatorRepository = $indicatorRepository;
        $this->goal_IndicatorRepository = $goal_IndicatorRepository;
        $this->campaignSurveyService =$campaignSurveyService;
    }
    public function index($id)
    {
        $campanig = $this->CampaignRepository->getById($id);
        if (!$campanig) {
            return [
                'data' => '',
                'message' => 'Campaign not found',
                'code' => 404];
        }
        $goals = $this->goalIndicatorRepository->getAllGoalsWithIndicators($campanig->id);
        if ($goals->isEmpty()) {
            return [
                'data' => [],
                'message' => 'No goals found',
                'code' => 200
            ];
        }

        return [
            'data' => GoalIndicatorResource::collection($goals),
            'message' => 'success',
            'code' => 200
        ];
    }
    public function show($goalId)
    {
        $goal = $this->goalIndicatorRepository
            ->findGoalWithIndicators($goalId);
        if (!$goal) {
            return [
                'data' => '',
                'message' => 'Goal not found',
                'code' => 404
            ];
        }
        if ($goal->goalIndicators->isEmpty()) {
            return [
                'data' => [],
                'message' => 'No indicators found for this goal',
                'code' => 200
            ];
        }

        return [
            'data' => IndicatorMatchResource::collection(
                $goal->goalIndicators
            ),

            'message' => 'success',

            'code' => 200
        ];}
    public function updateStatus($request, $goal_id, $indicator_id)
    {
        $goal = $this->goalIndicatorRepository
            ->findGoalWithIndicators($goal_id);
        if (!$goal) {
            return [
                'data' => '',
                'message' => 'Goal not found',
                'code' => 404
            ];
        }

        $indicator = $this->indicatorRepository
            ->getById($indicator_id);

        if (!$indicator) {

            return [
                'data' => '',
                'message' => 'Indicator not found',
                'code' => 404
            ];
        }

        $goalIndicator = $this->goal_IndicatorRepository
            ->getById($goal_id, $indicator_id);

        if (!$goalIndicator) {

            return [
                'data' => '',
                'message' => 'Goal indicator relation not found',
                'code' => 404
            ];
        }
        if ($request->approval_status === 'approved') {
            $this->campaignSurveyService ->build($goal->campaign_id); }
        if (
            $goalIndicator->approval_status ===
            $request->approval_status
        ) {
            return [
                'data' => '',
                'message' => 'Indicator already has this status',
                'code' => 409
            ];
        }
        $this->goal_IndicatorRepository->updateStatus(
            [
                'approval_status' => $request->approval_status,

                'approved_by_monitor' => auth()->id(),

                'approved_at' => now(),
            ],
            $goalIndicator
        );

        return [
            'data' => new IndicatorMatchResource(
                $goalIndicator->fresh('indicator')
            ),

            'message' => 'Indicator status updated successfully',

            'code' => 200
        ];
    }


    public function details(
         $goalId, $campaignId)
    {
        $goal = $this->goalRepository->findGoalWithIndicators($goalId);
        if (!$goal) {
            return [
                'data' => [],
                'message' => 'Goal not found',
                'code' => 404
            ];
        }

        $indicators = [];

        foreach ($goal->indicators as $indicator) {

            $score = $this->indicatorScoreService
                ->calculate(
                    $indicator,
                    $campaignId
                );

            $indicators[] = [

                'id' => $indicator->id,

                'name' => $indicator->name,

                'score' => round($score, 2),

                'status' => $this->statusService
                    ->getStatus($score),
            ];
        }

        return [
            'data' => [

                'goal' => [

                    'id' => $goal->id,

                    'title' => $goal->goal_text,

                    'weight' => $goal->weight,
                ],

                'indicators' => $indicators,
            ],

            'message' => 'success',

            'code' => 200
        ];
    }

    public function setTargetValue($request){

        $relation = $this->Goal_IndicatorRepository
            ->findByIds(
                $request->goal_id,
                $request->indicator_id
            );
        if (!$relation) {
            return [
                'data' => [],
                'message' => 'Indicator is not linked to this goal',
                'code' => 404
            ];
        }
        $this->Goal_IndicatorRepository
            ->updateTargetValue(
                $request->goal_id,
                $request->indicator_id,
                $request->target_value,
            );

        return [

            'data' => [

                'goal_id' => $request->goal_id,

                'indicator_id' => $request->indicator_id,

                'target_value' => $request->target_value
            ],

            'message' => 'Target value updated successfully',

            'code' => 200
        ];
    }
    public function setWeight(
        $request    ){
        $goal = $this->goalRepository
            ->findGoalWithIndicators($request->goal_id);
        if (!$goal) {
            return [
                'data' => [],
                'message' => 'Goal not found',
                'code' => 404
            ];
        }
        $goal->update([
            'weight' => $request->weight
        ]);
        return [
            'data' => [
                'goal_id' => $request->goal_id,

                'weight' => $request->weight
            ],
            'message' =>
                'Goal weight updated successfully',

            'code' => 200
        ];
    }













}
