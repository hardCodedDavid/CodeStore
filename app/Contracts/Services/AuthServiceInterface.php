<?php

namespace App\Contracts\Services;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;

interface AuthServiceInterface
{
    public function register(array $data): User;
    public function login(array $credentials): Authenticatable;
    public function logout(): void;
    public function getCurrentUser(): ?Authenticatable;
    public function setGuard(): void;
}
