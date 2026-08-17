<?php


namespace App\Http\Controllers;


use App\Helpers\ResponseHelper;
use App\Http\Requests\AddPermission;
use App\Http\Requests\AddRoleForDepartment;
use App\Http\Requests\AddRoleRequest;
use App\Http\Requests\SearchForPermissionsAndRolesRequest;
use App\Services\RoleService;

class RoleController
{

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }
//عرض الادوار
    public function getAllRoles(): \Illuminate\Http\JsonResponse
    {
        $data = $this->roleService->getAll();
        if ($data['code'] === 200) {
            return ResponseHelper::Success($data['data'], $data['message'], $data['code']);
        } else {
            return ResponseHelper::Error($data['data'], $data['message'], $data['code']);
        }
}
//اضافة دور
    public function AddRole(AddRoleRequest $request): \Illuminate\Http\JsonResponse
    {
        $data = $this->roleService->AddRole($request);
        if ($data['code'] === 200) {
            return ResponseHelper::Success($data['data'], $data['message'], $data['code']);
        } else {
            return ResponseHelper::Error($data['data'], $data['message'], $data['code']);
        }
    }
//عرض ادوار قسم معين
    public function getAllRolesForDepartment( $id): \Illuminate\Http\JsonResponse
    {
        $data = $this->roleService->getAllRolesForDepartment($id);
        if ($data['code'] === 200) {
            return ResponseHelper::Success($data['data'], $data['message'], $data['code']);
        } else {
            return ResponseHelper::Error($data['data'], $data['message'], $data['code']);
        }
    }

//اضافة ادوار لقسم معين
    public function AddRoleForDepartment(AddRoleForDepartment  $request): \Illuminate\Http\JsonResponse
    {
        $data = $this->roleService->AddRoleForDepartment($request);
        if ($data['code'] === 200) {
            return ResponseHelper::Success($data['data'], $data['message'], $data['code']);
        } else {
            return ResponseHelper::Error($data['data'], $data['message'], $data['code']);
        }
    }


//تعديل دور
    public function updateRole(AddPermission $request, $id): \Illuminate\Http\JsonResponse
    {
        $data = $this->roleService->updateRole($request,$id);
        if ($data['code'] === 200) {
            return ResponseHelper::Success($data['data'], $data['message'], $data['code']);
        } else {
            return ResponseHelper::Error($data['data'], $data['message'], $data['code']);
        }
    }
//حذف دور
    public function DeleteRole($id): \Illuminate\Http\JsonResponse
    {
        $data = $this->roleService->DeleteRole($id);
        if ($data['code'] === 200) {
            return ResponseHelper::Success($data['data'], $data['message'], $data['code']);
        } else {
            return ResponseHelper::Error($data['data'], $data['message'], $data['code']);
        }
    }

    public function SearchForRoles(SearchForPermissionsAndRolesRequest $request): \Illuminate\Http\JsonResponse
    {
        $data = $this->roleService->Search($request);
        if ($data['code'] === 200) {
            return ResponseHelper::Success($data['data'], $data['message'], $data['code']);
        } else {
            return ResponseHelper::Error($data['data'], $data['message'], $data['code']);
        }
    }
//عرض ادوار قسم معين
    public function showRoleForDepartment($department_id): \Illuminate\Http\JsonResponse
    {
        $data = $this->roleService->showRoleForDepartment($department_id);
        if ($data['code'] === 200) {
            return ResponseHelper::Success($data['data'], $data['message'], $data['code']);
        } else {
            return ResponseHelper::Error($data['data'], $data['message'], $data['code']);
        }
    }


}
