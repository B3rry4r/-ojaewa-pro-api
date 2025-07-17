<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use App\Models\Product;
use App\Models\BusinessProfile;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class WishlistController extends Controller
{
    /**
     * Get all wishlist items for the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $wishlists = $request->user()->wishlists()
            ->with('wishlistable')
            ->latest()
            ->paginate(20);

        return response()->json([
            'status' => 'success',
            'data' => $wishlists,
        ]);
    }

    /**
     * Add item to wishlist.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'wishlistable_type' => ['required', 'string', Rule::in(['product', 'business_profile'])],
            'wishlistable_id' => 'required|integer',
        ]);

        $wishlistableType = $request->wishlistable_type === 'product' ? Product::class : BusinessProfile::class;
        
        // Check if the wishlistable item exists
        $wishlistable = $wishlistableType::find($request->wishlistable_id);
        if (!$wishlistable) {
            return response()->json([
                'status' => 'error',
                'message' => 'Item not found',
            ], 404);
        }

        // Check if already in wishlist
        $existing = Wishlist::where([
            'user_id' => $request->user()->id,
            'wishlistable_id' => $request->wishlistable_id,
            'wishlistable_type' => $wishlistableType,
        ])->first();

        if ($existing) {
            return response()->json([
                'status' => 'error',
                'message' => 'Item already in wishlist',
            ], 409);
        }

        $wishlist = Wishlist::create([
            'user_id' => $request->user()->id,
            'wishlistable_id' => $request->wishlistable_id,
            'wishlistable_type' => $wishlistableType,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Item added to wishlist',
            'data' => $wishlist->load('wishlistable'),
        ], 201);
    }

    /**
     * Remove item from wishlist.
     */
    public function destroy(Request $request): JsonResponse
    {
        $request->validate([
            'wishlistable_type' => ['required', 'string', Rule::in(['product', 'business_profile'])],
            'wishlistable_id' => 'required|integer',
        ]);

        $wishlistableType = $request->wishlistable_type === 'product' ? Product::class : BusinessProfile::class;

        $wishlist = Wishlist::where([
            'user_id' => $request->user()->id,
            'wishlistable_id' => $request->wishlistable_id,
            'wishlistable_type' => $wishlistableType,
        ])->first();

        if (!$wishlist) {
            return response()->json([
                'status' => 'error',
                'message' => 'Item not found in wishlist',
            ], 404);
        }

        $wishlist->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Item removed from wishlist',
        ]);
    }
}
