# Public Endpoints Implementation Plan

## Overview
This plan addresses 9 critical missing public endpoints that prevent the mobile app from functioning correctly.

---

## 🎯 Issues to Fix

### Critical (Blocks core functionality):
1. Category items return IDs only (no card data)
2. Business profiles are owner-only (public can't view)
3. Product endpoints are seller-only (buyers can't browse)
4. Sustainability initiatives are admin-only (public can't view)
5. Adverts are admin-only (public can't view)

### High Priority (Poor UX):
6. No user logout endpoint
7. No filters/sort metadata endpoint

### Medium Priority (Missing features):
8. No cart endpoints (if server-side cart needed)
9. No user order cancellation endpoint

---

## 📋 Implementation Plan - Part 1: Critical Public Endpoints

### 1. Public Business Profiles

**What to add:**
- `GET /api/business/public` - List all approved businesses
- `GET /api/business/public/{id}` - View single approved business

**Files to modify:**
- `routes/api.php` - Add routes OUTSIDE auth group
- `app/Http/Controllers/API/BusinessProfileController.php` - Add 2 new methods

**Implementation:**

#### Step 1.1: Add Routes
```php
// In routes/api.php - OUTSIDE auth:sanctum group (public section)
// Add after line 93 (before AUTHENTICATED USER ROUTES comment)

// PUBLIC BUSINESS PROFILES
Route::prefix('business/public')->group(function () {
    Route::get('/', [BusinessProfileController::class, 'publicIndex']);
    Route::get('/{id}', [BusinessProfileController::class, 'publicShow']);
});
```

#### Step 1.2: Add Controller Methods
```php
// In BusinessProfileController.php - Add these methods

/**
 * Get all approved business profiles (public)
 */
public function publicIndex(Request $request): JsonResponse
{
    $query = BusinessProfile::where('store_status', 'approved')
                           ->with('user:id,firstname,lastname');
    
    // Optional filters
    if ($request->has('category')) {
        $query->where('category', $request->category);
    }
    
    if ($request->has('offering_type')) {
        $query->where('offering_type', $request->offering_type);
    }
    
    $perPage = $request->input('per_page', 15);
    $businesses = $query->latest()->paginate($perPage);
    
    return response()->json([
        'status' => 'success',
        'message' => 'Public business profiles retrieved successfully',
        'data' => $businesses
    ]);
}

/**
 * Get single approved business profile (public)
 */
public function publicShow(int $id): JsonResponse
{
    $business = BusinessProfile::where('id', $id)
                               ->where('store_status', 'approved')
                               ->with('user:id,firstname,lastname')
                               ->firstOrFail();
    
    return response()->json([
        'status' => 'success',
        'message' => 'Business profile retrieved successfully',
        'data' => $business
    ]);
}
```

**Expected Response:**
```json
{
  "status": "success",
  "data": {
    "id": 1,
    "business_name": "Glam Beauty Studio",
    "category": "beauty",
    "business_description": "...",
    "business_logo": "...",
    "store_status": "approved",
    "user": {
      "id": 1,
      "firstname": "Jane",
      "lastname": "Doe"
    }
  }
}
```

---

### 2. Public Product Browsing

**What to add:**
- `GET /api/products/browse` - Public product search/listing
- `GET /api/products/public/{id}` - View single product (public)

**Files to modify:**
- `routes/api.php` - Add public routes
- `app/Http/Controllers/API/ProductController.php` - Add 2 methods

**Implementation:**

#### Step 2.1: Add Routes
```php
// In routes/api.php - PUBLIC section (before auth group)

// PUBLIC PRODUCT BROWSING
Route::prefix('products')->group(function () {
    Route::get('/browse', [ProductController::class, 'browse']);
    Route::get('/public/{id}', [ProductController::class, 'publicShow']);
});
```

#### Step 2.2: Add Controller Methods
```php
// In ProductController.php

/**
 * Browse products (public - approved only)
 */
public function browse(Request $request): JsonResponse
{
    $request->validate([
        'q' => 'nullable|string|max:255',
        'gender' => 'nullable|in:male,female,unisex',
        'style' => 'nullable|string|max:100',
        'tribe' => 'nullable|string|max:100',
        'price_min' => 'nullable|numeric|min:0',
        'price_max' => 'nullable|numeric|min:0',
        'sort' => 'nullable|in:price_asc,price_desc,newest,popular',
        'per_page' => 'nullable|integer|min:1|max:50'
    ]);
    
    $query = Product::where('status', 'approved')
                   ->with(['sellerProfile:id,business_name']);
    
    // Search
    if ($request->filled('q')) {
        $search = $request->q;
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }
    
    // Filters
    if ($request->filled('gender')) {
        $query->where('gender', $request->gender);
    }
    
    if ($request->filled('style')) {
        $query->where('style', 'like', "%{$request->style}%");
    }
    
    if ($request->filled('tribe')) {
        $query->where('tribe', 'like', "%{$request->tribe}%");
    }
    
    if ($request->filled('price_min')) {
        $query->where('price', '>=', $request->price_min);
    }
    
    if ($request->filled('price_max')) {
        $query->where('price', '<=', $request->price_max);
    }
    
    // Sorting
    switch ($request->input('sort', 'newest')) {
        case 'price_asc':
            $query->orderBy('price', 'asc');
            break;
        case 'price_desc':
            $query->orderBy('price', 'desc');
            break;
        case 'popular':
            $query->withCount('reviews')->orderBy('reviews_count', 'desc');
            break;
        case 'newest':
        default:
            $query->latest();
            break;
    }
    
    $perPage = $request->input('per_page', 10);
    $products = $query->paginate($perPage);
    
    return response()->json([
        'status' => 'success',
        'data' => $products
    ]);
}

/**
 * View single product (public)
 */
public function publicShow(int $id): JsonResponse
{
    $product = Product::where('id', $id)
                     ->where('status', 'approved')
                     ->with([
                         'sellerProfile:id,business_name,business_email,city,state',
                         'reviews.user:id,firstname,lastname'
                     ])
                     ->firstOrFail();
    
    // Get suggestions (same as show method)
    $suggestions = Product::where('status', 'approved')
                         ->where('id', '!=', $product->id)
                         ->where(function($q) use ($product) {
                             $q->where('style', $product->style)
                               ->orWhere('tribe', $product->tribe)
                               ->orWhere('gender', $product->gender);
                         })
                         ->with('sellerProfile:id,business_name')
                         ->inRandomOrder()
                         ->limit(5)
                         ->get();
    
    return response()->json([
        'status' => 'success',
        'data' => [
            'product' => $product,
            'suggestions' => $suggestions
        ]
    ]);
}
```

---

### 3. Product Filters Metadata

**What to add:**
- `GET /api/products/filters` - Get all available filter values

**Implementation:**

```php
// Add route
Route::get('/products/filters', [ProductController::class, 'filters']);

// Add controller method
public function filters(): JsonResponse
{
    $filters = [
        'genders' => Product::select('gender')
                           ->where('status', 'approved')
                           ->distinct()
                           ->pluck('gender')
                           ->filter()
                           ->values(),
        
        'styles' => Product::select('style')
                          ->where('status', 'approved')
                          ->distinct()
                          ->pluck('style')
                          ->filter()
                          ->values(),
        
        'tribes' => Product::select('tribe')
                          ->where('status', 'approved')
                          ->distinct()
                          ->pluck('tribe')
                          ->filter()
                          ->values(),
        
        'price_range' => Product::where('status', 'approved')
                               ->selectRaw('MIN(price) as min, MAX(price) as max')
                               ->first(),
        
        'sort_options' => [
            ['value' => 'newest', 'label' => 'Newest First'],
            ['value' => 'price_asc', 'label' => 'Price: Low to High'],
            ['value' => 'price_desc', 'label' => 'Price: High to Low'],
            ['value' => 'popular', 'label' => 'Most Popular']
        ]
    ];
    
    return response()->json([
        'status' => 'success',
        'data' => $filters
    ]);
}
```

**Response:**
```json
{
  "status": "success",
  "data": {
    "genders": ["male", "female", "unisex"],
    "styles": ["Traditional", "Modern", "Casual"],
    "tribes": ["Yoruba", "Igbo", "Hausa"],
    "price_range": {"min": 5000, "max": 150000},
    "sort_options": [...]
  }
}
```

---

## 📝 Testing Plan - Part 1

After implementing Part 1:

```bash
# Test public business profiles
curl http://localhost:8000/api/business/public

# Test single business
curl http://localhost:8000/api/business/public/1

# Test product browse
curl http://localhost:8000/api/products/browse?q=ankara

# Test product filters
curl http://localhost:8000/api/products/filters

# Test public product view
curl http://localhost:8000/api/products/public/1
```

---

**Status:** Part 1 - Ready for review
**Next:** Part 2 will cover Sustainability, Adverts, and User features
