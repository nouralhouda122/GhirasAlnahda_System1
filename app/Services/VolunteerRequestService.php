<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\userRepository;
use App\Repositories\VolunteerRequestRepository;
use App\Repositories\NotificationRepository; // 1. استيراد مستودع الإشعارات
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class VolunteerRequestService
{
    protected $repository;
    protected $userRepository;
    protected $notificationRepository; // 2. تعريف الخاصية

    // 3. حقن الـ NotificationRepository داخل الـ Constructor
    public function __construct(
        VolunteerRequestRepository $repository,
        userRepository $userRepository,
        NotificationRepository $notificationRepository
    ) {
        $this->repository = $repository;
        $this->userRepository = $userRepository;
        $this->notificationRepository = $notificationRepository;
    }

    /**
     * جلب كافة الطلبات مع الاسم والايميل ورابط الـ CV الكامل
     */
    public function getPendingRequests()
    {
        $requests = $this->repository->getAllPending();

        return $requests->map(function ($request) {
            return [
                'id'                    => $request->id,
                'user_name'             => $request->user->name ?? 'N/A',
                'user_email'            => $request->user->email ?? 'N/A',
                'age'                   => $request->age,
                'gender'                => $request->gender,
                'current_address'       => $request->current_address,
                'cv_url'                => asset('storage/' . $request->cv_path),
                'preferred_sector'      => $request->preferred_sector,
                'preferred_field'       => $request->preferred_field,
                'weekly_hours_capacity' => $request->weekly_hours_capacity,
                'message_title'         => $request->message_title,
                'message_content'       => $request->message_content,
                'status'                => $request->status,
                'created_at'            => $request->created_at,
            ];
        });
    }

    /**
     * جلب تفاصيل طلب واحد بشكل منظم
     */
    public function getRequestDetails($id)
    {
        $request = $this->repository->findById($id);

        if (!$request) return null;

        $request->cv_url = asset('storage/' . $request->cv_path);
        $request->user_name = $request->user->name ?? 'N/A';
        $request->user_email = $request->user->email ?? 'N/A';

        return $request;
    }

    /**
     * معالجة حالة الطلب (قبول / رفض) وإرسال إشعار فوري للمتطوع
     */
    public function processStatus($id, $status)
    {
        return DB::transaction(function () use ($id, $status) {
            // تحديث حالة طلب الانضمام
            $joinRequest = $this->repository->updateStatus($id, $status);

            if ($status === 'approved') {
                // 1. توليد كود فريد للمتطوع (مثل: GH-2026-0005)
                $idCode = 'GH-' . date('Y') . '-' . str_pad($joinRequest->user_id, 4, '0', STR_PAD_LEFT);

                // 2. إعداد مسار حفظ الـ QR Code بصيغة SVG
                $qrPath = 'qrcodes/' . $idCode . '.svg';
                $fullPath = storage_path('app/public/' . $qrPath);

                // التأكد من وجود المجلد وتوليده إذا لم يكن موجوداً
                if (!file_exists(dirname($fullPath))) {
                    mkdir(dirname($fullPath), 0755, true);
                }

                // 3. توليد المحتوى (رابط ديناميكي لفحص حضور المتطوع)
                $qrContent = url("/api/attendance/check/" . $idCode);

                // 4. توليد صورة الـ QR وحفظها في المسار المحدد
                QrCode::format('svg')
                    ->size(200)
                    ->errorCorrection('H')
                    ->generate($qrContent, $fullPath);

                // 5. إنشاء بروفايل المتطوع في قاعدة البيانات
                $this->repository->createVolunteerProfile([
                    'user_id'               => $joinRequest->user_id,
                    'volunteer_id_code'     => $idCode,
                    'qr_code_path'          => $qrPath,
                    'card_expiry_date'      => now()->addYears(2), // صلاحية البطاقة سنتين
                    'is_active'             => true,
                    'age'                   => $joinRequest->age,
                    'gender'                => $joinRequest->gender,
                    'current_address'       => $joinRequest->current_address,
                    'cv_path'               => $joinRequest->cv_path,
                    'preferred_sector'      => $joinRequest->preferred_sector,
                    'preferred_field'       => $joinRequest->preferred_field,
                    'weekly_hours_capacity' => $joinRequest->weekly_hours_capacity,
                ]);

                // 6. تعيين دور (Role) متطوع للمستخدم في النظام
                $user = $this->userRepository->getById($joinRequest->user_id);
                if ($user) {
                    $user->assignRole('Volunteer');
                }
            }

            // 🔔 7. بناء وإرسال الإشعار للمستخدم بناءً على حالة الطلب
            $this->sendVolunteerStatusNotification($joinRequest, $status);

            return $joinRequest;
        });
    }

    /**
     * دالة مساعدة لصياغة وإرسال إشعار النتيجة للمتطوع
     */
    private function sendVolunteerStatusNotification($joinRequest, string $status)
    {
        // تخصيص النصوص بناءً على الحالة
        if ($status === 'approved') {
            $title = "تهانينا! تم قبول طلب انضمامك 🌿";
            $body = "مرحباً بك في غراس النهضة، تم قبول طلبك بنجاح وأصبحت الآن متطوعاً رسمياً معنا.";
            $notificationType = "volunteer_request_approved";
        } else {
            $title = "تحديث بخصوص طلب الانضمام 📋";
            $body = "نشكر اهتمامك بالانضمام إلينا، نعتذر منك فلم يتم قبول طلبك في هذه الفترة. نتمنى لك التوفيق.";
            $notificationType = "volunteer_request_rejected";
        }

        // إرسال الإشعار وتخزينه في الأرشيف (يستهدف السجل حاملي رول volunteer)
        $this->notificationRepository->sendToUser(
            $joinRequest->user_id,
            $title,
            $body,
            $notificationType,
            [
                'request_id' => $joinRequest->id,
                'status'     => $status
            ],
            'volunteer' // تطبيق المتطوعين هو المستهدف بـ الـ FCM Token
        );
    }
}