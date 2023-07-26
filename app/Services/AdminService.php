<?php

namespace App\Services;

use App\Models\Admin;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use App\Repositories\AdminRepository;
use App\Http\Controllers\Admin\NotificationController;

class AdminService
{
    public function __construct(protected AdminRepository $repository) 
    {
        //
    }

    public function all() : object 
    {
        $admin = Admin::with('roles')->get();
        
        return $admin;
    }

    public function addAdmin(array $data) : object
    {
        // Find role
        $role = Role::find(request('role'));

        // Generate login password
        $password = Str::random(8);
        $hashedPassword = Hash::make($password);

        // Create admin
        $data = request()->only('firstname', 'lastname', 'email');
        $data['password'] = $hashedPassword;
        $admin = Admin::create($data);

        // Assign role and send email notification
        $admin->assignRole($role);
        NotificationController::sendAdminRegistrationEmailNotification($admin, $password);

        return $admin;
    }

    public function editAdmin(Admin $admin, array $data) : object
    {
        // Find role
        $role = Role::find(request('role'));

        // Update admin
        $admin->update(request()->only('firstname', 'lastname', 'email'));
        $admin->syncRoles($role);

        return $admin;
    }

    public function deleteAdmin(Admin $admin) : bool
    {
        // Remove roles
        $admin->syncRoles([]);

        return $this->repository->destroy($admin);
    }
}
