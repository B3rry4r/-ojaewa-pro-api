<?php

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

// Seller Profile (authenticated routes)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/seller/profile', [SellerProfileController::class, 'show']);
    Route::post('/seller/profile', [SellerProfileController::class, 'store']);
    Route::put('/seller/profile', [SellerProfileController::class, 'update']);
    Route::delete('/seller/profile', [SellerProfileController::class, 'destroy']);
    Route::post('/seller/profile/upload', [SellerProfileController::class, 'uploadFile']);
});