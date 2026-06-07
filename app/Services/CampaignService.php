<?php
namespace App\Services;
use App\Helpers\StorageHelper;
use App\Http\Requests\ApprovalRequest;
use App\Http\Requests\CampaingRequest;
use App\Http\Requests\SearchCampaignRequest;
use App\Http\Requests\SearchForPermissionsAndRolesRequest;
use App\Http\Resources\CampaignDetailsResource;
use App\Http\Resources\CampaignResource;
use App\Http\Resources\UserResource;
use App\Repositories\AttendanceRepository;
use App\Repositories\CampaingRepository;
use App\Repositories\PointTransactionRepository;
use App\Repositories\userRepository;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\FcmNotificationService;
class CampaignService
{
    public function __construct(
        CampaingRepository $CampaignRepository,
        KPIBrain $KPIBrain,
        IndicatorMatchingService $indicatorService,
        UserService   $userService,
           FcmNotificationService $fcmService
    ) {
        $this->CampaignRepository = $CampaignRepository;
        $this->KPIBrain = $KPIBrain;
        $this->indicatorService = $indicatorService;
        $this->userService=$userService;
          $this->fcmService = $fcmService;
    }
    public function create(CampaingRequest $request)
    {
        return DB::transaction(function () use ($request) {

            $data = $request->validated();
            unset($data['image'], $data['video'], $data['goals']);

            $campaign = $this->CampaignRepository->createCampaing($data);

            $results = [];

            if ($request->has_evaluation && !empty($request->goals)) {

                foreach ($request->goals as $goalText) {

                    // 🔥 Step 1
                    $analysis = $this->KPIBrain->analyze($goalText);

                    $kpi = $this->CampaignRepository->createCampaing_Kpi([
                        'campaign_id' => $campaign->id,
                        'goal_text' => $goalText,
                        'domain' => $analysis['domain'],
                        'intent' => $analysis['intent'],
                        'type' => $analysis['type'],
                        'target_value' => $analysis['target_value'],
                    ]);

                    // 🔥 Step 2 + ربط المؤشرات
                    $indicators = $this->indicatorService->generate(
                        $analysis,
                        $goalText,
                        $kpi->id
                    );

                    $results[] = [
                        'goal' => $kpi,
                        'analysis' => $analysis,
                        'indicators' => $indicators
                    ];
                }
            
            
            $managers = \App\Models\User::role('Evaluation Manager')->get();
           foreach ($managers as $manager) {
    $this->fcmService->sendNotification(
        $manager,
        'حملة جديدة',
        'تم إنشاء حملة جديدة تحتاج مراجعة من قسم التقييم والمتابعة',
        'new_campaign',
        'manager',
        [
            'campaign_id' => $campaign->id
        ]
    );
}
                }
            

            return [
                'user' => new CampaignDetailsResource($campaign),
                'message' => 'Campaign created successfully',
                'code' => 201
            ];
        });
    }
    public function show()
    {
        $campanig=$this->CampaignRepository->index();
        return (['user'=>  CampaignResource::collection($campanig),
            'message' => 'Campaigns retrieved successfully',
            'code' =>200
        ]) ;

    }
    //ضبط صلاحيات والادوار
    public function indexDetail($id)
    {
        $campanig = $this->CampaignRepository->indexDetail($id);
        if ($campanig) {
            return [
                'user' => new CampaignDetailsResource($campanig),
                'message' => 'Campaign retrieved successfully',
                'code' => 200
            ];

        }

        return [
            'user' => null,
            'message' => 'the campaign_id  is not found',
            'code' => 404
        ];
    }
    public function SearchCampaign(SearchCampaignRequest $request)
    {
        $campanig = $this->CampaignRepository->Search($request);
            return [
                'user' =>  CampaignResource::collection($campanig),
                'message' => 'Campaign retrieved successfully',
                'code' => 200
            ];
        }

    public function assignTeamLeader($campaignId, $userId)
    {
        $campaign = $this->CampaignRepository->getById($campaignId);
        if (!$campaign) {
            return [
                'data' => null,
                'message' => 'Campaign not found',
                'code' => 404
            ];
        }

        $user = $this->userService->getById($userId);
        if (!$user) {
            return [
                'data' => null,
                'message' => 'User not found',
                'code' => 404
            ];
        }

        if (!$user->hasRole('Volunteer')) {
            return [
                'data' => null,
                'message' => 'User must be a volunteer',
                'code' => 403
            ];
        }

        if ($campaign->leader_id) {
            return [
                'data' => null,
                'message' => 'Campaign already has a leader',
                'code' => 400
            ];
        }
        $this->CampaignRepository->update([
            'leader_id' => $userId
        ], $campaign);

        return [
            'data' => [
                'campaign_id' => $campaignId,
                'leader_id' => $userId
            ],
            'message' => 'Leader assigned successfully',
            'code' => 200
        ];
    }


/**
 * انضمام المتطوع النشط إلى حملة مباشرة طالما المقاعد متاحة - Clean Code
 */
    public function joinCampaign(int $campaignId)
    {
        $user = auth()->user();
        /** @var \App\Models\User $user */

        // 1. Check if user has an active volunteer profile
        if (!$user->volunteerProfile || (int) $user->volunteerProfile->is_active !== 1) {
            return [
                'user' => null,
                'message' => 'Your volunteer account must be active to join campaigns.',
                'code' => 403
            ];
        }

        $volunteerProfileId = $user->volunteerProfile->id;

        try {
            return DB::transaction(function () use ($campaignId, $volunteerProfileId, $user) {

                // 2. Fetch campaign and lock row to prevent race conditions
                $campaignWithLock = \App\Models\Campaign::lockForUpdate()->find($campaignId);

                if (!$campaignWithLock) {
                    return [
                        'user' => null,
                        'message' => 'The requested campaign was not found.',
                        'code' => 404
                    ];
                }

                // 3. Check campaign status (Must be approved or ongoing)
                if (!in_array($campaignWithLock->status, ['approved', 'ongoing'])) {
                    return [
                        'user' => null,
                        'message' => 'Registration for this campaign is currently unavailable.',
                        'code' => 400
                    ];
                }

                // 4. Check for duplicate registration
                $alreadyJoined = DB::table('campaign_volunteer')
                    ->where('volunteer_profile_id', $volunteerProfileId)
                    ->where('campaign_id', $campaignId)
                    ->exists();

                if ($alreadyJoined) {
                    return [
                        'user' => null,
                        'message' => 'You are already registered in this campaign.',
                        'code' => 400
                    ];
                }

                // 5. Check if campaign capacity is full
                if ($campaignWithLock->current_volunteers >= $campaignWithLock->required_volunteers) {
                    return [
                        'user' => null,
                        'message' => 'We are sorry, this campaign has reached its maximum volunteer capacity.',
                        'code' => 400
                    ];
                }

                // 6. Action: Insert into pivot table
                DB::table('campaign_volunteer')->insert([
                    'volunteer_profile_id' => $volunteerProfileId,
                    'campaign_id'          => $campaignId,
                    'status'               => 'approved',
                    'created_at'           => now(),
                    'updated_at'           => now(),
                ]);

                // 7. Increment current volunteers counter
                $campaignWithLock->increment('current_volunteers');

                return [
                    'user' => true,
                    'message' => 'You have successfully joined the campaign!',
                    'code' => 200
                ];
            });

        } catch (\Throwable $e) {
            Log::error('Campaign Direct Join Failed', [
                'user_id' => $user->id,
                'volunteer_profile_id' => $volunteerProfileId,
                'campaign_id' => $campaignId,
                'error' => $e->getMessage()
            ]);

            return [
                'user' => null,
                'message' => 'An unexpected error occurred while processing your request: ' . $e->getMessage(),
                'code' => 500
            ];
        }

}


    public function showMyCampanig()
    {
        $user = auth()->user();
        if (!$user->volunteerProfile) {
            return [
                'user' => null,
                'message' => 'No volunteer profile found for this user.',
                'code' => 404
            ];
        }
        $campaigns = $user->volunteerProfile
            ->campaigns()
            ->get();
        return [
            'user' => CampaignResource::collection($campaigns),
            'message' => 'Your campaigns retrieved successfully.',
            'code' => 200
        ];
    }

    public function showCamanigEvaulation()
    {
        $campanig=$this->CampaignRepository->indexForEvaulation();
        return (['user'=>  CampaignResource::collection($campanig),
            'message' => 'Campaigns retrieved successfully',
            'code' =>200
        ]) ;

    }
}
