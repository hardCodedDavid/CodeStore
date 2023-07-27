<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\Auth\AuthController;
use App\Http\Controllers\Admin\VariationController;
use App\Http\Controllers\Admin\TransactionController;
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
    Route::get('/dashboard', [HomeController::class, 'index']);

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
    Route::post('authorization/{role}', [AuthorizationController::class, 'update']);
    Route::delete('authorization/{role}', [AuthorizationController::class, 'destroy']);

    Route::get('admins', [AdminController::class, 'index']);
    Route::post('admin', [AdminController::class, 'store']);
    Route::post('admin/{admin:id}', [AdminController::class, 'update']);
    Route::delete('admin/{admin:id}', [AdminController::class, 'destroy']);

    Route::get('reviews', [ReviewController::class, 'index']);
    Route::post('review/{review:id}/{action}', [ReviewController::class, 'action']);

    Route::get('suppliers', [SupplierController::class, 'index']);
    Route::post('supplier', [SupplierController::class, 'store']);
    Route::post('supplier/{supplier}', [SupplierController::class, 'update']);
    Route::delete('supplier/{supplier}', [SupplierController::class, 'destroy']);

    Route::get('users', [UserController::class, 'index']);
    Route::get('user/{user}', [UserController::class, 'show']);
    Route::post('user/{user}', [UserController::class, 'action']);

    Route::get('/transactions/purchases/{purchase:code}/invoice', [TransactionController::class, 'puchaseInvoice']);
    Route::post('/transactions/purchases', [TransactionController::class, 'storePurchase']);
    Route::post('/transactions/purchase/{purchase:id}', [TransactionController::class, 'updatePurchase']);
    Route::delete('/transactions/purchase/{purchase:id}', [TransactionController::class, 'destroyPurchase']);
    Route::put('/sales/item-number/{id}/update', [TransactionController::class, 'removeItemNumber']);
    Route::delete('/item-number/{id}/delete', [TransactionController::class, 'deleteItemNumber']);

    Route::get('/transactions/sale/{sale:code}/invoice', [TransactionController::class, 'saleInvoice']);
    Route::post('/transactions/sales', [TransactionController::class, 'storeSale']);
    Route::post('/transactions/sale/{sale:id}', [TransactionController::class, 'updateSale']);
    Route::delete('/transactions/sale/{sale:id}', [TransactionController::class, 'destroySale']);

    Route::post('/users/export/download', [ExportController::class, 'exportUsers']);
    Route::post('/products/export/download', [ExportController::class, 'exportProducts'])->middleware('permission:Export Products');
    Route::post('/purchases/export/download', [ExportController::class, 'exportPurchases'])->middleware('permission:Export Purchases');
    Route::post('/sales/export/download', [ExportController::class, 'exportSales'])->middleware('permission:Export Sales');

    Route::post('/settings/business/update', [HomeController::class, 'updateBusiness']);
    Route::post('/settings/location/update', [HomeController::class, 'updateLocation']);
    Route::post('/settings/bank/update', [HomeController::class, 'updateBank']);

    Route::post('/profile/update', [HomeController::class, 'updateProfile']);
    Route::post('/password/custom/change', [HomeController::class, 'changePassword']);
    Route::get('/{type}/{code}/invoice/send', [HomeController::class, 'sendInvoiceLinkToMail']);
});
