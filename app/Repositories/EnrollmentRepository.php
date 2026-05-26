<?php
namespace App\Repositories;
use App\Models\Enrollment;



class EnrollmentRepository
{
    public function create(array $data)
    {
        return Enrollment::create([
            'course_id' => $data['course_id'],
            'user_id' => $data['user_id'],
            'status' => $data['status'] ?? '',
        ]);
    }
    public  function alreadyEnrolled($user_id,$course_id)
{
    return Enrollment::where('user_id',$user_id)->where('course_id',$course_id)->exists();
}

}

