<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\CourseRequest;
use App\Services\CourseService;
use Illuminate\Http\Request;

class courseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $CourseService;

    public function __construct(CourseService $CourseService)
    {
        $this->CourseService = $CourseService;
    }
    public function index()
    {
        $data=$this->CourseService->index();
        if($data['code']===200){
            return ResponseHelper::Success($data['user'], $data['message'], $data['code']);
        } else {
            return ResponseHelper::Error($data['user'], $data['message'], $data['code']);
        }
    }
    public function show($id )
    {
        $data=$this->CourseService->show($id);
        if($data['code']===200){
            return ResponseHelper::Success($data['user'], $data['message'], $data['code']);
        } else {
            return ResponseHelper::Error($data['user'], $data['message'], $data['code']);
        }
    }


    public function create(CourseRequest $request)
    {
        $data=$this->CourseService->create($request);
        if($data['code']===200){
            return ResponseHelper::Success($data['user'], $data['message'], $data['code']);
        } else {
            return ResponseHelper::Error($data['user'], $data['message'], $data['code']);
        }

    }
    public function store( $id)
    {
        $data=$this->CourseService->store($id);
        if($data['code']===200){
            return ResponseHelper::Success($data['user'], $data['message'], $data['code']);
        } else {
            return ResponseHelper::Error($data['user'], $data['message'], $data['code']);
        }
    }
    public function showMyCourses( )
    {
        $data=$this->CourseService->showMyCourses();
        if($data['code']===200){
            return ResponseHelper::Success($data['user'], $data['message'], $data['code']);
        } else {
            return ResponseHelper::Error($data['user'], $data['message'], $data['code']);
        }
    }

    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
