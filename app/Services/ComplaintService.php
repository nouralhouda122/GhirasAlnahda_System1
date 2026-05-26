<?php

namespace App\Services;

use App\Repositories\ComplaintRepository;
use App\Models\Complaint;
use Illuminate\Database\Eloquent\Collection;

class ComplaintService
{
    protected ComplaintRepository $complaintRepository;

    public function __construct(ComplaintRepository $complaintRepository)
    {
        $this->complaintRepository = $complaintRepository;
    }

    public function getAllComplaints(): Collection
    {
        return $this->complaintRepository->getFilteredComplaints()->map(function ($complaint) {
            return $this->formatAttachmentUrl($complaint);
        });
    }

    public function storeComplaint(array $data, $hasFile, $file): Complaint
    {
        // 1. تطبيق سياسة غراس للسرية والهوية المجهولة
        $data['user_id'] = ($data['is_anonymous'] ?? false) ? null : auth()->id();

        // 2. التوجيه التلقائي للدور المسؤول بناءً على الحساسية
        $metaData = Complaint::getSensitivityMetaData();
        $data['assigned_role'] = $metaData[$data['sensitivity_level']]['target_role'];

        // 3. معالجة رفع الملف المرفق
        if ($hasFile) {
            $data['attachment_path'] = $file->store('complaints/attachments', 'public');
        }

        // إزالة الكائن الخام للملف قبل الإرسال للمستودع
        unset($data['attachment']);

        $complaint = $this->complaintRepository->create($data);


        $complaintWithDefaults = $complaint->fresh();

        return $this->formatAttachmentUrl($complaintWithDefaults);

    }

    public function processReview(int $id, array $data): Complaint
    {
        $complaint = $this->complaintRepository->findById($id);

        if (!$complaint) {
            throw new \Exception('Complaint not found or unauthorized', 404);
        }

        // عند مباشرة العمل، نربط الشكوى بالموظف الحالي لمنع التضارب وضبط الحالة
        $updateData = [
            'status' => $data['status'],
            'admin_reply' => $data['admin_reply'] ?? $complaint->admin_reply,
            'assigned_user_id' => auth()->id() // الموظف الذي يباشر الحل والرد حالياً
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
}
