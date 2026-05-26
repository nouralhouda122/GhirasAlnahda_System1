<?php

// app/Policies/UserPolicy.php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function create(User $user, string $role): bool
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }
        return $user->can("create $role");
    }
    public function update(User $authUser, User $user): bool
    {
        if ($authUser->hasRole('Super Admin')) {
            return true;
        }

        if ($authUser->hasRole('Campaign Manager')) {
            return $user->hasAnyRole([
                'Campaign Employee',
                'Volunteer Manager'
            ]);
        }

        if ($authUser->hasRole('Evaluation Manager')) {
            return $user->hasRole('Evaluation Officer');
        }

        return false;
    }
    public function view(User $authUser, User $user): bool
    {
        if ($authUser->hasRole('Super Admin')) {
            return true;
        }

        if ($authUser->hasRole('Campaign Manager')) {
            return $user->hasAnyRole([
                'Campaign Employee',
                'Volunteer Manager'
            ]);
        }

        if ($authUser->hasRole('Evaluation Manager')) {
            return $user->hasRole('Evaluation Officer');
        }

        return false;
    }    }
