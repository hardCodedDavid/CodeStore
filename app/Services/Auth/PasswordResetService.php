<?php

namespace App\Services\Auth;

use App\Contracts\Services\PasswordResetServiceInterface;
use App\Exceptions\CustomException;
use App\Repositories\AbstractRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class PasswordResetService implements PasswordResetServiceInterface
{
    protected AbstractRepository $repository;

    public function __construct()
    {
    }

    /**
     * @param string $email
     * @return void
     */
    public function sendResetTokenEmail(string $email): void
    {
        $user = $this->repository()->findByEmail($email);
        $token = rand(0000, 9999);
        $this->repository()->update($user, ['otp' => Hash::make($token), 'otp_expiry' => now()->addMinutes(10)]);
        $user->sendPasswordResetNotification($token);
    }

    /**
     * @param string $email
     * @param string $token
     * @return void
     * @throws CustomException
     */
    public function verifyToken(string $email, string $token): void
    {
        $user = $this->repository()->findByEmail($email);
        if (!$user) {
            throw new CustomException('User not found');
        }
        $user->verifyToken($token);
    }

    /**
     * @param string $email
     * @param string $token
     * @param string $password
     * @return Model
     * @throws CustomException
     */
    public function resetPassword(string $email, string $token, string $password): Model
    {
        $user = $this->repository()->findByEmail($email);
        if (!$user) {
            throw new CustomException('User not found');
        }
        $user->verifyToken($token);

        return $this->repository()->update($user, ['password' => Hash::make($password), 'otp' => null, 'otp_expiry' => null]);
    }

    /**
     * @param Model $user
     * @param string $oldPassword
     * @param string $newPassword
     * @return Model
     * @throws ValidationException
     */
    public function changePassword(Model $user, string $oldPassword, string $newPassword): Model
    {
        if (!Hash::check($oldPassword, $user['password'])) {
            throw ValidationException::withMessages([
                'old_password' => 'The old password is incorrect.'
            ]);
        }

        return $this->repository()->update($user, ['password' => Hash::make($newPassword)]);
    }

    /**
     * @param AbstractRepository $repository
     * @return void
     */
    public function setRepository(AbstractRepository $repository): void
    {
        $this->repository = $repository;
    }

    /**
     * @return AbstractRepository
     */
    private function repository(): AbstractRepository
    {
        return $this->repository;
    }
}
