<?php

namespace App\Repositories;

use App\Models\DepartmentRole;
use App\Models\DepartmentRolePermission;

class DepartmentRolePermissionRepository
{
    public function delete(
        int $permission_id
    ): bool {

        return DepartmentRolePermission::where(
            'id',
            $permission_id
        )->delete();
    }

    public function findById(
        $departmentRole,$permission_id
    ) {

        return DepartmentRolePermission::where('department_role_id', $departmentRole)
            ->where('permission_id',$permission_id)
            ->first();
    }

    public function create(array $array)
    {

    }


}
