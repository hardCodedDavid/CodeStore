<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use App\Services\AuthorizationService;
use App\Http\Requests\Auth\PermissionRequest;

class AuthorizationController extends Controller
{
    public function __construct(protected AuthorizationService $service)
    {
        //
    }

    public function index(): JsonResponse
    {
        $data = $this->service->all();

        return $this->success(message: 'Data fetched successfully', data: compact('data'));
    }

    public function store(PermissionRequest $request): JsonResponse
    {
        $roles = $this->service->addPermissions($request->validated());

        return $this->success(message: 'Data created successfully', data: compact('roles'));
    }

    public function update(Role $role, PermissionRequest $request): JsonResponse
    {
        $role = $this->service->editPermissions($role, $request->validated());

        return $this->success(message: 'Data updated successfully', data: compact('role'));
    }

    public function destroy(Role $role): JsonResponse
    {
        // $this->service->deletePermissions($role);

        // Check if role has users
        if ($role->users()->count() > 0){
            return $this->failure('An error occured!!', details: 'Can\'t delete role, administrators associated');
        }
        // remove all permissions
        $role->syncPermissions([]);

        // delete role
        $role->delete();
        
        return $this->success('Data deleted successfully');
    }
}
