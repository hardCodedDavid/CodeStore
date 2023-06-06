<?php

namespace App\Http\Controllers\User\Auth;

use App\Contracts\Services\AuthServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\AuthResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function __construct(protected AuthServiceInterface $service)
    {
        $this->service->setGuard();
    }

    /**
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $this->service->register($request->validated());
        $token = $user->createToken('auth_token')->plainTextToken;
        $user = new AuthResource($user);

        return $this->success('Registration successful!', compact('user', 'token'));
    }

    /**
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->service->login($request->validated());
        $token = $user->createToken('auth_token')->plainTextToken;
        $user = new AuthResource($user);

        return $this->success('Login successful!', compact('user', 'token'));
    }

    /**
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        $this->service->logout();

        return $this->success('Logout successful!');
    }

    /**
     * @return JsonResponse
     */
    public function user(): JsonResponse
    {
        $user = new AuthResource($this->service->getCurrentUser());

        return $this->success(data: compact('user'));
    }
}
