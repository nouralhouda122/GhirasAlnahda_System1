<?php


namespace App\Http\Controllers;


use App\Helpers\ResponseHelper;
use App\Http\Requests\AddPermission;
use App\Http\Requests\SearchForPermissionsAndRolesRequest;
use App\Services\PermissionServices;
use App\Services\RoleService;

class PermissionController
{
    public function __construct(PermissionServices $permissionServices)
    {
        $this->permissionServices = $permissionServices;
    }

//عرض كل صلاحيات
    public function getAllPermissions( ): \Illuminate\Http\JsonResponse
    {
        $data = $this->permissionServices->getAllPermissions();
        if ($data['code'] === 200) {
            return ResponseHelper::Success($data['data'], $data['message'], $data['code']);
        } else {
            return ResponseHelper::Error($data['data'], $data['message'], $data['code']);
        }
    }

//عرض صلاحيات دور
    public function getAllPermissionsForRole($id): \Illuminate\Http\JsonResponse
    {
        $data = $this->permissionServices->getAllPermissionsForRole($id);
        if ($data['code'] === 200) {
            return ResponseHelper::Success($data['data'], $data['message'], $data['code']);
        } else {
            return ResponseHelper::Error($data['data'], $data['message'], $data['code']);
        }
    }

//اضافة صلاحية
    public function AddPermission(AddPermission $request): \Illuminate\Http\JsonResponse
    {
        $data = $this->permissionServices->AddPermission($request);
        if ($data['code'] === 200) {
            return ResponseHelper::Success($data['data'], $data['message'], $data['code']);
        } else {
            return ResponseHelper::Error($data['data'], $data['message'], $data['code']);
        }
    }

//اضافة صلاحية لدور
    public function AddPermissionToRole($permission_id, $role_id): \Illuminate\Http\JsonResponse
    {
        $data = $this->permissionServices->AddPermissionToRole($permission_id,$role_id);
        if ($data['code'] === 200) {
            return ResponseHelper::Success($data['data'], $data['message'], $data['code']);
        } else {
            return ResponseHelper::Error($data['data'], $data['message'], $data['code']);
        }
    }

//تعديل صلاحية
    public function updatePermission(AddPermission $request, $id): \Illuminate\Http\JsonResponse
    {
        $data = $this->permissionServices->updatePermission($request,$id);
        if ($data['code'] === 200) {
            return ResponseHelper::Success($data['data'], $data['message'], $data['code']);
        } else {
            return ResponseHelper::Error($data['data'], $data['message'], $data['code']);
        }
    }

//تعديل صلاحية دور
    public function updatePermissionForRole(AddPermission $request, $role_id,$permission_id): \Illuminate\Http\JsonResponse
    {
        $data = $this->permissionServices->updatePermissionForRole($request,$role_id,$permission_id);
        if ($data['code'] === 200) {
            return ResponseHelper::Success($data['data'], $data['message'], $data['code']);
        } else {
            return ResponseHelper::Error($data['data'], $data['message'], $data['code']);
        }
    }

//حذف صلاحية
    public function DeletePermission($id): \Illuminate\Http\JsonResponse
    {
        $data = $this->permissionServices->DeletePermission($id);
        if ($data['code'] === 200) {
            return ResponseHelper::Success($data['data'], $data['message'], $data['code']);
        } else {
            return ResponseHelper::Error($data['data'], $data['message'], $data['code']);
        }
    }
    //حذف صلاحية دور
    public function deletePermissionForRole($role_id,$permission_id): \Illuminate\Http\JsonResponse
    {
        $data = $this->permissionServices->deletePermissionForRole($role_id,$permission_id);
        if ($data['code'] === 200) {
            return ResponseHelper::Success($data['data'], $data['message'], $data['code']);
        } else {
            return ResponseHelper::Error($data['data'], $data['message'], $data['code']);
        }
    }

    //بحث عن صلاحية

    public function SearchForPermissions(SearchForPermissionsAndRolesRequest $request): \Illuminate\Http\JsonResponse
    {
        $data = $this->permissionServices->search($request);
        if ($data['code'] === 200) {
            return ResponseHelper::Success($data['data'], $data['message'], $data['code']);
        } else {
            return ResponseHelper::Error($data['data'], $data['message'], $data['code']);
        }
    }

}
