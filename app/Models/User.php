<?php

namespace App\Models;

use App\Exceptions\CustomException;
use App\Notifications\EmailVerificationNotification;
use App\Notifications\PasswordResetNotification;
use App\Traits\UUID;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, UUID, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'phone',
        'status',
        'password',
        'otp',
        'otp_expiry',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'otp',
        'otp_expiry',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'otp_expiry' => 'datetime',
        'period_date' => 'datetime',
        'period_experience' => 'json',
        'password' => 'hashed',
        'otp' => 'hashed',
    ];

    public function sendEmailVerificationNotification()
    {
        $token = rand(0000, 9999);
        $this->update(['otp' => Hash::make($token), 'otp_expiry' => now()->addMinutes(10)]);
        $this->notify(new EmailVerificationNotification($token));
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new PasswordResetNotification($token));
    }

    /**
     * @throws CustomException
     */
    public function verifyToken(string $token)
    {
        if (!Hash::check($token, $this->attributes['otp'])) {
            throw new CustomException('Token is invalid!');
        }
        if (now()->gt($this->attributes['otp_expiry'])) {
            throw new CustomException('Token is expired!');
        }
    }
}
