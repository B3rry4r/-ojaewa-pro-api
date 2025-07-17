<?php

use App\Http\Controllers\API\OrderController as APIOrderController;
use App\Http\Controllers\API\ProductController as APIProductController;
use App\Http\Controllers\API\ReviewController as APIReviewController;
use App\Http\Controllers\API\BusinessProfileController;
use App\Http\Controllers\API\WishlistController;
use App\Http\Controllers\API\BlogController;
use App\Http\Controllers\API\FaqController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\ConnectController;
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

// Admin Dashboard Routes (protected by admin guard)
Route::middleware(['auth:sanctum'])->prefix('admin')->group(function () {
    // Dashboard Overview
    Route::get('/dashboard/overview', [App\Http\Controllers\API\Admin\AdminOverviewController::class, 'index']);
    
    // Pending Seller Profiles
    Route::get('/pending/sellers', [App\Http\Controllers\API\Admin\AdminSellerController::class, 'pendingSellers']);
    Route::patch('/seller/{id}/approve', [App\Http\Controllers\API\Admin\AdminSellerController::class, 'approveSeller']);
    
    // Pending Products
    Route::get('/pending/products', [App\Http\Controllers\API\Admin\AdminProductController::class, 'pendingProducts']);
    Route::patch('/product/{id}/approve', [App\Http\Controllers\API\Admin\AdminProductController::class, 'approveProduct']);
    
    // Orders Management
    Route::get('/orders', [App\Http\Controllers\API\Admin\AdminOrderController::class, 'index']);
    Route::get('/order/{id}', [App\Http\Controllers\API\Admin\AdminOrderController::class, 'show']);
    Route::patch('/order/{id}/status', [App\Http\Controllers\API\Admin\AdminOrderController::class, 'updateStatus']);
    
    // User Management
    Route::get('/users', [App\Http\Controllers\API\Admin\AdminUserController::class, 'index']);
    
    // Market Management
    Route::get('/market/sellers', [App\Http\Controllers\API\Admin\AdminSellerController::class, 'index']);
    Route::get('/market/products', [App\Http\Controllers\API\Admin\AdminProductController::class, 'index']);
    Route::patch('/market/seller/{id}/status', [App\Http\Controllers\API\Admin\AdminSellerController::class, 'updateStatus']);
    Route::patch('/market/product/{id}/status', [App\Http\Controllers\API\Admin\AdminProductController::class, 'updateStatus']);
    
    // Business Management
    Route::get('/business/{category}', [App\Http\Controllers\API\Admin\AdminBusinessController::class, 'index']);
    Route::get('/business/{category}/{id}', [App\Http\Controllers\API\Admin\AdminBusinessController::class, 'show']);
    Route::patch('/business/{category}/{id}/status', [App\Http\Controllers\API\Admin\AdminBusinessController::class, 'updateStatus']);
});

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
    
    // Business Profile endpoints
    Route::prefix('business')->group(function () {
        Route::get('/', [BusinessProfileController::class, 'index']);
        Route::post('/', [BusinessProfileController::class, 'store']);
        Route::get('/{id}', [BusinessProfileController::class, 'show']);
        Route::put('/{id}', [BusinessProfileController::class, 'update']);
        Route::delete('/{id}', [BusinessProfileController::class, 'destroy']);
        Route::patch('/{id}/deactivate', [BusinessProfileController::class, 'deactivate']);
        Route::post('/{id}/upload', [BusinessProfileController::class, 'upload']);
    });

    // Wishlist endpoints
    Route::prefix('wishlist')->group(function () {
        Route::get('/', [WishlistController::class, 'index']);
        Route::post('/', [WishlistController::class, 'store']);
        Route::delete('/', [WishlistController::class, 'destroy']);
    });

    // Notification endpoints
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
        Route::get('/filter', [NotificationController::class, 'filter']);
        Route::patch('/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::patch('/mark-all-read', [NotificationController::class, 'markAllAsRead']);
        Route::delete('/{id}', [NotificationController::class, 'destroy']);
    });
});

// Public Phase 6 routes
Route::prefix('blogs')->group(function () {
    Route::get('/', [BlogController::class, 'index']);
    Route::get('/latest', [BlogController::class, 'latest']);
    Route::get('/search', [BlogController::class, 'search']);
    Route::get('/{slug}', [BlogController::class, 'show']);
});

Route::prefix('faqs')->group(function () {
    Route::get('/', [FaqController::class, 'index']);
    Route::get('/categories', [FaqController::class, 'categories']);
    Route::get('/search', [FaqController::class, 'search']);
    Route::get('/{id}', [FaqController::class, 'show']);
});

Route::prefix('connect')->group(function () {
    Route::get('/', [ConnectController::class, 'index']);
    Route::get('/social', [ConnectController::class, 'social']);
    Route::get('/contact', [ConnectController::class, 'contact']);
    Route::get('/app-links', [ConnectController::class, 'appLinks']);
});