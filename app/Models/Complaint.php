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
            'target_role' => 'Campaign Manager',
            'examples' => 'Technical issues, attendance issues, campaign registration issues.',
            'allow_anonymous' => false
        ],

        'level_2' => [
            'label' => 'Department Management',
            'target_role' => 'Evaluation Manager',
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

    if ($user->hasRole('Super Admin')) {
        return $status
            ? $query->where('status', $status)
            : $query;
    }

    if ($user->hasRole('Campaign Manager')) {
        $query->where('assigned_role', 'Campaign Manager');
    }

    elseif ($user->hasRole('Evaluation Manager')) {
        $query->where('assigned_role', 'Evaluation Manager');
    }

    else {
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
