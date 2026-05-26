<?php


namespace App\Repositories;


use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasPermissions;

class PermissionRepository
{

    public function getAll()
    {
return Permission::all();
    }

     public function create(    array $data)
    {
        return Permission::create($data);
    }

    public function findById($permission_id)
    {
        return Permission::find($permission_id);

    }
    public function updateRole(array $data,$permission)
    {
        $permission->update($data);

    }

    public function delete($permission)
    {
        $permission->delete();
    }

    public function search(\App\Http\Requests\SearchForPermissionsAndRolesRequest $request)
    {
        $data = $request->only(['name', 'page']);
        $cacheKey = 'permissions_search_' . md5(json_encode($data));
        return Cache::remember($cacheKey, 60, function () use ($request) {
            $query = Permission::query()
                ->select('id', 'name');
            if ($request->filled('name')) {
                $query->where('name', 'like', $request->name . '%');
            }

            return $query->paginate(10);
        });

    }


}
