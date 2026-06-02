<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\NotificationRepository;
use Illuminate\Support\Facades\Auth;

class DeviceTokenController extends Controller
{
    protected $notificationRepository;

    // حقن الـ Repository داخل الـ Controller (Dependency Injection)
    public function __construct(NotificationRepository $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
    }

    /**
     * تحديث أو إنشاء توكن الجهاز لأي مستخدم (Volunteer, Manager, Admin)
     */
    public function updateDeviceToken(Request $request)
    {
        // 1. التحقق من البيانات القادمة من التطبيقات
        $request->validate([
            'fcm_token'   => 'required|string',
           // 'app_type'    => 'required|in:admin,manager,volunteer', // حماية المدخلات لتقبل الأنواع الثلاثة فقط
            'device_type' => 'nullable|in:android,ios,web'
        ]);

        // 2. جلب المستخدم الحالي المسجل عبر الـ Auth Token
        $user = Auth::user();
        $appType = $this->resolveAppType($user);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated.'
            ], 401);
        }

        // 3. حفظ التوكن في قاعدة البيانات عبر الـ Repository
        $this->notificationRepository->updateOrCreateToken(
            $user->id,
            $request->fcm_token,
           $appType,
            $request->device_type
        );

        return response()->json([
            'success' => true,
            'message' => 'Device token updated successfully for app: ' . $appType
        ], 200);
   
   
   
   
   
   
   
   
        }



private function resolveAppType($user): string
{
    if ($user->hasRole('Super Admin')) {
        return 'admin';
    }

    if (
        $user->hasRole('Campaign Manager') ||
        $user->hasRole('Campaign Employee') ||
        $user->hasRole('Volunteer Manager') ||
        $user->hasRole('Evaluation Manager') ||
        $user->hasRole('Evaluation Officer') ||
        $user->hasRole('Team Leader')
    ) {
        return 'manager';
    }

    return 'volunteer';
}


        
}