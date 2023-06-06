<?php

namespace App\Models;

use App\Exceptions\CustomException;
use App\Notifications\PasswordResetNotification;
use App\Traits\UUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
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
        'otp',
        'otp_expiry',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'otp_expiry' => 'datetime',
        'password' => 'hashed',
        'otp' => 'hashed',
    ];

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
