<?php

namespace App\Providers;

use App\Contracts\Repositories\AbstractRepositoryInterface;
use App\Contracts\Repositories\AdminRepositoryInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Services\AuthServiceInterface;
use App\Contracts\Services\EmailVerificationServiceInterface;
use App\Contracts\Services\PasswordResetServiceInterface;
use App\Models\PersonalAccessToken;
use App\Repositories\AbstractRepository;
use App\Repositories\AdminRepository;
use App\Repositories\UserRepository;
use App\Services\Auth\AuthService;
use App\Services\Auth\EmailVerificationService;
use App\Services\Auth\PasswordResetService;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Services
        $this->app->bind(AuthServiceInterface::class, AuthService::class);
        $this->app->bind(EmailVerificationServiceInterface::class, EmailVerificationService::class);
        $this->app->bind(PasswordResetServiceInterface::class, PasswordResetService::class);

        // Repositories
        $this->app->bind(AbstractRepositoryInterface::class, AbstractRepository::class);
        $this->app->bind(AdminRepositoryInterface::class, AdminRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
    }
}
