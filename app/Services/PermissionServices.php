<?php


namespace App\Services;


use App\Http\Requests\searchUserRequest;
use App\Http\Resources\UserResource;
use App\Repositories\DepartmentRolePermissionRepository;
use App\Repositories\DepartmentRoleRepository;
use App\Repositories\PermissionRepository;
use App\Repositories\RoleRepository;

class PermissionServices
{
    public function __construct(DepartmentRolePermissionRepository $departmentRolePermissionRepository,
                                DepartmentRoleRepository $departmentRoleRepository, PermissionRepository $permissionRepository, RoleRepository $roleRepository)
    {
        $this->permissionRepository = $permissionRepository;
        $this->roleRepository = $roleRepository;
        $this->departmentRoleRepository = $departmentRoleRepository;
        $this->departmentRolePermissionRepository = $departmentRolePermissionRepository;

    }


    public function getAllPermissions()
    {

      $permissions= $this->permissionRepository->getAll();
        return [
            'data' => $permissions,
            'message' => ' the Permissions  successfully',
            'code' => 200
        ];
    }
    public function getAllPermissionsForRoleInDepartment($departmentId, $roleId): array
    {
        $departmentRole = $this->departmentRoleRepository
            ->findByDepartmentAndRole($departmentId, $roleId);

        if (!$departmentRole) {
            return [
                'data' => null,
                'message' => 'This role is not assigned to the selected department',
                'code' => 404
            ];
        }

        $permissions = $this->departmentRoleRepository
            ->getPermissionsByDepartmentRoleId($departmentRole->id);

        return [
            'data' => $permissions,
            'message' => 'Permissions retrieved successfully',
            'code' => 200
        ];
    }    public function AddPermission(\App\Http\Requests\AddPermission $request)
    {
        $permission=  $this->permissionRepository->create([
                'name' =>  $request->name,
                'guard_name'=>'web',
            ]
        );
        return [
            'data' => $permission,
            'message' => ' the Permission Added successfully',
            'code' => 201
        ];


    }

    public function addPermissionsToDepartmentRole($request)
    {
        $departmentRole = $this->departmentRoleRepository
            ->findByDepartmentAndRole($request->department_id, $request->role_id);

        if (!$departmentRole) {
            return [
                'data' => null,
                'message' => 'This role is not assigned to the selected department',
                'code' => 404
            ];
        }       $permissionIds = $request->permission_ids;

        foreach ($permissionIds as $permissionId) {

            $permission = $this->permissionRepository
                ->findById($permissionId);

            if (!$permission) {

                return [
                    'data' => null,
                    'message' => "Permission {$permissionId} not found",
                    'code' => 404
                ];
            }
        }

        $departmentRole->permissions()
            ->syncWithoutDetaching($permissionIds);

        return [
            'data' => $departmentRole->permissions()->get(),
            'message' => 'Permissions added successfully',
            'code' => 200
        ];
    }
    public function updatePermission(\App\Http\Requests\AddPermission $request, $id)
    {
        $permission=$this->permissionRepository->findById($id);
        if(!$permission){
            return [
                'data' => null,
                'message' => 'the permission not found',
                'code' => 404
            ];

        }
        $this->permissionRepository->updateRole([
                'name'=>$request->name]
            ,$permission);
        return [
            'data' => $permission,
            'message' => ' the permission updated successfully',
            'code' => 200
        ];

    }





    public function DeletePermission($id)
    {
        $permission=$this->permissionRepository->findById($id);
        if(!$permission){
            return [
                'data' => null,
                'message' => 'the permission not found',
                'code' => 404
            ];

        }
        $this->permissionRepository->delete($permission);
        return [
            'data' => $permission,
            'message' => ' the permission deleted successfully',
            'code' => 200
        ];

    }

    public function deletePermissionForRole($department_id, $role_id, $permission_id)
    {
        $departmentRole = $this->departmentRoleRepository
            ->findByDepartmentAndRole($department_id, $role_id);

        if (!$departmentRole) {
            return [
                'data' => null,
                'message' => 'This role is not assigned to the selected department',
                'code' => 404
            ];
        }

        $permission = $this->departmentRolePermissionRepository
            ->findById($departmentRole, $permission_id);

        if (!$permission) {
            return [
                'data' => null,
                'message' => 'Permission not found',
                'code' => 404
            ];
        }

        $permission->delete();

        return [
            'data' => null,
            'message' => 'The permission deleted successfully',
            'code' => 200
        ];
    }
    public function search(\App\Http\Requests\SearchForPermissionsAndRolesRequest $request)
    {
        $permissions = $this->permissionRepository->search($request);
        return [
            'data' =>$permissions,
            'meta' => [
                'current_page' => $permissions->currentPage(),
                'last_page' => $permissions->lastPage(),
                'per_page' => $permissions->perPage(),
                'total' => $permissions->total(),
            ],
            'message' => 'permissions retrieved successfully',
            'code' => 200
        ];
    }





}
