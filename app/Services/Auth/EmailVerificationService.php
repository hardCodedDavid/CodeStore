<?php

namespace App\Services\Auth;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Services\EmailVerificationServiceInterface;
use App\Exceptions\CustomException;
use App\Models\User;

class EmailVerificationService implements EmailVerificationServiceInterface
{
    public function __construct(protected UserRepositoryInterface $repository)
    {
    }

    /**
     * @param User $user
     * @return void
     * @throws CustomException
     */
    public function sendVerificationEmail(User $user): void
    {
        if ($user->hasVerifiedEmail()) {
            throw new CustomException('Email verified already!');
        }

        $user->sendEmailVerificationNotification();
    }

    /**
     * @param User $user
     * @param string $token
     * @return User
     * @throws CustomException
     */
    public function verifyEmail(User $user, string $token): User
    {
        if ($user->hasVerifiedEmail()) {
            throw new CustomException('Email verified already!');
        }
        $user->verifyToken($token);
        $user->markEmailAsVerified();

        return $this->repository->update($user, ['otp' => null, 'otp_expiry' => null]);
    }
}
