<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Contracts\Services\AuthServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Admin\AuthResource;
use App\Models\Admin;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function __construct(protected AuthServiceInterface $service)
    {
        $this->service->setGuard('admin');
    }

    /**
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        /** @var Admin $admin */
        $admin = $this->service->login($request->validated());
        $token = $admin->createToken('auth_token')->plainTextToken;
        $user = new AuthResource($admin);

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
