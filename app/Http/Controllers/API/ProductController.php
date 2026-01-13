<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * Create a new ProductController instance.
     * 
     * @return void
     */
    public function __construct()
    {
        // Authentication will be handled by the sanctum middleware in routes
    }
    
    /**
     * Display a listing of the products.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        // Check if user has a seller profile
        $user = Auth::user();
        
        if (!$user->sellerProfile) {
            return response()->json([
                'message' => 'You must have a seller profile to view products'
            ], 403);
        }
        
        // Get products for this seller
        $products = Product::where('seller_profile_id', $user->sellerProfile->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return response()->json($products);
    }

    /**
     * Store a newly created product in storage.
     *
     * @param  \App\Http\Requests\StoreProductRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        // Get seller profile ID from authenticated user
        $sellerProfile = Auth::user()->sellerProfile;
        
        // Create the product with validated data
        $product = new Product($request->validated());
        $product->seller_profile_id = $sellerProfile->id;
        $product->status = 'pending'; // Default status is pending
        $product->save();
        
        return response()->json([
            'message' => 'Product created successfully',
            'product' => $product
        ], 201);
    }

    /**
     * Display the specified product.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $product = Product::with(['sellerProfile', 'reviews'])->findOrFail($id);
        
        // Get "You may also like" suggestions (max 5)
        $suggestions = Product::where('status', 'approved')
            ->where('id', '!=', $id)
            ->where(function($query) use ($product) {
                $query->where('style', $product->style)
                      ->orWhere('tribe', $product->tribe)
                      ->orWhere('gender', $product->gender);
            })
            ->with('sellerProfile')
            ->inRandomOrder()
            ->limit(5)
            ->get();
        
        // Convert product to array and add suggestions
        $productData = $product->toArray();
        $productData['suggestions'] = $suggestions;
        
        return response()->json($productData);
    }

    /**
     * Update the specified product in storage.
     *
     * @param  \App\Http\Requests\UpdateProductRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateProductRequest $request, string $id): JsonResponse
    {
        $product = Product::findOrFail($id);
        
        // The authorization is already checked in the request class
        // Update the product with validated data
        $product->update($request->validated());
        
        return response()->json([
            'message' => 'Product updated successfully',
            'product' => $product
        ]);
    }

    /**
     * Remove the specified product from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        $product = Product::findOrFail($id);
        $user = Auth::user();
        
        // Check if product belongs to authenticated user's seller profile
        if (!$user->sellerProfile || $product->seller_profile_id !== $user->sellerProfile->id) {
            return response()->json([
                'message' => 'Unauthorized to delete this product'
            ], 403);
        }
        
        // Soft delete the product
        $product->delete();
        
        return response()->json([
            'message' => 'Product deleted successfully'
        ]);
    }
    
    /**
     * Get product suggestions based on various criteria.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function suggestions(Request $request): JsonResponse
    {
        // Validate request parameters
        $request->validate([
            'gender' => 'sometimes|in:male,female,unisex',
            'tribe' => 'sometimes|string|max:100',
            'style' => 'sometimes|string|max:100',
            'price_min' => 'sometimes|numeric|min:0',
            'price_max' => 'sometimes|numeric|min:0',
            'limit' => 'sometimes|integer|min:1|max:20',
        ]);
        
        // Start building the query for approved products
        $query = Product::where('status', 'approved');
        
        // Apply filters if provided
        if ($request->has('gender')) {
            $query->where('gender', $request->gender);
        }
        
        if ($request->has('tribe')) {
            $query->where('tribe', 'like', '%' . $request->tribe . '%');
        }
        
        if ($request->has('style')) {
            $query->where('style', 'like', '%' . $request->style . '%');
        }
        
        if ($request->has('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }
        
        if ($request->has('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }
        
        // Get the products with reviews
        // Accept either 'count' or 'limit' parameter for backward compatibility
        $limit = $request->input('count') ?? $request->input('limit', 10); // Default to 10 if not specified
        $products = $query->with('reviews')
                        ->withCount('reviews')
                        ->orderBy('reviews_count', 'desc') // Products with more reviews first
                        ->take($limit)
                        ->get();
        
        // No need to manually add the avg_rating as it's already available as an accessor
        // The accessor is automatically included in the JSON response
        
        return response()->json($products);
    }
    
    /**
     * Search for products
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'required|string|min:1|max:255',
            'gender' => 'sometimes|in:male,female,unisex',
            'style' => 'sometimes|string|max:100',
            'tribe' => 'sometimes|string|max:100',
            'price_min' => 'sometimes|numeric|min:0',
            'price_max' => 'sometimes|numeric|min:0',
            'per_page' => 'sometimes|integer|min:1|max:50',
        ]);

        $query = Product::where('status', 'approved');
        
        // Search in name and description
        $searchTerm = $request->input('q');
        $query->where(function($q) use ($searchTerm) {
            $q->where('name', 'like', '%' . $searchTerm . '%')
              ->orWhere('description', 'like', '%' . $searchTerm . '%');
        });

        // Apply filters
        if ($request->has('gender')) {
            $query->where('gender', $request->gender);
        }

        if ($request->has('style')) {
            $query->where('style', 'like', '%' . $request->style . '%');
        }

        if ($request->has('tribe')) {
            $query->where('tribe', 'like', '%' . $request->tribe . '%');
        }

        if ($request->has('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }

        if ($request->has('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        // Get paginated results with avg ratings
        $perPage = $request->input('per_page', 10);
        $products = $query->with('sellerProfile')
                          ->orderBy('created_at', 'desc')
                          ->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => $products
        ]);
    }

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
        
        // Get suggestions
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

    /**
     * Get product filters metadata (public)
     */
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
}
