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
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\AddressController;
use App\Http\Controllers\API\CartController;
use App\Http\Controllers\API\SchoolController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\Admin\SchoolRegistrationController;
use App\Http\Controllers\API\NotificationPreferenceController;
use App\Http\Controllers\API\SubscriptionController;
use App\Http\Controllers\API\BlogFavoriteController;
use App\Http\Controllers\API\Admin\AdvertController;
use App\Http\Controllers\API\Admin\SustainabilityController as AdminSustainabilityController;
use App\Http\Controllers\API\SustainabilityController;
use App\Http\Controllers\API\Admin\AdminNotificationController;
use App\Http\Controllers\API\Admin\AdminSettingsController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\UserAuthController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\SellerProfileController;

/*
|--------------------------------------------------------------------------
| API Routes - Oja Ewa Pro
|--------------------------------------------------------------------------
|
| Routes organized by user type: User, Seller, Admin
| Following the structure defined in app.md documentation
|
*/

// =============================================================================
// USER ENDPOINTS
// =============================================================================

// USER AUTHENTICATION
Route::controller(UserAuthController::class)->group(function () {
    Route::post("register", "register");
    Route::post("login", "login");
    Route::post("password/forgot", "forgot");
    Route::post("password/reset", "reset");
});

Route::post("oauth/google", [GoogleAuthController::class, "handle"]);

// HOME SCREEN & PUBLIC ROUTES
Route::prefix("categories")->group(function () {
    Route::get("/", [CategoryController::class, "index"]);
    Route::get("/{id}/children", [CategoryController::class, "children"]);
    Route::get("/{type}/{slug}/items", [CategoryController::class, "items"]);
});

Route::prefix("blogs")->group(function () {
    Route::get("/", [BlogController::class, "index"]);
    Route::get("/latest", [BlogController::class, "latest"]);
    Route::get("/search", [BlogController::class, "search"]);
    
    // Authenticated blog favorites routes (must come before {slug} route)
    Route::middleware('auth:sanctum')->prefix("favorites")->group(function () {
        Route::get("/", [BlogFavoriteController::class, "index"]);
        Route::post("/", [BlogFavoriteController::class, "store"]);
        Route::delete("/", [BlogFavoriteController::class, "destroy"]);
    });
    
    // IMPORTANT: Keep {slug} route last to avoid catching other routes
    Route::get("/{slug}", [BlogController::class, "show"]);
});

Route::prefix("faqs")->group(function () {
    Route::get("/", [FaqController::class, "index"]);
    Route::get("/categories", [FaqController::class, "categories"]);
    Route::get("/search", [FaqController::class, "search"]);
    Route::get("/{id}", [FaqController::class, "show"]);
});

Route::prefix("connect")->group(function () {
    Route::get("/", [ConnectController::class, "index"]);
    Route::get("/social", [ConnectController::class, "social"]);
    Route::get("/contact", [ConnectController::class, "contact"]);
    Route::get("/app-links", [ConnectController::class, "appLinks"]);
});

// SCHOOLS (Public Registration)
Route::post("/school-registrations", [SchoolController::class, "register"]);

// PAYMENT WEBHOOKS (Public, no auth required)
Route::post("/webhook/paystack", [
    PaymentController::class,
    "handleOrderWebhook",
]);
Route::post("/webhook/paystack/school", [
    SchoolController::class,
    "handlePaymentWebhook",
]);

// ============================================
// PUBLIC BUSINESS PROFILES
// ============================================
Route::prefix('business')->group(function () {
    // Public endpoints (no auth required)
    Route::get('/public', [BusinessProfileController::class, 'publicIndex']);
    Route::get('/public/search', [BusinessProfileController::class, 'search']);
    Route::get('/public/filters', [BusinessProfileController::class, 'filters']);
    Route::get('/public/{id}', [BusinessProfileController::class, 'publicShow']);
    
    // Alternative: /api/business/{id} for public access (must be numeric ID)
    Route::get('/{id}', [BusinessProfileController::class, 'publicShow'])
        ->where('id', '[0-9]+')
        ->name('business.public.show');
});


// ============================================
// PUBLIC PRODUCT BROWSING
// ============================================
Route::prefix('products')->group(function () {
    Route::get('/browse', [APIProductController::class, 'browse']);
    Route::get('/filters', [APIProductController::class, 'filters']);
    Route::get('/public/{id}', [APIProductController::class, 'publicShow']);
});

// ============================================
// PUBLIC SUSTAINABILITY INITIATIVES
// ============================================
Route::prefix('sustainability')->group(function () {
    Route::get('/', [SustainabilityController::class, 'index']);
    Route::get('/search', [SustainabilityController::class, 'search']);
    Route::get('/filters', [SustainabilityController::class, 'filters']);
    Route::get('/{id}', [SustainabilityController::class, 'show']);
});

// ============================================
// PUBLIC ADVERTS
// ============================================
Route::prefix('adverts')->group(function () {
    Route::get('/', [AdvertController::class, 'index']);
    Route::get('/{id}', [AdvertController::class, 'show']);
});

// AUTHENTICATED USER ROUTES
Route::middleware("auth:sanctum")->group(function () {
    // 👤 ACCOUNT MANAGEMENT
    Route::get("/profile", [UserController::class, "profile"]);
    Route::put("/profile", [UserController::class, "updateProfile"]);
    Route::put("/password", [UserController::class, "updatePassword"]);
    
    // USER LOGOUT
    Route::post('/logout', function (Request $request) {
        $token = $request->user()->currentAccessToken();
        if ($token) {
            $token->delete();
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully'
        ]);
    });
    
    // SHOPPING CART
    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'index']);
        Route::post('/items', [CartController::class, 'store']);
        Route::patch('/items/{id}', [CartController::class, 'update']);
        Route::delete('/items/{id}', [CartController::class, 'destroy']);
        Route::delete('/', [CartController::class, 'clear']);
    });

    // ADDRESS MANAGEMENT
    Route::prefix("addresses")->group(function () {
        Route::get("/", [AddressController::class, "index"]);
        Route::post("/", [AddressController::class, "store"]);
        Route::get("/{id}", [AddressController::class, "show"]);
        Route::put("/{id}", [AddressController::class, "update"]);
        Route::delete("/{id}", [AddressController::class, "destroy"]);
    });

    // MARKET OPERATIONS
    Route::prefix("products")->group(function () {
        Route::get("/", [APIProductController::class, "index"]);
        Route::post("/", [APIProductController::class, "store"]);
        Route::get("/search", [APIProductController::class, "search"]);
        Route::get("/suggestions", [
            APIProductController::class,
            "suggestions",
        ]);
        Route::get("/{product}", [APIProductController::class, "show"]);
        Route::put("/{product}", [APIProductController::class, "update"]);
        Route::delete("/{product}", [APIProductController::class, "destroy"]);
    });

    Route::prefix("orders")->group(function () {
        Route::get("/", [APIOrderController::class, "index"]);
        Route::post("/", [APIOrderController::class, "store"]);
        Route::get("/{order}", [APIOrderController::class, "show"]);
        Route::get("/{order}/tracking", [
            APIOrderController::class,
            "tracking",
        ]);
        Route::post("/{id}/cancel", [APIOrderController::class, "cancel"]);
    });

    Route::prefix("reviews")->group(function () {
        Route::post("/", [APIReviewController::class, "store"]);
        Route::get("/{type}/{id}", [APIReviewController::class, "byEntity"]);
    });

    // PAYMENT OPERATIONS
    Route::post("/payment/link", [
        PaymentController::class,
        "createOrderPaymentLink",
    ]);
    Route::post("/payment/link/school", [
        SchoolController::class,
        "createPaymentLink",
    ]);
    Route::post("/payment/verify", [PaymentController::class, "verifyPayment"]);

    // WISHLIST
    Route::prefix("wishlist")->group(function () {
        Route::get("/", [WishlistController::class, "index"]);
        Route::post("/", [WishlistController::class, "store"]);
        Route::delete("/", [WishlistController::class, "destroy"]);
    });

    // NOTIFICATIONS
    Route::prefix("notifications")->group(function () {
        Route::get("/", [NotificationController::class, "index"]);
        Route::get("/unread-count", [
            NotificationController::class,
            "unreadCount",
        ]);
        Route::get("/filter", [NotificationController::class, "filter"]);
        Route::patch("/{id}/read", [
            NotificationController::class,
            "markAsRead",
        ]);
        Route::patch("/mark-all-read", [
            NotificationController::class,
            "markAllAsRead",
        ]);
        Route::delete("/{id}", [NotificationController::class, "destroy"]);
        Route::get("/preferences", [
            NotificationPreferenceController::class,
            "show",
        ]);
        Route::put("/preferences", [
            NotificationPreferenceController::class,
            "update",
        ]);
    });

    // BLOG FAVORITES
    // Blog favorites routes moved to public blogs prefix group above to avoid route conflict

    // =============================================================================
    // SELLER ENDPOINTS (Authenticated Users with Seller Profiles)
    // =============================================================================

    // SELLER PROFILE MANAGEMENT
    Route::get("/seller/profile", [SellerProfileController::class, "show"]);
    Route::post("/seller/profile", [SellerProfileController::class, "store"]);
    Route::put("/seller/profile", [SellerProfileController::class, "update"]);
    Route::delete("/seller/profile", [
        SellerProfileController::class,
        "destroy",
    ]);
    Route::post("/seller/profile/upload", [
        SellerProfileController::class,
        "uploadFile",
    ]);

    // BUSINESS PROFILE MANAGEMENT (authenticated user's own profiles)
    Route::prefix("business")->group(function () {
        Route::get("/", [BusinessProfileController::class, "index"]);
        Route::post("/", [BusinessProfileController::class, "store"]);
        Route::get("/my/{id}", [BusinessProfileController::class, "show"]);
        Route::put("/my/{id}", [BusinessProfileController::class, "update"]);
        Route::delete("/my/{id}", [BusinessProfileController::class, "destroy"]);
        Route::patch("/my/{id}/deactivate", [
            BusinessProfileController::class,
            "deactivate",
        ]);
        Route::post("/my/{id}/upload", [
            BusinessProfileController::class,
            "upload",
        ]);
        Route::put("/subscription", [SubscriptionController::class, "update"]);
    });
});

// =============================================================================
// ADMIN ENDPOINTS
// =============================================================================

// ADMIN AUTHENTICATION (Unprotected)
Route::post("/admin/login", [AdminAuthController::class, "login"]);
Route::post("/admin/create", [AdminAuthController::class, "create"]);

// PROTECTED ADMIN ROUTES
Route::middleware(["auth:sanctum", "admin"])
    ->prefix("admin")
    ->group(function () {
        // ADMIN PROFILE MANAGEMENT
        Route::get("/profile", [AdminAuthController::class, "profile"]);
        Route::post("/logout", [AdminAuthController::class, "logout"]);

        // DASHBOARD OVERVIEW
        Route::get("/dashboard/overview", [
            App\Http\Controllers\API\Admin\AdminOverviewController::class,
            "index",
        ]);

        // USER MANAGEMENT
        Route::get("/users", [
            App\Http\Controllers\API\Admin\AdminUserController::class,
            "index",
        ]);

        // MARKET MANAGEMENT
        Route::get("/pending/sellers", [
            App\Http\Controllers\API\Admin\AdminSellerController::class,
            "pendingSellers",
        ]);
        Route::get("/market/sellers", [
            App\Http\Controllers\API\Admin\AdminSellerController::class,
            "index",
        ]);
        Route::get("/sellers/{id}", [
            App\Http\Controllers\API\Admin\AdminSellerController::class,
            "show",
        ]);
        Route::patch("/seller/{id}/approve", [
            App\Http\Controllers\API\Admin\AdminSellerController::class,
            "approveSeller",
        ]);
        Route::patch("/market/seller/{id}/status", [
            App\Http\Controllers\API\Admin\AdminSellerController::class,
            "updateStatus",
        ]);

        Route::get("/pending/products", [
            App\Http\Controllers\API\Admin\AdminProductController::class,
            "pendingProducts",
        ]);
        Route::get("/market/products", [
            App\Http\Controllers\API\Admin\AdminProductController::class,
            "index",
        ]);
        Route::get("/products/{id}", [
            App\Http\Controllers\API\Admin\AdminProductController::class,
            "show",
        ]);
        Route::patch("/product/{id}/approve", [
            App\Http\Controllers\API\Admin\AdminProductController::class,
            "approveProduct",
        ]);
        Route::patch("/market/product/{id}/status", [
            App\Http\Controllers\API\Admin\AdminProductController::class,
            "updateStatus",
        ]);

        // ORDER MANAGEMENT
        Route::get("/orders", [
            App\Http\Controllers\API\Admin\AdminOrderController::class,
            "index",
        ]);
        Route::get("/order/{id}", [
            App\Http\Controllers\API\Admin\AdminOrderController::class,
            "show",
        ]);
        Route::patch("/order/{id}/status", [
            App\Http\Controllers\API\Admin\AdminOrderController::class,
            "updateStatus",
        ]);

        // BUSINESS CATEGORY MANAGEMENT
        Route::get("/business/{category}", [
            App\Http\Controllers\API\Admin\AdminBusinessController::class,
            "index",
        ]);
        Route::get("/business/{category}/{id}", [
            App\Http\Controllers\API\Admin\AdminBusinessController::class,
            "show",
        ]);
        Route::patch("/business/{category}/{id}/status", [
            App\Http\Controllers\API\Admin\AdminBusinessController::class,
            "updateStatus",
        ]);

        // CONTENT MANAGEMENT
        Route::prefix("blogs")->group(function () {
            Route::get("/", [
                App\Http\Controllers\API\Admin\AdminBlogController::class,
                "index",
            ]);
            Route::post("/", [
                App\Http\Controllers\API\Admin\AdminBlogController::class,
                "store",
            ]);
            Route::get("/{id}", [
                App\Http\Controllers\API\Admin\AdminBlogController::class,
                "show",
            ]);
            Route::put("/{id}", [
                App\Http\Controllers\API\Admin\AdminBlogController::class,
                "update",
            ]);
            Route::delete("/{id}", [
                App\Http\Controllers\API\Admin\AdminBlogController::class,
                "destroy",
            ]);
            Route::patch("/{id}/toggle-publish", [
                App\Http\Controllers\API\Admin\AdminBlogController::class,
                "togglePublish",
            ]);
        });

        Route::prefix("adverts")->group(function () {
            Route::get("/", [AdvertController::class, "index"]);
            Route::post("/", [AdvertController::class, "store"]);
            Route::put("/{advert}", [AdvertController::class, "update"]);
            Route::delete("/{advert}", [AdvertController::class, "destroy"]);
        });

        // 🎓 SCHOOL REGISTRATION MANAGEMENT
        Route::prefix("school-registrations")->group(function () {
            Route::get("/", [SchoolRegistrationController::class, "index"]);
            Route::get("/{schoolRegistration}", [
                SchoolRegistrationController::class,
                "show",
            ]);
            Route::put("/{schoolRegistration}", [
                SchoolRegistrationController::class,
                "update",
            ]);
            Route::delete("/{schoolRegistration}", [
                SchoolRegistrationController::class,
                "destroy",
            ]);
        });

        // SUSTAINABILITY MANAGEMENT
        Route::prefix("sustainability")->group(function () {
            Route::get("/", [AdminSustainabilityController::class, "index"]);
            Route::post("/", [AdminSustainabilityController::class, "store"]);
            Route::put("/{sustainabilityInitiative}", [
                AdminSustainabilityController::class,
                "update",
            ]);
            Route::delete("/{sustainabilityInitiative}", [
                AdminSustainabilityController::class,
                "destroy",
            ]);
        });

        // ADMIN SETTINGS & NOTIFICATIONS
        Route::post("/notifications/send", [
            AdminNotificationController::class,
            "send",
        ]);
        Route::get("/settings", [AdminSettingsController::class, "show"]);
        Route::put("/settings", [AdminSettingsController::class, "update"]);
    });

// ============================================
// PUBLIC SELLER PROFILE ENDPOINTS
// ============================================
Route::prefix('sellers')->group(function () {
    Route::get('/{id}', [SellerProfileController::class, 'publicShow']);
    Route::get('/{id}/products', [SellerProfileController::class, 'products']);
});
