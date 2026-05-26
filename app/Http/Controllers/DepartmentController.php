<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\DepartmentRequest;
use App\Models\Department;
use App\Services\CampaignService;
use App\Services\DepartmentService;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    private DepartmentService $departmentService;

    public function __construct(DepartmentService $departmentService)
    {
        $this->departmentService = $departmentService;
    }
    public function store(DepartmentRequest $request)
    {
        $data = $this->departmentService->store($request->validated());

        if ($data['code'] === 200) {
            return ResponseHelper::Success($data['data'], $data['message'], $data['code']);
        } else {
            return ResponseHelper::Error($data['data'], $data['message'], $data['code']);
        }
    }
    public function index()
    {
        $data = $this->departmentService->index();

        if ($data['code'] === 201) {
            return ResponseHelper::Success($data['data'], $data['message'], $data['code']);
        } else {
            return ResponseHelper::Error($data['data'], $data['message'], $data['code']);
        }
    }}
