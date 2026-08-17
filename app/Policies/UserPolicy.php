<?php

// app/Policies/UserPolicy.php

namespace App\Policies;

use App\Models\User;
use Spatie\Permission\Models\Role;

class UserPolicy
{
    public function create(User $authUser, array $requestedData)
    {
        if ($authUser->departmentRole?->role?->name === 'Super Admin') {
            return true;
        }

        if ($authUser->departmentRole?->role?->name === 'Manager') {

            $isSameDepartment = (int)$requestedData['department_id'] === (int)$authUser->departmentRole->department_id;

            $targetRole = Role::find($requestedData['role_id']);

            $allowedRoles = ['Employee', 'Team Leader', 'Volunteer'];
            $isAllowedRole = $targetRole && in_array($targetRole->name, $allowedRoles);

            return $isSameDepartment && $isAllowedRole;
        }

        return false;
    }

    public function update(User $authUser, User $targetUser): bool
    {
        if ($authUser->departmentRole?->role?->name === 'Super Admin') {
            return true;
        }

        if ($authUser->departmentRole?->role?->name === 'Manager') {

            $isSameDepartment = $authUser->departmentRole->department_id === $targetUser->departmentRole?->department_id;

            $targetRoleName = $targetUser->departmentRole?->role?->name;
            $canManageRole = in_array($targetRoleName, ['Employee', 'Team Leader', 'Volunteer', 'Volunteer Manager']);

            return $isSameDepartment && $canManageRole;
        }

        return false;
    }

    public function view(User $authUser, User $targetUser): bool
    {
        if ($authUser->departmentRole?->role?->name === 'Super Admin') {
            return true;
        }

        if ($authUser->departmentRole?->role?->name === 'Manager') {
            return $authUser->departmentRole->department_id === $targetUser->departmentRole?->department_id;
        }

        return false;
    }
}
