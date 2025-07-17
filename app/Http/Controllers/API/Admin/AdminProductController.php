<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdminProductController extends Controller
{
    /**
     * List all products paginated
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = Product::with(['sellerProfile.user']);
        
        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        // Sort by created_at by default
        $products = $query->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Products retrieved successfully',
            'data' => [
                'data' => $products->items(),
                'links' => [
                    'first' => $products->url(1),
                    'last' => $products->url($products->lastPage()),
                    'prev' => $products->previousPageUrl(),
                    'next' => $products->nextPageUrl(),
                ],
                'meta' => [
                    'current_page' => $products->currentPage(),
                    'from' => $products->firstItem(),
                    'last_page' => $products->lastPage(),
                    'path' => $products->path(),
                    'per_page' => $products->perPage(),
                    'to' => $products->lastItem(),
                    'total' => $products->total(),
                ],
            ]
        ]);
    }
    
    /**
     * Get list of pending products
     * 
     * @return JsonResponse
     */
    public function pendingProducts(): JsonResponse
    {
        $pendingProducts = Product::with(['sellerProfile.user'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->paginate(15);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Pending products retrieved successfully',
            'data' => [
                'data' => $pendingProducts->items(),
                'links' => [
                    'first' => $pendingProducts->url(1),
                    'last' => $pendingProducts->url($pendingProducts->lastPage()),
                    'prev' => $pendingProducts->previousPageUrl(),
                    'next' => $pendingProducts->nextPageUrl(),
                ],
                'meta' => [
                    'current_page' => $pendingProducts->currentPage(),
                    'from' => $pendingProducts->firstItem(),
                    'last_page' => $pendingProducts->lastPage(),
                    'path' => $pendingProducts->path(),
                    'per_page' => $pendingProducts->perPage(),
                    'to' => $pendingProducts->lastItem(),
                    'total' => $pendingProducts->total(),
                ],
            ]
        ]);
    }
    
    /**
     * Approve or reject a product
     * 
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function approveProduct(Request $request, $id): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'rejection_reason' => 'required_if:status,rejected|string|nullable',
        ]);
        
        $product = Product::findOrFail($id);
        $product->status = $request->status;
        
        if ($request->status === 'rejected' && $request->has('rejection_reason')) {
            $product->rejection_reason = $request->rejection_reason;
        }
        
        $product->save();
        
        return response()->json([
            'status' => 'success',
            'message' => "Product {$request->status} successfully",
            'data' => $product
        ]);
    }
    
    /**
     * Update product status (active/inactive)
     * 
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function updateStatus(Request $request, $id): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
        ]);
        
        $product = Product::findOrFail($id);
        $product->status = $request->status;
        $product->save();
        
        return response()->json([
            'status' => 'success',
            'message' => "Product status updated to {$request->status} successfully",
            'data' => $product
        ]);
    }
}
