<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use Illuminate\Http\Request;
use App\Services\AdminService;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminRequest;
use App\Http\Requests\Admin\UpdateRequest;

class AdminController extends Controller
{
    public function __construct(protected AdminService $service)
    {
        //
    }

    public function index(): JsonResponse
    {
        $data = $this->service->all();

        return $this->success(message: 'Data fetched successfully', data: compact('data'));
    }

    public function store(AdminRequest $request): JsonResponse
    {
        $role = Role::find(request('role'));

        if ($role) {
            $admin = $this->service->addAdmin($request->validated());
            return $this->success(message: 'Data created successfully', data: compact('admin'));
        } else {
            return $this->failure('An error occured!!', details: 'Can\'t find Role.');
        }
    }

    public function update(Admin $admin, UpdateRequest $request): JsonResponse
    {
        $admin = $this->service->editAdmin($admin, $request->validated());

        return $this->success(message: 'Data updated successfully', data: compact('admin'));
    }

    public function destroy(Admin $admin): JsonResponse
    {
        $this->service->deleteAdmin($admin);
        
        return $this->success('Data deleted successfully');
    }
}
