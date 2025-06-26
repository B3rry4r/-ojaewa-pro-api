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
        
        // The avg_rating is already available as an accessor
        // No need to manually set it
        
        return response()->json($product);
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
}
