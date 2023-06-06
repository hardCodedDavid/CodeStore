<?php

use App\Http\Controllers\User\Auth\AuthController;
use App\Http\Controllers\User\Auth\EmailVerificationController;
use App\Http\Controllers\User\Auth\PasswordResetController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your authentication. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::post('password/resend', [PasswordResetController::class, 'resend']);
Route::post('password/verify', [PasswordResetController::class, 'verify']);
Route::post('password/reset', [PasswordResetController::class, 'reset']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('user', [AuthController::class, 'user']);

    Route::post('email/resend', [EmailVerificationController::class, 'resend']);
    Route::post('email/verify', [EmailVerificationController::class, 'verify']);

    Route::post('password/change', [PasswordResetController::class, 'change']);
});
