<?php

use App\Models\Admin;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AddPermissionsToDatabase extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('permissions', function (Blueprint $table) {
            //
        });

        \Illuminate\Support\Facades\DB::table('roles')->insert(['name' => 'Super Admin', 'guard_name' => 'admin', 'created_at' => now(), 'updated_at' => now()]);
        \Illuminate\Support\Facades\DB::table('permissions')->insert([
            ['name' => 'View Dashboard', 'guard_name' => 'admin'],
            ['name' => 'View Products', 'guard_name' => 'admin'],
            ['name' => 'Add Products', 'guard_name' => 'admin'],
            ['name' => 'Edit Products', 'guard_name' => 'admin'],
            ['name' => 'Delete Products', 'guard_name' => 'admin'],
            ['name' => 'List Products', 'guard_name' => 'admin'],
            ['name' => 'Unlist Products', 'guard_name' => 'admin'],
            ['name' => 'Export Products', 'guard_name' => 'admin'],
            ['name' => 'View Purchases', 'guard_name' => 'admin'],
            ['name' => 'Add Purchases', 'guard_name' => 'admin'],
            ['name' => 'Edit Purchases', 'guard_name' => 'admin'],
            ['name' => 'Delete Purchases', 'guard_name' => 'admin'],
            ['name' => 'Print Purchase Invoice', 'guard_name' => 'admin'],
            ['name' => 'Export Purchases', 'guard_name' => 'admin'],
            ['name' => 'View Sales', 'guard_name' => 'admin'],
            ['name' => 'Add Sales', 'guard_name' => 'admin'],
            ['name' => 'Edit Sales', 'guard_name' => 'admin'],
            ['name' => 'Delete Sales', 'guard_name' => 'admin'],
            ['name' => 'Print Sale Invoice', 'guard_name' => 'admin'],
            ['name' => 'Export Sales', 'guard_name' => 'admin'],
            ['name' => 'View Suppliers', 'guard_name' => 'admin'],
            ['name' => 'Add Suppliers', 'guard_name' => 'admin'],
            ['name' => 'Edit Suppliers', 'guard_name' => 'admin'],
            ['name' => 'Delete Suppliers', 'guard_name' => 'admin'],
            ['name' => 'View Users', 'guard_name' => 'admin'],
            ['name' => 'Block Users', 'guard_name' => 'admin'],
            ['name' => 'Unblock Users', 'guard_name' => 'admin'],
            ['name' => 'Export Users', 'guard_name' => 'admin'],
            ['name' => 'View Banners', 'guard_name' => 'admin'],
            ['name' => 'Add Banners', 'guard_name' => 'admin'],
            ['name' => 'Edit Banners', 'guard_name' => 'admin'],
            ['name' => 'Delete Banners', 'guard_name' => 'admin'],
            ['name' => 'View Orders', 'guard_name' => 'admin'],
            ['name' => 'Process Orders', 'guard_name' => 'admin'],
            ['name' => 'View Categories', 'guard_name' => 'admin'],
            ['name' => 'Add Categories', 'guard_name' => 'admin'],
            ['name' => 'Edit Categories', 'guard_name' => 'admin'],
            ['name' => 'Delete Categories', 'guard_name' => 'admin'],
            ['name' => 'View Brands', 'guard_name' => 'admin'],
            ['name' => 'Add Brands', 'guard_name' => 'admin'],
            ['name' => 'Edit Brands', 'guard_name' => 'admin'],
            ['name' => 'Delete Brands', 'guard_name' => 'admin'],
            ['name' => 'View Variations', 'guard_name' => 'admin'],
            ['name' => 'Add Variations', 'guard_name' => 'admin'],
            ['name' => 'Edit Variations', 'guard_name' => 'admin'],
            ['name' => 'Delete Variations', 'guard_name' => 'admin'],
            ['name' => 'View Administrators', 'guard_name' => 'admin'],
            ['name' => 'Add Administrators', 'guard_name' => 'admin'],
            ['name' => 'Edit Administrators', 'guard_name' => 'admin'],
            ['name' => 'Delete Administrators', 'guard_name' => 'admin'],
            ['name' => 'View Roles', 'guard_name' => 'admin'],
            ['name' => 'Add Roles', 'guard_name' => 'admin'],
            ['name' => 'Edit Roles', 'guard_name' => 'admin'],
            ['name' => 'Delete Roles', 'guard_name' => 'admin'],
            ['name' => 'Update Settings', 'guard_name' => 'admin'],
            ['name' => 'View Reviews', 'guard_name' => 'admin'],
            ['name' => 'Approve Reviews', 'guard_name' => 'admin'],
            ['name' => 'View Subscriptions', 'guard_name' => 'admin'],
            ['name' => 'Delete Subscriptions', 'guard_name' => 'admin'],
            ['name' => 'Send Newsletter', 'guard_name' => 'admin'],
        ]);
        $role = Role::where('name', 'Super Admin')->first();
        $permissions = Permission::all();
        $role->syncPermissions($permissions);
        $admins = Admin::all();
        foreach ($admins as $admin)
            $admin->assignRole($role);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('permissions', function (Blueprint $table) {
            //
        });

        $role = Role::where('name', 'Super Admin')->first();
        $permissions = Permission::all();
        if ($role) {
            $role->syncPermissions([]);
            $admins = Admin::all();
            foreach ($admins as $admin)
                $admin->removeRole($role);
            $role->delete();
        }
        $permissions->each(function($permission) {
            $permission->delete();
        });
    }
}
