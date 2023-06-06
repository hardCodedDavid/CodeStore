<?php

namespace App\Contracts\Services;

use App\Models\User;

interface EmailVerificationServiceInterface
{
    public function sendVerificationEmail(User $user);
    public function verifyEmail(User $user, string $token);
}
