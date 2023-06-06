<?php

namespace App\Contracts\Repositories;

interface UserRepositoryInterface extends AbstractRepositoryInterface
{
    public function findByEmail(string $email);
}
