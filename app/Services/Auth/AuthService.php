<?php

namespace App\Services\Auth;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Services\AuthServiceInterface;
use App\Exceptions\CustomException;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService implements AuthServiceInterface
{
    public StatefulGuard $guard;

    public function __construct(protected UserRepositoryInterface $repository) {}

    /**
     * @param array $data
     * @return User
     */
    public function register(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        /** @var User $user */
        $user = $this->repository->create($data);
        event(new Registered($user));

        return $user;
    }

    /**
     * @param array $credentials
     * @return Authenticatable
     * @throws CustomException
     */
    public function login(array $credentials): Authenticatable
    {
        if (!$this->guard()->attempt($credentials)) {
            throw new CustomException('Invalid login credentials!', 400);
        }

        return $this->guard()->user();
    }

    /**
     * @return void
     */
    public function logout(): void
    {
        request()->user()->currentAccessToken()->delete();
    }

    /**
     * @return Authenticatable|null
     */
    public function getCurrentUser(): ?Authenticatable
    {
        return request()->user();
    }

    /**
     * @param string|null $guard
     * @return void
     */
    public function setGuard(?string $guard = null): void
    {
        $this->guard = Auth::guard($guard);
    }

    /**
     * @return StatefulGuard
     */
    private function guard(): StatefulGuard
    {
        return $this->guard;
    }
}
