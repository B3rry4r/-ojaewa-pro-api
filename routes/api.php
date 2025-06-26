<?php

use App\Http\Controllers\API\OrderController as APIOrderController;
use App\Http\Controllers\API\ProductController as APIProductController;
use App\Http\Controllers\API\ReviewController as APIReviewController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\UserAuthController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\SellerProfileController;

// User Auth
Route::controller(UserAuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::post('password/forgot', 'forgot');
    Route::post('password/reset', 'reset');
});

// Google OAuth
Route::post('oauth/google', [GoogleAuthController::class, 'handle']);

// Admin Auth
Route::post('/admin/login', [AdminAuthController::class, 'login']);

// Seller Profile, Product, Order, Review (authenticated routes)
Route::middleware('auth:sanctum')->group(function () {
    // Seller Profile endpoints
    Route::get('/seller/profile', [SellerProfileController::class, 'show']);
    Route::post('/seller/profile', [SellerProfileController::class, 'store']);
    Route::put('/seller/profile', [SellerProfileController::class, 'update']);
    Route::delete('/seller/profile', [SellerProfileController::class, 'destroy']);
    Route::post('/seller/profile/upload', [SellerProfileController::class, 'uploadFile']);
    
    // Product endpoints
    Route::prefix('products')->group(function () {
        Route::get('/', [APIProductController::class, 'index']);
        Route::post('/', [APIProductController::class, 'store']);
        Route::get('/suggestions', [APIProductController::class, 'suggestions']);
        Route::get('/{product}', [APIProductController::class, 'show']);
        Route::put('/{product}', [APIProductController::class, 'update']);
        Route::delete('/{product}', [APIProductController::class, 'destroy']);
    });
    
    // Order endpoints
    Route::prefix('orders')->group(function () {
        Route::get('/', [APIOrderController::class, 'index']);
        Route::post('/', [APIOrderController::class, 'store']);
        Route::get('/{order}', [APIOrderController::class, 'show']);
    });
    
    // Review endpoints
    Route::prefix('reviews')->group(function () {
        Route::post('/', [APIReviewController::class, 'store']);
        Route::get('/{type}/{id}', [APIReviewController::class, 'byEntity']);
    });
});