<?php
namespace App\Repositories;
use App\Models\Course;
class CourseRepository
{
    public function create(array $data,$instructor_id)
    {
        return Course::create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'duration_hours' => $data['duration_hours'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'] ?? null,
            'cost' => $data['cost'],
            'required_points' => $data['required_points'],

            'instructor_id' => $instructor_id ?? null,
        ]);
    }

    public function indexAll()
    {
        return Course::all();
    }

    public function findById($id)
    {
        return Course::find($id);
    }

    public function store($id)
    {

    }

    public function enrollment($id)
    {
    }
}
