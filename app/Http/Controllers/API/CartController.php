<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Get user's cart
     */
    public function index(): JsonResponse
    {
        try {
            $user = Auth::user();
            $cart = Cart::firstOrCreate(['user_id' => $user->id]);
            
            // Explicitly load relationships
            $cart->load(['items' => function($query) {
                $query->with(['product' => function($q) {
                    $q->with('sellerProfile:id,business_name');
                }]);
            }]);
            
            return response()->json([
                'status' => 'success',
                'data' => [
                    'cart_id' => $cart->id,
                    'items' => $cart->items,
                    'total' => $cart->total,
                    'items_count' => $cart->items_count
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve cart',
                'error' => config('app.debug') ? $e->getMessage() : 'An error occurred'
            ], 500);
        }
    }
    
    /**
     * Add item to cart
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'selected_size' => 'nullable|string',
            'processing_time_type' => 'nullable|in:normal,quick_quick'
        ]);
        
        $user = Auth::user();
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);
        
        $product = Product::where('id', $request->product_id)
                         ->where('status', 'approved')
                         ->firstOrFail();
        
        // Check if item with same product, size, and processing type already exists
        $existingItem = CartItem::where('cart_id', $cart->id)
                                ->where('product_id', $product->id)
                                ->where('selected_size', $request->selected_size)
                                ->where('processing_time_type', $request->processing_time_type ?? 'normal')
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
                'unit_price' => $product->price,
                'selected_size' => $request->selected_size,
                'processing_time_type' => $request->processing_time_type ?? 'normal'
            ]);
        }
        
        $cartItem->load('product.sellerProfile:id,business_name');
        
        return response()->json([
            'status' => 'success',
            'message' => 'Item added to cart',
            'data' => [
                'cart_item' => $cartItem,
                'cart_total' => $cart->fresh()->total,
                'items_count' => $cart->fresh()->items_count
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
                'cart_total' => $cart->fresh()->total,
                'items_count' => $cart->fresh()->items_count
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
                'items_count' => $cart->fresh()->items_count
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
