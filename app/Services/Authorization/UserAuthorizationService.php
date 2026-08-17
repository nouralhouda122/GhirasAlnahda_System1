<?php

namespace App\Services\Authorization;

use App\Models\User;
use Spatie\Permission\Models\Role;

class UserAuthorizationService
{

    public function canCreate(User $authUser, array $data): bool
    {
        if ($authUser->departmentRole?->role?->name === 'Super Admin') {
            return true;
        }

        if ($authUser->departmentRole?->role?->name === 'Manager') {

            if ($authUser->departmentRole->department_id != $data['department_id']) {
                return false;
            }

            $role = Role::find($data['role']);

            if (! $role) {
                return false;
            }

            return in_array($role->name, [
                'Employee',
                'Team Leader',
                'Volunteer',
                'Volunteer Manager',
            ]);
        }

        return false;
    }
    public function canView(User $authUser, User $user): bool
    {
        if ($authUser->id === $user->id) {
            return false;
        }

        $authRole = $authUser->departmentRole?->role?->name;

        if ($authRole === 'Super Admin') {
            return true;
        }

        return $authUser->departmentRole?->department_id === $user->departmentRole?->department_id;
    }public function canUpdate(User $authUser, User $targetUser): bool
{
    $authRole = $authUser->departmentRole?->role?->name;

    if ($authRole === 'Super Admin') {
        return true;
    }

    if ($authUser->id === $targetUser->id) {
        return false;
    }

    if ($authRole === 'Manager') {
        return $authUser->departmentRole?->department_id === $targetUser->departmentRole?->department_id;
    }

    return false;
}}
