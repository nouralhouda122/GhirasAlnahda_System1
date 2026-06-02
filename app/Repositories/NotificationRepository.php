<?php

namespace App\Repositories;

use App\Models\Notification;
use Illuminate\Support\Facades\DB;

class NotificationRepository
{
    /**
     * حفظ أو تحديث توكن الجهاز للمخدم بناءً على نوع التطبيق
     */
   /**
 * حفظ أو تحديث توكن الجهاز للمخدم بناءً على نوع التطبيق
 */
public function updateOrCreateToken(int $userId, string $fcmToken, string $appType, ?string $deviceType = null)
{
    // 1. التحقق أولاً فيما إذا كان هذا التوكن مسجلاً مسبقاً لأي مستخدم آخر أو تطبيق آخر لتنظيفه
    DB::table('user_fcm_tokens')->where('fcm_token', $fcmToken)->delete();

    // 2. الآن نقوم بعملية الإدخال أو التحديث الآمنة تماماً للمستخدم الحالي
    return DB::table('user_fcm_tokens')->updateOrInsert(
        [
            'user_id'   => $userId,
            'app_type'  => $appType // البحث بناءً على المستخدم ونوع التطبيق
        ],
        [
            'fcm_token'   => $fcmToken,
            'device_type' => $deviceType,
            'updated_at'  => now(),
            'created_at'  => now()
        ]
    );
}

    /**
     * جلب جميع التوكنز الخاصة بمستخدم معين لتطبيق محدد
     */
    public function getUserTokens(int $userId, string $appType): array
    {
        return DB::table('user_fcm_tokens')
            ->where('user_id', $userId)
            ->where('app_type', $appType)
            ->pluck('fcm_token')
            ->toArray();
    }

    /**
     * حفظ الإشعار في أرشيف قاعدة البيانات (In-App Archive)
     */
    public function saveToArchive(int $userId, string $title, string $message, string $type, array $data = [])
    {
        // استخدام الموديل الموحد بالحرف الكبير كمعيار نظيف
        return Notification::create([
            'type'    => $type,
            'user_id' => $userId,
            'title'   => $title,
            'data'    => array_merge(['message' => $message], $data),
            'read_at' => null,
        ]);
    }


    /**
 * إرسال إشعار لمستخدم محدد (حفظ في الأرشيف + تجهيز لـ Firebase مستقبلاً)
 */
public function sendToUser(int $userId, string $title, string $message, string $type, array $data = [], string $appType = 'volunteer')
{
    // 1. حفظ الإشعار في أرشيف قاعدة البيانات للمستخدم ليظهر له داخل التطبيق
    $notification = $this->saveToArchive($userId, $title, $message, $type, $data);

    // 2. جلب التوكنز الخاصة بأجهزة هذا المستخدم بناءً على نوع التطبيق
    $tokens = $this->getUserTokens($userId, $appType);

    // 3. هنا سيتم استدعاء خدمة Firebase Cloud Messaging (FCM) لإرسال الإشعار الفوري للأجهزة
    if (!empty($tokens)) {
        // فكرة للمستقبل: app(FCMService::class)->sendNotification($tokens, $title, $message, $data);
    }

    return $notification;
}



public function getForUser(int $userId)
{
    return \App\Models\Notification::where('user_id', $userId)
        ->orderBy('created_at', 'desc')
        ->get();
}
}