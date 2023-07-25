<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\Auth\AuthController;
use App\Http\Controllers\Admin\VariationController;
use App\Http\Controllers\Admin\AuthorizationController;
use App\Http\Controllers\Admin\Auth\PasswordResetController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Auth
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);

    Route::post('password/resend', [PasswordResetController::class, 'resend']);
    Route::post('password/verify', [PasswordResetController::class, 'verify']);
    Route::post('password/reset', [PasswordResetController::class, 'reset']);

    Route::middleware('auth:admin')->group(function () {
        Route::get('user', [AuthController::class, 'user']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('password/change', [PasswordResetController::class, 'change']);
    });
});

Route::middleware('auth:admin')->group(function () {
    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/{product:code}', [ProductController::class, 'show']);
    Route::post('products', [ProductController::class, 'store']);
    Route::post('products/{product:code}', [ProductController::class, 'update']);
    Route::delete('products/{product:code}', [ProductController::class, 'destroy']);

    Route::get('category', [CategoryController::class, 'index']);
    Route::post('category', [CategoryController::class, 'store']);
    Route::post('category/{category:id}', [CategoryController::class, 'update']);
    Route::delete('category/{category:id}', [CategoryController::class, 'destroy']);

    Route::get('brands', [BrandController::class, 'index']);
    Route::post('brand', [BrandController::class, 'store']);
    Route::post('brand/{brand:id}', [BrandController::class, 'update']);
    Route::delete('brand/{brand:id}', [BrandController::class, 'destroy']);

    Route::get('variations', [VariationController::class, 'index']);
    Route::post('variation', [VariationController::class, 'store']);
    Route::post('variation/{variation:id}', [VariationController::class, 'update']);
    Route::delete('variation/{variation:id}', [VariationController::class, 'destroy']);

    Route::get('banners', [BannerController::class, 'index'])->middleware('permission:View Banners');
    Route::post('banner', [BannerController::class, 'store'])->middleware('permission:Add Banners');
    Route::post('banner/{banner:id}', [BannerController::class, 'update'])->middleware('permission:Edit Banners');
    Route::delete('banner/{banner:id}', [BannerController::class, 'destroy'])->middleware('permission:Delete Banners');

    Route::get('authorizations', [AuthorizationController::class, 'index']);
    Route::post('authorization', [AuthorizationController::class, 'store']);
    Route::post('authorization/{authorization:id}', [AuthorizationController::class, 'update']);
    Route::delete('authorization/{authorization:id}', [AuthorizationController::class, 'destroy']);
});
