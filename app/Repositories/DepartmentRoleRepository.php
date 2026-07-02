<?php

namespace App\Repositories;

use App\Models\DepartmentRole;

class DepartmentRoleRepository
{
    public function create(array $data): DepartmentRole
    {
        return DepartmentRole::create($data);
    }
    public function getPermissionsByDepartmentRoleId(int $departmentRoleId)
    {
        $departmentRole = DepartmentRole::with('permissions:id,name,guard_name')
            ->find($departmentRoleId);

        return $departmentRole ? $departmentRole->permissions : collect();}
    public function firstOrCreate(array $attributes): DepartmentRole
    {
        return DepartmentRole::firstOrCreate($attributes);
    }
    public function findByDepartmentAndRole($department_id,$role_id)
    {
        return DepartmentRole::where('department_id', $department_id)
            ->where('role_id', $role_id)
            ->first();
    }
    public function exists(
        int $departmentId,
        int $roleId
    ): bool {

        return DepartmentRole::where(
            'department_id',
            $departmentId
        )
            ->where(
                'role_id',
                $roleId
            )
            ->exists();
    }

    public function getByDepartment(int $departmentId)
    {
        return DepartmentRole::with('role')
            ->where('department_id', $departmentId)
            ->get()
            ->pluck('role');
    }
    public function delete(
        int $departmentRoleId
    ): bool {

        return DepartmentRole::where(
            'id',
            $departmentRoleId
        )->delete();
    }

    public function findById(
        int $department_role_id,
    ) {

        return DepartmentRole::where(
            'id',
            $department_role_id
        )
            ->first();
    }


}
