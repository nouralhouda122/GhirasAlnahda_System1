<?php


namespace App\Services;


use App\Repositories\DepartmentRepository;
use App\Repositories\EmailVerficationRepository;
use App\Repositories\RoleRepository;
use App\Repositories\userRepository;
use Spatie\Permission\Models\Role;

class RoleService
{
    protected $roleRepository;
    public function __construct(RoleRepository $roleRepository,DepartmentRepository $departmentRepository)
    {
        $this->roleRepository = $roleRepository;
        $this->departmentRepository = $departmentRepository;

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

    public function AddRole(\App\Http\Requests\AddPermission $request)
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

    public function getAllRolesForDepartment($id)
    {
        $departemnt=$this->departmentRepository->find($id);
        if(!$departemnt){
            return [
                'data' => null,
                'message' => 'the Department not found',
                'code' => 404
            ];
        }
       $role= $this->roleRepository->getRoleByDepartment($departemnt->id);
        return [
            'data' => $role,
            'message' => ' the Role  successfully',
            'code' => 200
        ];

    }

    public function getAlld()
    {
        $roles=$this->roleRepository->getAll();
        return [
            'data' => $roles,
            'message' => 'Roles fetched successfully',
            'code' => 200
        ];
    }
    public function AddRoleForDepartment($request, $department_id)
    {
        $department = $this->departmentRepository->find($department_id);

        if (!$department) {
            return [
                'data' => null,
                'message' => 'the Department not found',
                'code' => 404
            ];
        }

        $role = $this->roleRepository->create([
            'name' => $request->name,
            'department_id' => $department->id,
            'guard_name' => 'web',
        ]);

        return [
            'data' => $role,
            'message' => 'the role added successfully',
            'code' => 200
        ];
    }

    public function showRoleForDepartment($department_id)
    {
        $department = $this->departmentRepository->find($department_id);

        if (!$department) {
            return [
                'data' => null,
                'message' => 'the Department not found',
                'code' => 404
            ];
        }

        $role = $this->roleRepository->getRoleByDepartment($department->id,
        );

        return [
            'data' => $role,
            'message' => '  success',
            'code' => 200
        ];

    }
}
