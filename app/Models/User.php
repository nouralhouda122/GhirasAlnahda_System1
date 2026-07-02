<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded=[];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    public function volunteerProfile()
    {
        return $this->hasOne(VolunteerProfile::class);
    }
    public function Instructor_profile()
    {
        return $this->hasOne(instructor_profile::class);
    }
    public function JoinRequests()
    {
        return $this->hasMany(JoinRequest::class);
    }
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }
    public function managedDepartment() {
        return $this->hasOne(Department::class, 'manager_id');
    }
    public function specializations()
    {
        return $this->belongsToMany(Specialization::class, 'instructor_specializations');
    }
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'volunteer_id');
    }
    public function receivedPoints()
    {
        return $this->hasMany(PointTransaction::class, 'volunteer_id');
    }

    public function givenPoints()
    {
        return $this->hasMany(PointTransaction::class, 'awarded_by');
    }
    public function myCourses()
    {
        return $this->belongsToMany(Course::class, 'enrollments')
            ->withPivot('status', 'enrolled_at')
            ->withTimestamps();
    }

    public function hasPermission($permissionName)
    {
        return $this->departmentRole
            ->permissions()
            ->where('name', $permissionName)
            ->exists();
    }
    public function departmentRole()
    {
        return $this->belongsTo(DepartmentRole::class);
    }
    /**
     * جلب جميع توكنات الأجهزة الخاصة بالمستخدم (تعدد الأجهزة)
     */
    public function fcmTokens()
    {
        return $this->hasMany(UserFcmToken::class, 'user_id');
    }
}
