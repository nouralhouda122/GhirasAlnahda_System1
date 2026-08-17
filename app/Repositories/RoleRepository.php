<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleRepository
{
    public function getAll()
    {
        return Role::all();
    }

    public function getNames()
    {
        return Role::pluck('name');
    }

    public function findById($id)
    {
        return Role::find($id);
    }
    public function findByName($name)
    {
        return Role::where('name',$name)->first();
    }

    public function create(    array $data)
    {
        return Role::create($data);
    }

    public function DeleteRole($role)
    {
        $role->delete();
    }

    public function updateRole(array $data,$role)
    {
        $role->update($data);

    }

    public function search(\App\Http\Requests\SearchForPermissionsAndRolesRequest $request)
    {
        $data = $request->only(['name', 'page']);
        $cacheKey = 'roles_search_' . md5(json_encode($data));
        return Cache::remember($cacheKey, 60, function () use ($request) {
            $query = Role::query()
                ->select('id', 'name');
            if ($request->filled('name')) {
                $query->where('name', 'like', $request->name . '%');
            }

            return $query->paginate(10);
        });

    }

    public function getRoleByDepartment($id)
    {
        return Role::where('department_id',$id)->get();

    }

    public function findRoleByDepartment($id)
    {
        return Role::where('department_id',$id)->first();

    }

}
