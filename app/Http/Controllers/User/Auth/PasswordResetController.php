<?php

namespace App\Http\Controllers\User\Auth;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Services\PasswordResetServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\VerifyTokenRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PasswordResetController extends Controller
{
    public function __construct(
        protected UserRepositoryInterface $repository,
        protected PasswordResetServiceInterface $service
    )
    {
        $this->service->setRepository($this->repository);
    }

    public function resend(Request $request): JsonResponse
    {
        $request->validate(['email' => ['required', 'exists:users']]);
        $this->service->sendResetTokenEmail($request->input('email'));

        return $this->success('Password reset otp sent!');
    }

    public function verify(VerifyTokenRequest $request): JsonResponse
    {
        $this->service->verifyToken($request->input('email'), $request->input('token'));

        return $this->success('Token verified successfully');
    }

    public function reset(ResetPasswordRequest $request): JsonResponse
    {
        $this->service->resetPassword(
            $request->input('email'),
            $request->input('token'),
            $request->input('password')
        );

        return $this->success('Password reset successful!');
    }

    public function change(ChangePasswordRequest $request): JsonResponse
    {
        $this->service->changePassword(
            $request->user(),
            $request->input('old_password'),
            $request->input('new_password')
        );

        return $this->success('Password updated successfully!');
    }
}
