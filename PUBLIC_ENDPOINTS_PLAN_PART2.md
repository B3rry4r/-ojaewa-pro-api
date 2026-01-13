# Public Endpoints Implementation Plan - Part 2

## Overview
Part 2 covers: Sustainability Initiatives, Adverts, User Features (Logout, Cart, Order Cancel)

---

## 📋 Part 2: Remaining Public Endpoints

### 4. Public Sustainability Initiatives

**What to add:**
- `GET /api/sustainability` - List active sustainability initiatives (public)
- `GET /api/sustainability/{id}` - View single initiative (public)

**Files to modify:**
- `routes/api.php` - Add public routes
- Create new `app/Http/Controllers/API/SustainabilityController.php` (public version)

**Implementation:**

#### Step 4.1: Add Routes
```php
// In routes/api.php - PUBLIC section

// PUBLIC SUSTAINABILITY INITIATIVES
Route::prefix('sustainability')->group(function () {
    Route::get('/', [SustainabilityController::class, 'index']);
    Route::get('/{id}', [SustainabilityController::class, 'show']);
});
```

#### Step 4.2: Create Controller
```php
// Create: app/Http/Controllers/API/SustainabilityController.php

<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\SustainabilityInitiative;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SustainabilityController extends Controller
{
    /**
     * Get all active sustainability initiatives (public)
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'category' => 'nullable|in:environmental,social,economic,governance',
            'per_page' => 'nullable|integer|min:1|max:50'
        ]);
        
        $query = SustainabilityInitiative::where('status', 'active')
                                        ->with('admin:id,firstname,lastname');
        
        // Filter by category if provided
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        
        $perPage = $request->input('per_page', 10);
        $initiatives = $query->latest()->paginate($perPage);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Sustainability initiatives retrieved successfully',
            'data' => $initiatives
        ]);
    }
    
    /**
     * Get single sustainability initiative (public)
     */
    public function show(int $id): JsonResponse
    {
        $initiative = SustainabilityInitiative::where('id', $id)
                                             ->where('status', 'active')
                                             ->with('admin:id,firstname,lastname')
                                             ->firstOrFail();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Sustainability initiative retrieved successfully',
            'data' => $initiative
        ]);
    }
}
```

**Response Example:**
```json
{
  "status": "success",
  "data": {
    "id": 1,
    "title": "Zero Waste Fashion Initiative",
    "description": "...",
    "category": "environmental",
    "status": "active",
    "target_amount": 1000000.00,
    "current_amount": 350000.00,
    "progress_percentage": 35.00,
    "partners": ["NGO", "Government"],
    "participant_count": 150
  }
}
```

---

### 5. Public Adverts

**What to add:**
- `GET /api/adverts` - List active adverts (public)
- `GET /api/adverts/{id}` - View single advert (public)

**Files to modify:**
- `routes/api.php` - Add public routes
- Create new `app/Http/Controllers/API/AdvertController.php` (public version)

**Implementation:**

#### Step 5.1: Add Routes
```php
// In routes/api.php - PUBLIC section

// PUBLIC ADVERTS
Route::prefix('adverts')->group(function () {
    Route::get('/', [AdvertController::class, 'index']);
    Route::get('/{id}', [AdvertController::class, 'show']);
});
```

#### Step 5.2: Create Controller
```php
// Create: app/Http/Controllers/API/AdvertController.php

<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Advert;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdvertController extends Controller
{
    /**
     * Get active adverts (public)
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'position' => 'nullable|in:banner,sidebar,footer,popup',
            'per_page' => 'nullable|integer|min:1|max:50'
        ]);
        
        $query = Advert::where('status', 'active')
                      ->where(function($q) {
                          $q->whereNull('start_date')
                            ->orWhere('start_date', '<=', Carbon::now());
                      })
                      ->where(function($q) {
                          $q->whereNull('end_date')
                            ->orWhere('end_date', '>=', Carbon::now());
                      });
        
        // Filter by position if provided
        if ($request->filled('position')) {
            $query->where('position', $request->position);
        }
        
        $perPage = $request->input('per_page', 10);
        $adverts = $query->orderBy('priority', 'desc')->paginate($perPage);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Adverts retrieved successfully',
            'data' => $adverts
        ]);
    }
    
    /**
     * Get single advert (public)
     */
    public function show(int $id): JsonResponse
    {
        $advert = Advert::where('id', $id)
                       ->where('status', 'active')
                       ->firstOrFail();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Advert retrieved successfully',
            'data' => $advert
        ]);
    }
}
```

**Response Example:**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "title": "Summer Sale",
      "description": "50% off all items",
      "image_url": "https://...",
      "action_url": "https://...",
      "position": "banner",
      "priority": 10
    }
  ]
}
```

---

### 6. User Logout

**What to add:**
- `POST /api/logout` - Revoke current user token

**Files to modify:**
- `routes/api.php` - Add auth route

**Implementation:**

```php
// In routes/api.php - Inside auth:sanctum group (around line 95)

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
```

**Response:**
```json
{
  "status": "success",
  "message": "Logged out successfully"
}
```

---

### 7. Shopping Cart (Server-Side)

**What to add:**
- `GET /api/cart` - Get user's cart
- `POST /api/cart/items` - Add item to cart
- `PATCH /api/cart/items/{id}` - Update cart item quantity
- `DELETE /api/cart/items/{id}` - Remove item from cart
- `DELETE /api/cart` - Clear entire cart

**Files to create:**
- `app/Models/Cart.php`
- `app/Models/CartItem.php`
- `app/Http/Controllers/API/CartController.php`
- Migration file for cart tables

**Implementation:**

#### Step 7.1: Create Migration
```bash
php artisan make:migration create_carts_and_cart_items_tables
```

```php
// Migration file
public function up(): void
{
    Schema::create('carts', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->timestamps();
    });
    
    Schema::create('cart_items', function (Blueprint $table) {
        $table->id();
        $table->foreignId('cart_id')->constrained()->onDelete('cascade');
        $table->foreignId('product_id')->constrained()->onDelete('cascade');
        $table->integer('quantity')->default(1);
        $table->decimal('unit_price', 10, 2);
        $table->timestamps();
        
        // Prevent duplicate products in same cart
        $table->unique(['cart_id', 'product_id']);
    });
}
```

#### Step 7.2: Create Models
```php
// app/Models/Cart.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    protected $fillable = ['user_id'];
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }
    
    public function getTotalAttribute(): float
    {
        return $this->items->sum(function ($item) {
            return $item->unit_price * $item->quantity;
        });
    }
}

// app/Models/CartItem.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $fillable = ['cart_id', 'product_id', 'quantity', 'unit_price'];
    
    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2'
    ];
    
    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }
    
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    
    public function getSubtotalAttribute(): float
    {
        return $this->unit_price * $this->quantity;
    }
}
```

#### Step 7.3: Create Controller
```php
// app/Http/Controllers/API/CartController.php
<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /**
     * Get user's cart
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);
        
        $cart->load(['items.product.sellerProfile:id,business_name']);
        
        return response()->json([
            'status' => 'success',
            'data' => [
                'cart_id' => $cart->id,
                'items' => $cart->items,
                'total' => $cart->total,
                'items_count' => $cart->items->count()
            ]
        ]);
    }
    
    /**
     * Add item to cart
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);
        
        $user = Auth::user();
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);
        
        $product = Product::where('id', $request->product_id)
                         ->where('status', 'approved')
                         ->firstOrFail();
        
        // Check if item already exists
        $existingItem = CartItem::where('cart_id', $cart->id)
                                ->where('product_id', $product->id)
                                ->first();
        
        if ($existingItem) {
            // Update quantity
            $existingItem->quantity += $request->quantity;
            $existingItem->save();
            $cartItem = $existingItem;
        } else {
            // Create new cart item
            $cartItem = CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'unit_price' => $product->price
            ]);
        }
        
        $cartItem->load('product');
        
        return response()->json([
            'status' => 'success',
            'message' => 'Item added to cart',
            'data' => [
                'cart_item' => $cartItem,
                'cart_total' => $cart->fresh()->total
            ]
        ], 201);
    }
    
    /**
     * Update cart item quantity
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);
        
        $user = Auth::user();
        $cart = Cart::where('user_id', $user->id)->firstOrFail();
        
        $cartItem = CartItem::where('cart_id', $cart->id)
                           ->where('id', $id)
                           ->firstOrFail();
        
        $cartItem->quantity = $request->quantity;
        $cartItem->save();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Cart item updated',
            'data' => [
                'cart_item' => $cartItem,
                'cart_total' => $cart->fresh()->total
            ]
        ]);
    }
    
    /**
     * Remove item from cart
     */
    public function destroy(int $id): JsonResponse
    {
        $user = Auth::user();
        $cart = Cart::where('user_id', $user->id)->firstOrFail();
        
        $cartItem = CartItem::where('cart_id', $cart->id)
                           ->where('id', $id)
                           ->firstOrFail();
        
        $cartItem->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Item removed from cart',
            'data' => [
                'cart_total' => $cart->fresh()->total,
                'items_count' => $cart->fresh()->items->count()
            ]
        ]);
    }
    
    /**
     * Clear entire cart
     */
    public function clear(): JsonResponse
    {
        $user = Auth::user();
        $cart = Cart::where('user_id', $user->id)->first();
        
        if ($cart) {
            $cart->items()->delete();
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Cart cleared successfully'
        ]);
    }
}
```

#### Step 7.4: Add Routes
```php
// In routes/api.php - Inside auth:sanctum group

// SHOPPING CART
Route::prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'index']);
    Route::post('/items', [CartController::class, 'store']);
    Route::patch('/items/{id}', [CartController::class, 'update']);
    Route::delete('/items/{id}', [CartController::class, 'destroy']);
    Route::delete('/', [CartController::class, 'clear']);
});
```

---

### 8. Order Cancellation

**What to add:**
- `POST /api/orders/{id}/cancel` - Cancel order (user)

**Files to modify:**
- `routes/api.php` - Add route
- `app/Http/Controllers/API/OrderController.php` - Add method

**Implementation:**

```php
// Add route in routes/api.php (inside auth group, after existing order routes)
Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel']);

// Add method in OrderController.php
/**
 * Cancel order (user can cancel pending/processing orders)
 */
public function cancel(Request $request, int $id): JsonResponse
{
    $request->validate([
        'cancellation_reason' => 'required|string|max:500'
    ]);
    
    $user = Auth::user();
    $order = Order::where('id', $id)
                 ->where('user_id', $user->id)
                 ->firstOrFail();
    
    // Business rules: can only cancel pending or processing orders
    if (!in_array($order->status, ['pending', 'processing'])) {
        return response()->json([
            'status' => 'error',
            'message' => "Cannot cancel order with status '{$order->status}'. Only pending or processing orders can be cancelled."
        ], 400);
    }
    
    // Update order
    $order->status = 'cancelled';
    $order->cancellation_reason = $request->cancellation_reason;
    $order->save();
    
    // Send notification
    $this->notificationService->sendEmailAndPush(
        $user,
        'Order Cancelled - Oja Ewa',
        'order_cancelled',
        'Order Cancelled',
        "Your order #{$order->id} has been cancelled.",
        ['order' => $order],
        ['order_id' => $order->id, 'deep_link' => "/orders/{$order->id}"]
    );
    
    return response()->json([
        'status' => 'success',
        'message' => 'Order cancelled successfully',
        'data' => $order
    ]);
}
```

---

## 📝 Summary - All New Endpoints

### Public (No Auth):
1. `GET /api/business/public` - List businesses
2. `GET /api/business/public/{id}` - View business
3. `GET /api/products/browse` - Browse products
4. `GET /api/products/public/{id}` - View product
5. `GET /api/products/filters` - Get filter metadata
6. `GET /api/sustainability` - List initiatives
7. `GET /api/sustainability/{id}` - View initiative
8. `GET /api/adverts` - List adverts
9. `GET /api/adverts/{id}` - View advert

### Authenticated:
10. `POST /api/logout` - User logout
11. `GET /api/cart` - Get cart
12. `POST /api/cart/items` - Add to cart
13. `PATCH /api/cart/items/{id}` - Update cart item
14. `DELETE /api/cart/items/{id}` - Remove from cart
15. `DELETE /api/cart` - Clear cart
16. `POST /api/orders/{id}/cancel` - Cancel order

**Total: 16 new endpoints**

---

## 🚀 Implementation Order

1. Part 1 endpoints (Business, Products, Filters)
2. Public content (Sustainability, Adverts)
3. User features (Logout, Cart, Cancel)
4. Test all endpoints
5. Update documentation

---

**Status:** Part 2 - Ready for implementation
