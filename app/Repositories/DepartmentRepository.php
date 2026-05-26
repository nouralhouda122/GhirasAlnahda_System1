<?php


namespace App\Repositories;


use App\Models\Department;

class DepartmentRepository
{

    public function getAll()
    {
        return  Department::all();

    }
    public function find($id)
    {
        return  Department::find($id);

    }

    public function create($data)
    {
        return Department::create($data);
    }}
