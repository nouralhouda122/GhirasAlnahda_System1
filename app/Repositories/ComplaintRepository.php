<?php

namespace App\Repositories;

use App\Models\Complaint;
use Illuminate\Database\Eloquent\Collection;

class ComplaintRepository
{
    /**
     * جلب الشكاوى المفلترة تلقائياً بناءً على صلاحيات المستخدم الحالي
     */
    public function getFilteredComplaints(): Collection
    {
        return Complaint::withControlPermission()
            ->with('user:id,name,email')
            ->latest()
            ->get();
    }

    /**
     * حفظ الشكوى في قاعدة البيانات
     */
    public function create(array $data): Complaint
    {
        return Complaint::create($data);
    }

    /**
     * جلب شكوى محددة مع علاقاتها
     */
    public function findById(int $id): ?Complaint
    {
        return Complaint::withControlPermission()->find($id);
    }

    /**
     * تحديث بيانات الشكوى (الرد، الحالة، تعيين الموظف)
     */
    public function update(Complaint $complaint, array $data): bool
    {
        return $complaint->update($data);
    }
}
