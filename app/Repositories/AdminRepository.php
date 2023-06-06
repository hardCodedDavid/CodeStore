<?php

namespace App\Repositories;

use App\Contracts\Repositories\AdminRepositoryInterface;
use App\Models\Admin;

class AdminRepository extends AbstractRepository implements AdminRepositoryInterface
{
    public function __construct(Admin $admin)
    {
        parent::__construct($admin);
    }

    public function model()
    {
        return app(Admin::class);
    }

    /**
     * @param string $email
     * @return Admin|null
     */
    public function findByEmail(string $email): ?Admin
    {
        return parent::findByColumn('email', $email);
    }
}
