<?php


namespace App\Services;


use App\Http\Resources\DepartmentResourses;
use App\Repositories\CampaingRepository;
use App\Repositories\DepartmentRepository;
use App\Repositories\userRepository;

class DepartmentService
{
    public function __construct(DepartmentRepository $departmentRepository)
    {
        $this->departmentRepository = $departmentRepository;
    }

    public function index()
    {
        $departments =$this->departmentRepository->getAll();

        if ($departments->isEmpty()) {
            return [
                'data' => [],
                'message' => 'No departments found',
                'code' => 404
            ];
        }

        return [
            'data' =>  DepartmentResourses::collection($departments),
            'message' => 'Departments retrieved successfully',
            'code' => 200
        ];
    }

    public function store($validated)
    {
        $department = $this->departmentRepository->create($validated);

        return [
            'data' => $department,
            'message' => 'The department created successfully',
            'code' => 201
        ];
    }}
