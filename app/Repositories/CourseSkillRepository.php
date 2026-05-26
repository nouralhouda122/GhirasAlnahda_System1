<?php


namespace App\Repositories;


use App\Models\Course_skill;

class CourseSkillRepository
{
    public function create(array $data)
    {
        return Course_skill::create([
            'course_id' => $data['course_id'],
            'skill_name' => $data['skill_name'],
        ]);
    }

}
