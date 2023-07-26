<?php

namespace App\Services;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Repositories\PermissionRepository;

class AuthorizationService
{
    public function __construct(protected PermissionRepository $repository) 
    {
        //
    }

    public function all() : array 
    {
        return [
            'roles' => Role::where('name', '!=', 'Super Admin')->with('permissions')->get(),
            // 'permissions' => Permission::all()
        ];
    }

    public function addPermissions(array $data) : object
    {
        $role = $this->repository->create(request()->only('name'));

        $role->syncPermissions($data['permissions']);

        return $role;
    }

    public function editPermissions(Role $role, array $data) : object
    {
        $role = $this->repository->update($role, request()->only('name'));

        $role->syncPermissions($data['permissions']);

        return $role;
    }

    public function deletePermissions(Role $role) : bool
    {
        // Check if role has users
        if ($role->users()->count() > 0){
            return false;
        }
        // remove all permissions
        $role->syncPermissions([]);

        // delete role
        return $role->delete();
    }
}
