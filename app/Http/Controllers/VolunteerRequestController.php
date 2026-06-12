<?php

namespace App\Http\Controllers;
use App\Http\Requests\VolunteerStoreRequest;
use App\Models\JoinRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\VolunteerRequestService;
use App\Models\User;
use App\Services\FcmNotificationService;
class VolunteerRequestController extends Controller
{
    protected $service;
    protected $fcmNotificationService;
protected $notificationRepo; // أضفنا هذه الخاصية

// حقن المستودع هنا في المعاملات

public function __construct(
    VolunteerRequestService $service,
    \App\Repositories\NotificationRepository $notificationRepo,
    FcmNotificationService $fcmNotificationService
)
{
    $this->service = $service;
    $this->notificationRepo = $notificationRepo;
    $this->fcmNotificationService = $fcmNotificationService;
}
    public function index()
    {
        $requests = $this->service->getPendingRequests();
        return response()->json([
            'success' => true,
            'data' => $requests
        ]);
    }
    public function show($id)
    {
        $request = $this->service->getRequestDetails($id);
        return response()->json([
            'success' => true,
            'data' => $request
        ]);
    }

   public function store(VolunteerStoreRequest $request)
{
    $path = $request->file('cv')->store('volunteer_cvs', 'public');

    $data = $request->validated();

    unset($data['cv']);

    $data['user_id'] = auth()->id();
    $data['cv_path'] = $path;
    $data['status']  = 'pending';

    $joinRequest = \App\Models\JoinRequest::create($data);

        $campaignManager = \App\Models\User::role('Campaign Manager')->first();

    if ($campaignManager) {

        $this->fcmNotificationService->sendNotification(
            $campaignManager,
            'New Volunteer Request',
            'New volunteer request from ' . auth()->user()->name,
            'new_volunteer_request',
            'manager',
            [
                'join_request_id' => (string)$joinRequest->id,
                'volunteer_id' => (string)auth()->id()
            ]
        );
    }

    return response()->json([
        'message' => 'Volunteer request submitted successfully!',
        'cv_url' => asset('storage/' . $path),
        'data' => $joinRequest
    ], 201);
}
    public function updateStatus(Request $request, $id)
    {
        // نتحقق من أن الحالة المرسلة إما approved أو rejected
        $request->validate([
            'status' => 'required|in:approved,rejected'
        ]);
        $result = $this->service->processStatus($id, $request->status);
        return response()->json([
            'message' => "Request status updated to {$request->status} successfully.",
            'data' => $result
        ]);
    }



    ////////////////

    public function getMyIDCard()
    {
        // تحميل البروفايل المرتبط بالمستخدم الحالي
        $user = auth()->user()->load('volunteerProfile');
        $profile = $user->volunteerProfile;

        // 1. التحقق من وجود البروفايل (أمان إضافي)
        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'Volunteer profile not found for this account.'
            ], 404);
        }

        // 2. التحقق من حالة الحظر (is_active)
        if (!$profile->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'This ID card is currently disabled. Please contact administration.'
            ], 403);
        }

        // 3. التحقق من تاريخ انتهاء الصلاحية
        $isExpired = \Carbon\Carbon::parse($profile->card_expiry_date)->isPast();

        if ($isExpired) {
            return response()->json([
                'success' => false,
                'status' => 'Expired',
                'message' => 'This ID card has expired and requires renewal.'
            ], 403);
        }

        // 4. حالة النجاح
        return response()->json([
            'success' => true,
            'data' => [
                'full_name'    => $user->name,
                'id_number'    => $profile->volunteer_id_code,
                'member_since' => $user->created_at->format('M Y'),
                'expiry_date'  => $profile->card_expiry_date,
                'qr_code_url'  => asset('storage/' . $profile->qr_code_path),
                'status'       => 'Active'
            ]
        ]);
    }




    public function getMyNotifications()
{
    // 1. جلب الـ ID الخاص بالمستخدم المسجل دخوله حالياً من التوكن (المتطوع)
    $userId = auth()->id(); 

    // 2. استدعاء الدالة المجهزة في الريبوزيتوري لجلب إشعاراته
    $notifications = $this->notificationRepo->getForUser($userId);

    // 3. إرجاع الإشعارات كاستجابة JSON
    return response()->json([
        'success' => true,
        'data' => $notifications
    ]);
}

 
}
