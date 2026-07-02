<?php

namespace App\Services;
use App\Http\Requests\AttendanceRequest;
use App\Http\Resources\ApprovalRequestResource;
use App\Http\Resources\PointTransactionResources;
use App\Repositories\ApprovalRequestRepository;
use App\Repositories\AttendanceRepository;
use App\Repositories\CampaingRepository;
use App\Repositories\PointTransactionRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\VolunteerProfile;
use App\Http\Resources\AttendanceResource;
class AttendanceService
{
    public function __construct(
        CampaingRepository $CampaingRepository,
        AttendanceRepository $atendanceRepository,
        PointTransactionRepository $pointTransactionRepository
    ) {
        $this->CampaingRepository = $CampaingRepository;
        $this->atendanceRepository = $atendanceRepository;
        $this->pointTransactionRepository = $pointTransactionRepository;
    }
    public function leaderCheckIn(AttendanceRequest $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $user = Auth::user();
        $campaign = $this->CampaingRepository->getById($id);
        if (!$campaign) {
            return ['user'=>'',
                'message' => 'Campaign not found', 'code' => 404];
        }
        if ($campaign->leader_id !== $user->id) {
            return ['user'=>'','message' => 'Unauthorized', 'code' => 403];
        }
        $exists = $this->atendanceRepository->findActiveLeaderSession($user->id, $campaign->id);
        if ($exists) {
            return ['user'=>'','message' => 'Already checked in', 'code' => 400];
        }
        //
        if (now()->lt($campaign->start_date)) {
            return ['user'=>'','message' => 'Campaign not started yet', 'code' => 400];
        }
        if (now()->gt($campaign->end_date)) {
            return ['user'=>'','message' => 'Campaign already ended', 'code' => 400];
        }

        $distance = $this->calculateDistance(
            $request->latitude,
            $request->longitude,
            $campaign->latitude,
            $campaign->longitude
        );

        if ($distance > $campaign->radius) {
            return ['user'=>'',
                'message' => 'Out of allowed range', 'code' => 403];
        }

            $attendance = $this->atendanceRepository->create([
                'volunteer_id' => $user->id,
            'campaign_id' => $campaign->id,
            'check_in_time' => now(),
            'recorded_by' => $user->id,
            'is_leader' => true,
            'is_active_session' => true,
            ]);
         return [
                'user' => $attendance,
                'message' => 'Check-in successful',
                'code' => 201
            ];
        });
    }
    public function leaderCheckOut( $id)
    {
        return DB::transaction(function () use ( $id) {

            $user = Auth::user();
        $campaign = $this->CampaingRepository->getById($id);
        if (!$campaign) {
            return ['user'=>'',
                'message' => 'Campaign not found', 'code' => 404];
        }
        if ($campaign->leader_id !== $user->id) {
            return ['user'=>'',
                'message' => 'Unauthorized', 'code' => 403];
        }
        $attendance = $this->atendanceRepository
            ->findActiveLeaderSession($user->id, $campaign->id);
        if (!$attendance) {
            return ['user'=>'',
                'message' => 'No active session found',
                'code' => 400
            ];
        }

        if ($attendance->check_out_time) {
            return [
                'user'=>'',
                'message' => 'Already checked out', 'code' => 400];
        }

        $start = Carbon::parse($attendance->check_in_time);
        $end = min(now(), $campaign->end_date);
        $minutes = $start->diffInMinutes($end);
        $hours = floor($minutes / 60);
        $points = $hours;
        $this->atendanceRepository->update([
            'check_out_time' => now(),
            'hours' => $hours,
            'is_active_session' => false,
        ], $attendance);
        $profile = $user->volunteerProfile;
        $profile->increment('totalHours', $hours);
        $profile->increment('pointsBalance', $points);
        $this->pointTransactionRepository->create([
            'volunteer_id' => $user->id,
            'campaign_id' => $campaign->id,
            'points' => $points,
            'type' => 'attendance',
            'reason' => 'Leader campaign completion',
            'description' => 'Auto calculated from attendance',
            'awarded_by' => $user->id,
        ]);

        return [
            'user' => $attendance,
            'message' => 'Check-out successful',
            'code' => 200
        ];
    });}

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000;

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos($latFrom) * cos($latTo) *
            sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    public function index($user)
    {
        $attendances = $user->attendances()
            ->with(['volunteer', 'campaign'])
            ->get();

        if ($attendances->isEmpty()) {
            return [
                'user' => [],
                'message' => 'No attendance records found',
                'code' => 404
            ];
        }

        return [
            'user' => ApprovalRequestResource::collection($attendances),
            'message' => 'success',
            'code' => 200
        ];
    }
    public function show($campanig_id)
    {
        $campanig = $this->CampaingRepository->getById($campanig_id);

        if (!$campanig) {
            return [
                'user' => '',
                'message' => 'Campaign not found',
                'code' => 200
            ];
        }
        $attendances = $campanig->attendances()
            ->with(['volunteer', 'campaign'])
            ->get();

        if ($attendances->isEmpty()) {
            return [
                'user' => [],
                'message' => 'No attendance records for this campaign',
                'code' => 200
            ];
        }

        return [
            'user' => AttendanceResource::collection($attendances),
            'message' => 'success',
            'code' => 200
        ];
    }

    ///////راية


    public function scanVolunteerQr($request)
    {
        $currentUser = Auth::user();
        $campaignId  = $request->input('campaign_id');

        $campaign = $this->CampaingRepository->getById($campaignId);
        if (!$campaign) {
            return ['code' => 404, 'message' => 'Campaign not found.', 'data' => null];
        }

        if ($campaign->leader_id !== $currentUser->id) {
            return ['code' => 403, 'message' => 'Unauthorized. Only the team leader of this campaign can scan QR codes.', 'data' => null];
        }

        $profile = VolunteerProfile::with('user')->where('volunteer_id_code', $request->input('volunteer_id_code'))->first();

        if (!$profile) {
            return ['code' => 404, 'message' => 'Volunteer profile not found.', 'data' => null];
        }

        if (!$profile->is_active) {
            return ['code' => 403, 'message' => 'This ID card is disabled by administration.', 'data' => null];
        }

        if (Carbon::parse($profile->card_expiry_date)->isPast()) {
            return ['code' => 403, 'message' => 'This ID card has expired.', 'data' => null];
        }

        $isVolunteerApprovedInCampaign = DB::table('campaign_volunteer')
            ->where('campaign_id', $campaignId)
            ->where('volunteer_profile_id', $profile->id)
            ->where('status', 'approved')
            ->exists();

        if (!$isVolunteerApprovedInCampaign) {
            return ['code' => 400, 'message' => 'This volunteer is not approved or registered in this campaign.', 'data' => null];
        }

        $volunteer = $profile->user;

        $activeSession = $this->atendanceRepository->findActiveVolunteerSession($volunteer->id, $campaignId);

        if (!$activeSession) {
            $attendance = $this->atendanceRepository->create([
                'volunteer_id'       => $volunteer->id,
                'campaign_id'        => $campaignId,
                'check_in_time'      => Carbon::now(),
                'is_leader'          => false,
                'is_active_session'  => true,
                'hours'              => 0.00
            ]);

            return [
                'code'    => 200,
                'message' => "Check-in successful for: {$volunteer->name}",
                'data'    => new AttendanceResource($attendance->load('volunteer', 'campaign'))
            ];
        }

        // سـيـنـاريـو الـ Check-Out (تسجيل خروج وحساب الساعات)
        $checkOutTime = Carbon::now();
        $checkInTime  = Carbon::parse($activeSession->check_in_time);

        $minutes = $checkOutTime->diffInMinutes($checkInTime);
        $hours   = round($minutes / 60, 2);

        $this->atendanceRepository->update([
            'check_out_time'    => $checkOutTime,
            'is_active_session' => false,
            'hours'             => $hours
        ], $activeSession);

        return [
            'code'    => 200,
            'message' => "Check-out successful for: {$volunteer->name}. Total hours: {$hours}",
            'data'    => new AttendanceResource($activeSession->fresh(['volunteer', 'campaign']))
        ];
    }

    }
