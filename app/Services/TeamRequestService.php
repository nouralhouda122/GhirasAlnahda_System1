<?php

namespace App\Services;

use App\Models\TeamRequest;
use App\Models\TeamRequestMember;
use App\Models\Campaign;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TeamRequestService
{
    private FcmNotificationService $fcmService;

public function __construct(FcmNotificationService $fcmService)
{
    $this->fcmService = $fcmService;
}

    public function createTeamRequest(int $campaignId, array $memberProfileIds)
    {
        $user = auth()->user();
        $creatorProfileId = $user->volunteerProfile->id;

        return DB::transaction(function () use ($campaignId, $memberProfileIds, $creatorProfileId) {

            $campaign = Campaign::find($campaignId);

            if (!$campaign) {
                return ['code' => 404, 'message' => 'Campaign not found', 'data' => null];
            }

            if ($campaign->status !== 'approved' && $campaign->status !== 'ongoing') {
                return ['code' => 400, 'message' => 'Campaign not available for team joining', 'data' => null];
            }

            $teamSize = count($memberProfileIds) + 1; // + creator

            if ($teamSize < 4 || $teamSize > 8) {
                return ['code' => 400, 'message' => 'Team size must be between 4 and 8', 'data' => null];
            }

            if ($campaign->current_volunteers + $teamSize > $campaign->required_volunteers) {
                return ['code' => 400, 'message' => 'Not enough slots in campaign', 'data' => null];
            }

            $teamRequest = TeamRequest::create([
                'campaign_id' => $campaignId,
                'creator_volunteer_profile_id' => $creatorProfileId,
                'status' => 'pending',
                'expires_at' => Carbon::now()->addHours(12),
                'required_acceptance_percentage' => 80,
            ]);

            // add creator as accepted
            TeamRequestMember::create([
                'team_request_id' => $teamRequest->id,
                'volunteer_profile_id' => $creatorProfileId,
                'status' => 'accepted',
                'responded_at' => now(),
            ]);

            // add invited members
           $members = $memberProfileIds;
  foreach ($memberProfileIds as $id) {

    TeamRequestMember::create([
        'team_request_id' => $teamRequest->id,
        'volunteer_profile_id' => $id,
        'status' => 'pending',
    ]);

    $user = \App\Models\VolunteerProfile::with('user')
        ->find($id)?->user;

    if ($user) {
        $this->fcmService->sendNotification(
            $user,
            'دعوة للانضمام إلى فريق تطوعي 🌿',
            'تمت دعوتك للانضمام إلى فريق تطوعي داخل حملة.',
            'team_invitation',
            'volunteer_app',
            [
                'team_request_id' => $teamRequest->id,
                'campaign_id' => $campaignId
            ]
        );
    }
}
            return ['code' => 200, 'message' => 'Team request created', 'data' => $teamRequest];
        });
    }

    public function acceptInvitation(int $teamRequestId)
    {
        $profileId = auth()->user()->volunteerProfile->id;

        $member = TeamRequestMember::where('team_request_id', $teamRequestId)
            ->where('volunteer_profile_id', $profileId)
            ->first();

        if (!$member) {
            return ['code' => 404, 'message' => 'Invitation not found', 'data' => null];
        }

        $member->update([
            'status' => 'accepted',
            'responded_at' => now()
        ]);

        return $this->checkCompletion($teamRequestId);
    }

    public function rejectInvitation(int $teamRequestId)
    {
        $profileId = auth()->user()->volunteerProfile->id;

        TeamRequestMember::where('team_request_id', $teamRequestId)
            ->where('volunteer_profile_id', $profileId)
            ->update([
                'status' => 'rejected',
                'responded_at' => now()
            ]);

        return $this->checkCompletion($teamRequestId);
    }

    private function checkCompletion(int $teamRequestId)
    {
        $teamRequest = TeamRequest::with('members')->find($teamRequestId);

        if (!$teamRequest) {
            return ['code' => 404, 'message' => 'Team request not found', 'data' => null];
        }

        $members = $teamRequest->members;

        $total = $members->count();
        $accepted = $members->where('status', 'accepted')->count();

       $percentage = $total > 0 ? ($accepted / $total) * 100 : 0;

        if ($percentage >= $teamRequest->required_acceptance_percentage) {

            $teamRequest->update(['status' => 'completed']);

            // إدخال كل المقبولين في الحملة
            foreach ($members as $member) {

                if ($member->status === 'accepted') {
                    app(CampaignService::class)
                        ->registerVolunteerToCampaign(
                            $teamRequest->campaign_id,
                            $member->volunteer_profile_id
                        );
                }
            }

            return ['code' => 200, 'message' => 'Team approved and joined campaign', 'data' => true];
        }

        return ['code' => 200, 'message' => 'Response recorded', 'data' => true];
    }

    public function completeTeamRequest($teamRequestId)
    {
        return $this->checkCompletion($teamRequestId);
    }

    public function expireTeamRequest()
    {
        $expired = TeamRequest::where('status', 'pending')
            ->where('expires_at', '<', now())
            ->get();

        foreach ($expired as $team) {
            $team->update(['status' => 'expired']);
        }

        return ['code' => 200, 'message' => 'Expired requests handled', 'data' => true];
    }
}