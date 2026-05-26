<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;
    protected $guarded=[];
    public function skills()
    {
        return $this->hasMany(Course_skill::class,'course_id');
    }
    public function schedules()
    {
        return $this->hasMany(Course_schedule::class,'course_id');
    }
    public function instructor(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }
    public function approvals()
    {
        return $this->morphMany(ApprovalRequest::class, 'approvable');
    }
}
