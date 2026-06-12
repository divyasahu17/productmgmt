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

    // Public Storefront routes
    Route::get('/public/categories', [\App\Http\Controllers\Api\V1\StorefrontController::class, 'categories']);
    Route::get('/public/products', [\App\Http\Controllers\Api\V1\StorefrontController::class, 'products']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
        
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
    });
});
