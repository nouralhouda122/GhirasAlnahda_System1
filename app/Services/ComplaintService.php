<?php

namespace App\Services;

use App\Repositories\ComplaintRepository;
use App\Models\Complaint;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class ComplaintService
{
    protected ComplaintRepository $complaintRepository;
    protected FcmNotificationService $fcmNotificationService;

    public function __construct(
        ComplaintRepository $complaintRepository,
        FcmNotificationService $fcmNotificationService
    ) {
        $this->complaintRepository = $complaintRepository;
        $this->fcmNotificationService = $fcmNotificationService;
    }

    public function getAllComplaints(): Collection
    {
        return $this->complaintRepository->getFilteredComplaints()->map(function ($complaint) {
            return $this->formatAttachmentUrl($complaint);
        });
    }

    public function storeComplaint(array $data, bool $hasFile, $file): Complaint
    {
        // 1. تطبيق سياسة السرية والهوية المجهولة
        $data['user_id'] = ($data['is_anonymous'] ?? false) ? null : auth()->id();

        // 2. التوجيه التلقائي للدور المسؤول بناءً على مستوى الحساسية الجديد
        $metaData = Complaint::getSensitivityMetaData();
        $data['assigned_role'] = $metaData[$data['sensitivity_level']]['target_role'];

        // 3. معالجة الملف المرفق
        if ($hasFile && $file) {
            $data['attachment_path'] = $file->store('complaints/attachments', 'public');
        }

        unset($data['attachment']);

        $complaint = $this->complaintRepository->create($data);
        $complaintWithDefaults = $complaint->fresh();

        // 4. إشعار المستخدمين الذين يمتلكون الدور المسند للشكوى
        $targetUsers = User::role($data['assigned_role'])->get();

        if ($targetUsers->isEmpty()) {
            // بدون هذا التحذير تمرّ الحالة بصمت تام: لا مستخدم بالدور المستهدف
            // يعني أن الحلقة أدناه لا تُنفَّذ ولا يصل أي إشعار.
            Log::warning("Complaint {$complaint->id}: no users hold role '{$data['assigned_role']}' — no notifications sent.");
        }

        foreach ($targetUsers as $user) {
            // 'admin' مخصص لمشروع ghiras-dash (السوبر أدمن فقط).
            // بقية الأدوار — بما فيها Manager — تستخدم تطبيق الإدارة finalproject-d4cd4.
            $appType = $data['assigned_role'] === 'Super Admin' ? 'admin' : 'manager';

            $this->fcmNotificationService->sendNotification(
                $user,
                'شكوى جديدة',
                'تم إرسال شكوى جديدة تحتاج إلى مراجعتك',
                'new_complaint',
                $appType,
                [
                    'complaint_id' => (string)$complaint->id,
                    'sensitivity' => $data['sensitivity_level']
                ]
            );
        }

        return $this->formatAttachmentUrl($complaintWithDefaults);
    }

    public function processReview(int $id, array $data): Complaint
    {
        $complaint = $this->complaintRepository->findById($id);

        if (!$complaint) {
            throw new \Exception('Complaint not found or unauthorized', 404);
        }

        if (
            $complaint->assigned_user_id &&
            $complaint->assigned_user_id != auth()->id()
        ) {
            throw new \Exception('Complaint already assigned to another employee.', 403);
        }

        $updateData = [
            'status' => $data['status'],
            'admin_reply' => $data['admin_reply'] ?? $complaint->admin_reply,
            'assigned_user_id' => auth()->id(),
        ];

        $this->complaintRepository->update($complaint, $updateData);

        return $this->formatAttachmentUrl($complaint->fresh());
    }

    private function formatAttachmentUrl(Complaint $complaint): Complaint
    {
        if ($complaint->attachment_path && !str_starts_with($complaint->attachment_path, 'http')) {
            $complaint->attachment_path = asset('storage/' . $complaint->attachment_path);
        }
        return $complaint;
    }

    public function getComplaintById(int $id): Complaint
    {
        $complaint = $this->complaintRepository->findById($id);

        if (!$complaint) {
            throw new \Exception('Complaint not found', 404);
        }

        return $this->formatAttachmentUrl($complaint->fresh());
    }

    public function filterComplaints(?string $status, ?string $sensitivity)
    {
        $query = $this->complaintRepository->queryFiltered();

        $map = [
            1 => 'level_1',
            2 => 'level_2',
            3 => 'level_3',
        ];

        if ($status) {
            $query->where('status', $status);
        }

        if ($sensitivity) {
            $query->where(
                'sensitivity_level',
                $map[$sensitivity] ?? $sensitivity
            );
        }

        return $query->latest()->get()->map(
            fn($c) => $this->formatAttachmentUrl($c)
        );
    }
}