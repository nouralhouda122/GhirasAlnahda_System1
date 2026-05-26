<?php


namespace App\Services;


use App\Http\Requests\searchUserRequest;
use App\Http\Resources\UserResource;
use App\Repositories\PermissionRepository;
use App\Repositories\RoleRepository;

class PermissionServices
{
    public function __construct(PermissionRepository $permissionRepository,RoleRepository $roleRepository)
    {
        $this->permissionRepository = $permissionRepository;
        $this->roleRepository = $roleRepository;

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
    public function getAllPermissionsForRole($id)
    {
        $role=$this->roleRepository->findById($id);
        if(!$role){
            return [
                'data' => null,
                'message' => 'the Role not found',
                'code' => 404
            ];

        }
      $permissions=  $role->permissions;
        return [
            'data' => $permissions,
            'message' => ' the permissions  successfully',
            'code' => 200
        ];


    }
    public function AddPermission(\App\Http\Requests\AddPermission $request)
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

    public function AddPermissionToRole($permission_id, $role_id)
    {
        $role=$this->roleRepository->findById($role_id);
        if(!$role){
            return [
                'data' => null,
                'message' => 'the Role not found',
                'code' => 404
            ];

        }
        $permission=$this->permissionRepository->findById($permission_id);
        if(!$permission){
            return [
                'data' => null,
                'message' => 'the permission not found',
                'code' => 404
            ];

        }
        $role->givePermissionTo($permission);
        return [
            'data' => $role->permissions,
            'message' => ' the Permissions ِAdded successfully',
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




    public function updatePermissionForRole($request,$role_id,$permission_id)
    {
        $role=$this->roleRepository->findById($role_id);
        if(!$role){
            return [
                'data' => null,
                'message' => 'the Role not found',
                'code' => 404
            ];

        }
        $permission=$this->permissionRepository->findById($permission_id);
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

    public function deletePermissionForRole($role_id, $permission_id)
    {
        $role=$this->roleRepository->findById($role_id);
        if(!$role){
            return [
                'data' => null,
                'message' => 'the Role not found',
                'code' => 404
            ];

        }
        $permission=$this->permissionRepository->findById($permission_id);
        if(!$permission){
            return [
                'data' => null,
                'message' => 'the permission not found',
                'code' => 404
            ];

        }
        $role->revokePermissionTo($permission);
        return [
            'data' => $permission,
            'message' => ' the permission deleted successfully',
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
