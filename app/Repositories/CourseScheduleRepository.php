<?php
namespace App\Repositories;
use App\Models\Course_schedule;
class CourseScheduleRepository
{
    public function create(array $data)
    {
        return Course_schedule::create([
            'course_id' => $data['course_id'],
            'day' => $data['day'],
            'from_time' => $data['from_time'],
            'to_time' => $data['to_time'],
        ]);
    }

}
