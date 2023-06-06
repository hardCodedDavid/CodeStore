<?php

namespace App\Contracts\Services;

use App\Repositories\AbstractRepository;
use Illuminate\Database\Eloquent\Model;

interface PasswordResetServiceInterface
{
    public function sendResetTokenEmail(string $email);
    public function verifyToken(string $email, string $token);
    public function resetPassword(string $email, string $token, string $password);
    public function changePassword(Model $user, string $oldPassword, string $newPassword);
    public function setRepository(AbstractRepository $repository): void;
}
