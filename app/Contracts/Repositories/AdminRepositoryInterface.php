<?php

namespace App\Contracts\Repositories;

interface AdminRepositoryInterface extends AbstractRepositoryInterface
{
    public function findByEmail(string $email);
}
