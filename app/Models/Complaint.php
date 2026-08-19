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
                'label' => 'General Support',
                'target_role' => 'Manager',
                'examples' => 'Technical issues, attendance issues, campaign registration issues.',
                'allow_anonymous' => false
            ],

            'level_2' => [
                'label' => 'Department Management',
                'target_role' => 'Volunteer Manager',
                'examples' => 'Supervisor issues, evaluation disputes, internal department conflicts.',
                'allow_anonymous' => false
            ],

            'level_3' => [
                'label' => 'Confidential',
                'target_role' => 'Super Admin',
                'examples' => 'Harassment, abuse of authority, threats, privacy violations.',
                'allow_anonymous' => true
            ]
        ];
    }

    public function scopeWithControlPermission($query, ?string $status = null)
    {
        $user = auth()->user();

        if (!$user) {
            return $query->whereRaw('1=0');
        }

        // السوبر أدمن يشاهد كافة الشكاوى
        if ($user->hasRole('Super Admin')) {
            return $status ? $query->where('status', $status) : $query;
        }

        // مدراء الأقسام يستعرضون الشكاوى الموجهة لدورهم فقط
        if ($user->hasRole('Manager')) {
            $query->where('assigned_role', 'Manager');
        }
        elseif ($user->hasRole('Volunteer Manager')) {
            $query->where('assigned_role', 'Volunteer Manager');
        }
        else {
            // باقي المستخدمين يرون شكاويهم الشخصية فقط
            $query->where('user_id', $user->id);
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
