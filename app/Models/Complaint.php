<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Complaint extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'is_anonymous' => 'boolean',
    ];

    public static function getSensitivityMetaData(): array
    {
        return [
            'level_1' => [
                'label' => 'Level 1 — General Support',
                'target_role' => 'support_team',
                'examples' => 'Technical issues, course registration problems, attendance inquiries.',
                'allow_anonymous' => false
            ],
            'level_2' => [
                'label' => 'Level 2 — Department Management',
                'target_role' => 'department_manager',
                'examples' => 'Problems with supervisors, unfair evaluation, internal department conflicts.',
                'allow_anonymous' => false
            ],
            'level_3' => [
                'label' => 'Level 3 — Confidential',
                'target_role' => 'general_manager',
                'examples' => 'Harassment, abuse of authority, threats, privacy violations.',
                'allow_anonymous' => true
            ]
        ];
    }

    public function scopeWithControlPermission($query, ?string $status = null)
    {
        /** @var \App\Models\User|\Illuminate\Contracts\Auth\Authenticatable $user */
        $user = auth()->user();

        // دمج الشروط لحذف الـ Empty Body وتأمين الاستعلام بناءً على الصلاحيات
        if ($user && !$user->hasPermissionTo('resolve.complaint')) {
            if ($user->hasPermissionTo('create.complaint') && $user->hasPermissionTo('view.complaint')) {
                $query->whereIn('sensitivity_level', ['level_1', 'level_2']);
            } else {
                $query->where('user_id', $user->id)->where('is_anonymous', false);
            }
        }

        if ($status) {
            $query->where('status', $status);
        }

        return $query;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }
}
