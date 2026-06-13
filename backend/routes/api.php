<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->group(function () {
    // Public routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [\App\Http\Controllers\Api\V1\PasswordResetController::class, 'sendResetOtp']);
    Route::post('/reset-password', [\App\Http\Controllers\Api\V1\PasswordResetController::class, 'verifyOtpAndReset']);

    // Marketplace Public routes
    Route::get('/marketplace/categories', [\App\Http\Controllers\Api\V1\MarketplaceController::class, 'categories']);
    Route::get('/marketplace/products', [\App\Http\Controllers\Api\V1\MarketplaceController::class, 'products']);
    Route::get('/marketplace/products/{id}', [\App\Http\Controllers\Api\V1\MarketplaceController::class, 'showProduct']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
        
        // Profile Management
        Route::put('/profile', [\App\Http\Controllers\Api\V1\ProfileController::class, 'updateProfile']);
        Route::post('/profile/email/verify', [\App\Http\Controllers\Api\V1\ProfileController::class, 'verifyEmailOtp']);
        Route::put('/profile/password', [\App\Http\Controllers\Api\V1\ProfileController::class, 'updatePassword']);
        
        // Notifications
        Route::get('/notifications', [\App\Http\Controllers\Api\V1\NotificationController::class, 'index']);
        Route::put('/notifications/{id}/read', [\App\Http\Controllers\Api\V1\NotificationController::class, 'markAsRead']);
        Route::put('/notifications/read-all', [\App\Http\Controllers\Api\V1\NotificationController::class, 'markAllAsRead']);

        // Dashboard
        Route::get('/dashboard/low-stock', [\App\Http\Controllers\Api\V1\DashboardController::class, 'lowStock']);
        
        // Categories
        Route::apiResource('categories', \App\Http\Controllers\Api\V1\CategoryController::class);

        // Products
        Route::apiResource('products', \App\Http\Controllers\Api\V1\ProductController::class);

        // Users
        Route::get('/users', [\App\Http\Controllers\Api\V1\UserController::class, 'index']);
    });
});
