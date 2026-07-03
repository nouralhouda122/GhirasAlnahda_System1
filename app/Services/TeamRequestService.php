<?php

namespace App\Services;

use App\Models\TeamRequest;
use App\Models\TeamRequestMember;
use App\Models\Campaign;
use App\Repositories\NotificationRepository; // 🌟 1. اعتماد مستودع الإشعارات الموحد والمنظم
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TeamRequestService
{
    private NotificationRepository $notificationRepository; // 🌟 2. تعريف خاصية المستودع الجديد

    // 🌟 3. حقن الـ NotificationRepository بدلاً من الـ FcmService القديم
    public function __construct(NotificationRepository $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
    }

    /**
     * إنشاء طلب انضمام فريق جديد وإرسال الإشعارات للأعضاء
     */
    public function createTeamRequest(int $campaignId, array $memberProfileIds)
    {
        $user = auth()->user();
        $creatorProfileId = $user->volunteerProfile->id;
        $creatorName = $user->name; // جلب اسم القائد للإشعار

        return DB::transaction(function () use ($campaignId, $memberProfileIds, $creatorProfileId, $creatorName) {
            $campaign = Campaign::find($campaignId);

            if (!$campaign) {
                return ['code' => 404, 'message' => 'Campaign not found', 'data' => null];
            }

            if ($campaign->status !== 'approved' && $campaign->status !== 'ongoing') {
                return ['code' => 400, 'message' => 'Campaign not available for team joining', 'data' => null];
            }

            $teamSize = count($memberProfileIds) + 1;

            if ($teamSize < 4 || $teamSize > 8) {
                return ['code' => 400, 'message' => 'Team size must be between 4 and 8', 'data' => null];
            }

            if ($campaign->current_volunteers + $teamSize > $campaign->required_volunteers) {
                return ['code' => 400, 'message' => 'Not enough slots in campaign', 'data' => null];
            }

            // إنشاء رأس الطلب في جدول team_requests
            $teamRequest = TeamRequest::create([
                'campaign_id' => $campaignId,
                'creator_volunteer_profile_id' => $creatorProfileId,
                'status' => 'pending',
                'expires_at' => Carbon::now()->addHours(12),
                'required_acceptance_percentage' => 80,
            ]);

            // إضافة منشئ الفريق (القائد) كعضو مقبول تلقائياً
            TeamRequestMember::create([
                'team_request_id' => $teamRequest->id,
                'volunteer_profile_id' => $creatorProfileId,
                'status' => 'accepted',
                'responded_at' => now(),
            ]);

            // إدخال الأعضاء المدعوين وإرسال الإشعارات لهم
            foreach ($memberProfileIds as $id) {
                // يتم الحفظ في جدول team_request_members أولاً
                TeamRequestMember::create([
                    'team_request_id' => $teamRequest->id,
                    'volunteer_profile_id' => $id,
                    'status' => 'pending',
                ]);

                $profile = \App\Models\VolunteerProfile::with('user')->find($id);
                $memberUser = $profile?->user;

                if ($memberUser) {
                    // 🌟 الحماية الذهبية: try-catch لمنع عمل Rollback للـ Transaction عند فشل إرسال الـ FCM
                    try {
                        // استخدام التابع الموحد لإرسال وحفظ الإشعار بالأرشيف لقاعدة البيانات والـ FCM معاً
                        $this->notificationRepository->sendToUser(
                            $memberUser->id,
                            'دعوة للانضمام إلى فريق تطوعي 🌿',
                            "دعاك الزميل {$creatorName} للانضمام لفريقه في حملة ({$campaign->title}).",
                            'team_invitation',
                            [
                                'team_request_id' => $teamRequest->id,
                                'campaign_id' => $campaignId
                            ],
                            'volunteer' // توحيد الـ App Type مع حقل جدول الـ Tokens
                        );
                    } catch (\Exception $e) {
                        // تسجيل الخطأ في الـ Logs لتتبعه لاحقاً دون التسبب في تراجع تخزين الأعضاء
                        Log::error("FCM Send failed for user {$memberUser->id} in TeamRequest {$teamRequest->id}: " . $e->getMessage());
                    }
                }
            }
            
            return ['code' => 200, 'message' => 'Team request created successfully', 'data' => $teamRequest];
        });
    }

    /**
     * قبول دعوة الانضمام للفريق
     */
    public function acceptInvitation(int $teamRequestId)
    {
        return DB::transaction(function () use ($teamRequestId) {
            $profileId = auth()->user()->volunteerProfile->id;

            $member = TeamRequestMember::where('team_request_id', $teamRequestId)
                ->where('volunteer_profile_id', $profileId)
                ->first();

            if (!$member || $member->status !== 'pending') {
                return ['code' => 404, 'message' => 'Invitation not found or already processed.', 'data' => null];
            }

            $member->update([
                'status' => 'accepted',
                'responded_at' => now()
            ]);

            return $this->checkCompletion($teamRequestId);
        });
    }

    /**
     * رفض دعوة الانضمام للفريق
     */
    public function rejectInvitation(int $teamRequestId)
    {
        return DB::transaction(function () use ($teamRequestId) {
            $profileId = auth()->user()->volunteerProfile->id;

            $member = TeamRequestMember::where('team_request_id', $teamRequestId)
                ->where('volunteer_profile_id', $profileId)
                ->first();

            if (!$member || $member->status !== 'pending') {
                return ['code' => 404, 'message' => 'Invitation not found or already processed.', 'data' => null];
            }

            $member->update([
                'status' => 'rejected',
                'responded_at' => now()
            ]);

            return $this->checkCompletion($teamRequestId);
        });
    }

    /**
     * دالة التحقق الذكي من اكتمال قبول أعضاء الفريق أو إلغاء الطلب
     */
    private function checkCompletion(int $teamRequestId)
    {
        $teamRequest = TeamRequest::with('members')->find($teamRequestId);

        if (!$teamRequest || $teamRequest->status !== 'pending') {
            return ['code' => 404, 'message' => 'Active team request not found', 'data' => null];
        }

        $members = $teamRequest->members;
        $total = $members->count();
        $accepted = $members->where('status', 'accepted')->count();
        $rejected = $members->where('status', 'rejected')->count();

        $percentage = $total > 0 ? ($accepted / $total) * 100 : 0;

        // 1. حالة النجاح: الوصول للنسبة المطلوبة (80% فما فوق)
        if ($percentage >= $teamRequest->required_acceptance_percentage) {
            $teamRequest->update(['status' => 'completed']);

            foreach ($members as $member) {
                if ($member->status === 'accepted') {
                    $result = app(CampaignService::class)->registerVolunteerToCampaign(
                        $teamRequest->campaign_id,
                        $member->volunteer_profile_id
                    );

                    // 🌟 إذا فشل تسجيل العضو داخل الحملة لأي سبب (امتلأت مثلاً)، نرمي Exception ليعمل الـ Transaction الأب تراجعاً صريحاً
                    if (isset($result['code']) && $result['code'] !== 200) {
                        throw new \Exception($result['message'] ?? 'Failed to register team member to campaign.');
                    }
                }
            }
            return ['code' => 200, 'message' => 'Team approved and joined campaign successfully.', 'data' => true];
        }

        // 2. الفحص الذكي: هل أصبح مستحيلاً رياضياً الوصول للـ 80% بسبب عدد الرفض؟
        $maxPossibleAccepted = $total - $rejected;
        $maxPossiblePercentage = $total > 0 ? ($maxPossibleAccepted / $total) * 100 : 0;

        if ($maxPossiblePercentage < $teamRequest->required_acceptance_percentage) {
            $teamRequest->update(['status' => 'cancelled']);
            return ['code' => 200, 'message' => 'Team request cancelled because required acceptance percentage cannot be reached.', 'data' => false];
        }

        return ['code' => 200, 'message' => 'Response recorded.', 'data' => true];
    }

    /**
     * جلب المتطوعين المتاحين لإنشاء فريق بدون أي تضارب منطقي أو زمني
     */
    public function getAvailableVolunteersForTeam(int $campaignId)
    {
        $creatorProfileId = auth()->user()->volunteerProfile->id;
        $campaign = Campaign::findOrFail($campaignId);

        $startDate = $campaign->start_date;
        $endDate   = $campaign->end_date;

        return \App\Models\VolunteerProfile::with('user')
            // استبعاد منشئ الفريق (القائد نفسه)
            ->where('id', '!=', $creatorProfileId)

            // ليس مسجلاً بالفعل في هذه الحملة بشكل مقبول
            ->whereDoesntHave('campaigns', function ($query) use ($campaignId) {
                $query->where('campaigns.id', $campaignId)
                      ->where('campaign_volunteer.status', 'approved');
            })

            // ليس لديه دعوة معلقة حالياً لنفس الحملة
            ->whereDoesntHave('teamInvitations', function ($query) use ($campaignId) {
                $query->where('status', 'pending')
                      ->whereHas('teamRequest', function ($q) use ($campaignId) {
                          $q->where('campaign_id', $campaignId)
                            ->where('status', 'pending');
                      });
            })

            // لا يوجد تعارض مواعيد مع حملة أخرى مقبول فيها المتطوع في نفس الوقت
            ->whereDoesntHave('campaigns', function ($query) use ($startDate, $endDate) {
                $query->where('campaign_volunteer.status', 'approved')
                      ->where(function ($q) use ($startDate, $endDate) {
                          $q->whereBetween('campaigns.start_date', [$startDate, $endDate])
                            ->orWhereBetween('campaigns.end_date', [$startDate, $endDate])
                            ->orWhere(function ($q2) use ($startDate, $endDate) {
                                $q2->where('campaigns.start_date', '<=', $startDate)
                                   ->where('campaigns.end_date', '>=', $endDate);
                            });
                      });
            })
            ->get()
            ->map(function ($profile) {
                return [
                    'volunteer_profile_id' => $profile->id,
                    'name' => $profile->user?->name,
                    'gender' => $profile->gender,
                    'age' => $profile->age,
                    'preferred_field' => $profile->preferred_field,
                ];
            });
    }
}