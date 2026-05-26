<?php
namespace App\Repositories;
use App\Models\Campaign;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
class userRepository
{
    public function getByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function create(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'],
            'email_verified_at' => now(),
        ]);
    }
    public function create_instructor(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'email_verified_at' => now(),
        ]);
    }

    public function create_User(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'],
            'email_verified_at' => now(),
            'status' => 'active',
            'department_id' => $data['department_id'],
        ]);
    }

    public function getById($id)
    {
        return User::query()->find($id);
    }
    public function getAll()
    {
        return User::all();
    }


    public function searchUser($request)
    {
        $data = $request->only(['name', 'role', 'page']);
        $cacheKey = 'users_search_' . md5(json_encode($data));
        return Cache::remember($cacheKey, 60, function () use ($request) {
            $query = User::query()
                ->with('roles')
                ->select('id', 'name');
            if ($request->filled('name')) {
                $query->where('name', 'like', $request->name . '%');
            }
            if ($request->filled('role')) {
                $query->whereHas('roles', function ($q) use ($request) {
                    $q->where('name', $request->role);
                });
            }
            return $query->paginate(10);
        });
    }         public function UpdateEmployee($data, $id)
    {
        $user=User::query()->find($id);
         $user->update([
            'name' => $data['name'] ?? $user->name,
            'email' => $data['email'] ?? $user->email,
            'phone' => $data['phone'] ?? $user->phone,
            'department_id' => $data['department_id'] ?? $user->department_id,
           // 'status' => $data['status'] ?? $user->status,
             ]);
        return $user;
    }
    public function getByRolesAndDepartment($roles, $departmentId)
    {
        return User::query()
            ->role($roles)
            ->where('department_id', $departmentId)
            ->get();
    }
//عرض متطوعين
    public function getVoulnteer( )
    {
        return User::query()
            ->role('Volunteer')
            ->get();
    }
    public function ShowAllRoles()
    {
        return Role::all();


}

    public function updateStatusUser(array $data, $user)
    {
        $user->update($data);
        return $user;
    }
}
