<?php

namespace App\Services;

use Exception;
use App\Repositories\NotificationRepository;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Illuminate\Support\Facades\DB;
class FcmNotificationService
{
    protected $notificationRepository;

    public function __construct(NotificationRepository $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
    }

    /**
     * دالة ديناميكية لتوليد مجمّع اتصال الفايربيز بناءً على نوع التطبيق
     */
    private function getMessagingInstance(string $appType)
    {
        try {
            $configPath = config("firebase.credentials.$appType");

            if (!$configPath || !file_exists(base_path($configPath))) {
                Log::error("Firebase config file not found for app type: {$appType} inside env path: {$configPath}");
                return null;
            }

            $factory = (new Factory)->withServiceAccount(base_path($configPath));
            return $factory->createMessaging();

        } catch (Exception $e) {
            Log::error("Error initializing Firebase for {$appType}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * إرسال إشعار موحد وذكي لأي مستخدم ولأي تطبيق من الـ 3 تطبيقات
     */
    public function sendNotification($user, string $title, string $message, string $type, string $targetApp, array $data = []): bool
    {
        if (!$user) {
            Log::warning('Target user is null');
            return false;
        }

        // 1. جلب توكنز الأجهزة الخاصة بهذا المستخدم لهذا التطبيق تحديداً
        $tokens = $this->notificationRepository->getUserTokens($user->id, $targetApp);

        // 2. حفظ الإشعار في أرشيف قاعدة البيانات (In-App) لكي يراه المستخدم عندما يفتح صندوق الإشعارات
        $this->notificationRepository->saveToArchive($user->id, $title, $message, $type, $data);

        if (empty($tokens)) {
            // تحذير وليس info: الإشعار لم يصل فعلياً إلى الجهاز، وصل للأرشيف فقط.
            // يظهر عادةً عندما يسجّل التطبيق التوكن تحت app_type مختلف عن المستهدف.
            Log::warning("FCM: user {$user->id} has no tokens for app '{$targetApp}' — archived only, no push sent.");
            return false;
        }

        // 3. بناء اتصال الفايربيز المخصص لهذا التطبيق
        $messaging = $this->getMessagingInstance($targetApp);
        if (!$messaging) {
            return false;
        }

        // 4. إرسال الإشعار لكل الأجهزة المرتبطة بحساب المستخدم في هذا التطبيق
        $success = false;
        foreach ($tokens as $token) {
            try {
                $cloudMessage = CloudMessage::withTarget('token', $token)
                    ->withNotification([
                        'title' => $title,
                        'body'  => $message,
                    ])
                    ->withData(array_merge([
                        'type' => $type
                    ], $data));

                $messaging->send($cloudMessage);
                $success = true;
            } catch (Exception $e) {

                Log::error(
                    "FCM Send Error for token {$token}: "
                    . $e->getMessage()
                );

                if (
                str_contains(
                    strtolower($e->getMessage()),
                    'registration-token-not-registered'
                )
                ) {

                    DB::table('user_fcm_tokens')
                        ->where('fcm_token', $token)
                        ->delete();

                    Log::info(
                        "Dead FCM token deleted: {$token}"
                    );
                }
            }
        }

        return $success;
    }
}
