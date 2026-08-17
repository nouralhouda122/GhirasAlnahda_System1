<?php


namespace App\Services;


use App\Http\Requests\AddPermission;
use App\Repositories\DepartmentRepository;
use App\Repositories\DepartmentRoleRepository;
use App\Repositories\EmailVerficationRepository;
use App\Repositories\RoleRepository;
use App\Repositories\userRepository;
use Spatie\Permission\Models\Role;

class RoleService
{
    protected $roleRepository;
    public function __construct(DepartmentRoleRepository $departmentRoleRepository,
                                roleRepository $roleRepository, DepartmentRepository $departmentRepository)
    {
        $this->roleRepository = $roleRepository;
        $this->departmentRepository = $departmentRepository;
        $this->departmentRoleRepository = $departmentRoleRepository;


    }

    public function getRoleNames()
    {
        $roles = $this->roleRepository->getNames();

        if ($roles->isEmpty()) {
            return [
                'data' => [],
                'message' => 'No roles found',
                'code' => 200
            ];
        }

        return [
            'data' => $roles,
            'message' => 'Roles fetched successfully',
            'code' => 200
        ];
    }

    public function DeleteRole($id)
    {
        $role=$this->roleRepository->findById($id);
        if(!$role){
            return [
                'data' => null,
                'message' => 'the Role not found',
                'code' => 404
            ];

        }
        $this->roleRepository->DeleteRole($role);
        return [
            'data' => null,
            'message' => ' the Role deleted successfully',
            'code' => 200
        ];

}

    public function updateRole($request,$id)
    {
        $role=$this->roleRepository->findById($id);
        if(!$role){
            return [
                'data' => null,
                'message' => 'the Role not found',
                'code' => 404
            ];

        }
        $this->roleRepository->updateRole([
            'name'=>$request->name]
        ,$role);
        return [
            'data' => $role,
            'message' => ' the Role updated successfully',
            'code' => 200
        ];

    }

    public function getAll()
    {
        $roles=$this->roleRepository->getAll();
        return [
            'data' => $roles,
            'message' => 'Roles fetched successfully',
            'code' => 200
        ];
    }

    public function AddRole( $request)
    {
      $role=  $this->roleRepository->create([

         'name' =>  $request->name,
                'guard_name'=>'web',
            ]
        );
        return [
            'data' => $role,
            'message' => ' the Role Added successfully',
            'code' => 201
        ];


    }

    public function search(\App\Http\Requests\SearchForPermissionsAndRolesRequest $request)
    {
        $roles = $this->roleRepository->search($request);
        return [
            'data' =>$roles,
            'meta' => [
                'current_page' => $roles->currentPage(),
                'last_page' => $roles->lastPage(),
                'per_page' => $roles->perPage(),
                'total' => $roles->total(),
            ],
            'message' => 'permissions retrieved successfully',
            'code' => 200
        ];
    }


    public function addRoleForDepartment($request): array
    {
        foreach ($request->role_ids as $roleId) {

            $this->departmentRoleRepository->create([

                'department_id' => $request->department_id,

                'role_id' => $roleId,

            ]);
        }

        return [

            'data' => [],

            'message' => 'Roles assigned to department successfully.',

            'code' => 200
        ];
    }
    public function showRoleForDepartment(int $departmentId): array
    {
        $department = $this->departmentRepository->find($departmentId);

        if (!$department) {
            return [
                'data' => null,
                'message' => 'Department not found',
                'code' => 404
            ];
        }

        $roles = $this->departmentRoleRepository
            ->getByDepartment($departmentId);

        return [
            'data' => $roles,
            'message' => 'Roles retrieved successfully',
            'code' => 200
        ];
    }}
