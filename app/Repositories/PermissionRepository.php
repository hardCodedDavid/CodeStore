<?php

namespace App\Repositories;


use Spatie\Permission\Models\Role;
use App\Repositories\AbstractRepository;

class PermissionRepository extends AbstractRepository
{
    public function __construct(Role $role)
    {
        parent::__construct($role);
    }

    public function model()
    {
        return app(Role::class);
    }
}
