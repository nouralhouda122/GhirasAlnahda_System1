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
         //  'app_type'    => 'required|in:admin,manager,volunteer', // حماية المدخلات لتقبل الأنواع الثلاثة فقط
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
            'message' => 'Device token updated successfully'
        ], 200);








        }



private function resolveAppType($user): string
{
    // ملاحظة: الأسماء هنا يجب أن تطابق جدول roles تماماً.
    // الأسماء القديمة (Campaign Manager / Evaluation Manager / Evaluation Officer /
    // Campaign Employee) غير موجودة في النظام، وكان وجودها يجعل كل مدير
    // يسقط إلى 'volunteer' فلا تصله إشعارات لوحة الإدارة.
    // 'admin' = مشروع ghiras-dash (تطبيق السوبر أدمن فقط)
    if ($user->hasRole('Super Admin')) {
        return 'admin';
    }

    // 'manager' = مشروع finalproject-d4cd4 (تطبيق الإدارة: Manager وما دونه)
    if (
        $user->hasRole('Manager') ||
        $user->hasRole('Volunteer Manager') ||
        $user->hasRole('Team Leader') ||
        $user->hasRole('Employee')
    ) {
        return 'manager';
    }

    return 'volunteer';
}



}
