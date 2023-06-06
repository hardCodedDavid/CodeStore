<?php

namespace App\Http\Controllers\User\Auth;

use App\Contracts\Services\EmailVerificationServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\VerifyEmailRequest;
use App\Http\Resources\AuthResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    public function __construct(protected EmailVerificationServiceInterface $service)
    {
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function resend(Request $request): JsonResponse
    {
        $this->service->sendVerificationEmail($request->user());

        return $this->success('Verification otp sent!');
    }

    /**
     * @param VerifyEmailRequest $request
     * @return JsonResponse
     */
    public function verify(VerifyEmailRequest $request): JsonResponse
    {
        $user = new AuthResource($this->service->verifyEmail($request->user(), $request->input('token')));

        return $this->success('Email verified successfully!', compact('user'));
    }
}
