<?php

namespace App\Http\Controllers;

use App\Models\SellerProfile;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SellerProfileController extends Controller
{
    /**
     * Display the authenticated user's seller profile.
     */
    public function show(): JsonResponse
    {
        $user = Auth::user();
        $sellerProfile = $user->sellerProfile;

        if (!$sellerProfile) {
            return response()->json(['message' => 'Seller profile not found'], 404);
        }

        // Calculate seller statistics
        $totalSales = $user->orders()
            ->whereHas('orderItems.product.sellerProfile', function($query) use ($sellerProfile) {
                $query->where('id', $sellerProfile->id);
            })
            ->where('status', 'paid')
            ->sum('total_price');
            
        $avgRating = $user->sellerProfile->products()
            ->whereHas('reviews')
            ->with('reviews')
            ->get()
            ->flatMap(function ($product) {
                return $product->reviews;
            })
            ->avg('rating');

        // Add calculated fields to seller profile
        $sellerData = $sellerProfile->toArray();
        $sellerData['selling_since'] = $sellerProfile->created_at;
        $sellerData['total_sales'] = round($totalSales, 2);
        $sellerData['avg_rating'] = round($avgRating ?: 0, 1);

        return response()->json($sellerData);
    }

    /**
     * Store a newly created seller profile.
     */
    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();

        // Check if user already has a seller profile
        if ($user->sellerProfile) {
            return response()->json(['message' => 'User already has a seller profile'], 409);
        }

        $validated = $request->validate([
            'country' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'address' => 'required|string',
            'business_email' => 'required|email|max:255',
            'business_phone_number' => 'required|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'facebook' => 'nullable|string|max:255',
            'identity_document' => 'nullable|string|max:255',
            'business_name' => 'required|string|max:255',
            'business_registration_number' => 'required|string|max:255',
            'business_certificate' => 'nullable|string|max:255',
            'business_logo' => 'nullable|string|max:255',
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
        ]);

        $validated['user_id'] = $user->id;
        $sellerProfile = SellerProfile::create($validated);

        return response()->json($sellerProfile, 201);
    }

    /**
     * Update the authenticated user's seller profile.
     */
    public function update(Request $request): JsonResponse
    {
        $user = Auth::user();
        $sellerProfile = $user->sellerProfile;

        if (!$sellerProfile) {
            return response()->json(['message' => 'Seller profile not found'], 404);
        }

        $validated = $request->validate([
            'country' => 'sometimes|required|string|max:255',
            'state' => 'sometimes|required|string|max:255',
            'city' => 'sometimes|required|string|max:255',
            'address' => 'sometimes|required|string',
            'business_email' => 'sometimes|required|email|max:255',
            'business_phone_number' => 'sometimes|required|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'facebook' => 'nullable|string|max:255',
            'identity_document' => 'nullable|string|max:255',
            'business_name' => 'sometimes|required|string|max:255',
            'business_registration_number' => 'sometimes|required|string|max:255',
            'business_certificate' => 'nullable|string|max:255',
            'business_logo' => 'nullable|string|max:255',
            'bank_name' => 'sometimes|required|string|max:255',
            'account_number' => 'sometimes|required|string|max:255',
        ]);

        $sellerProfile->update($validated);

        return response()->json($sellerProfile);
    }

    /**
     * Soft delete the authenticated user's seller profile.
     */
    public function destroy(Request $request): JsonResponse
    {
        $user = Auth::user();
        $sellerProfile = $user->sellerProfile;

        if (!$sellerProfile) {
            return response()->json(['message' => 'Seller profile not found'], 404);
        }

        // Optional reason for deletion
        $reason = $request->input('reason');
        
        $sellerProfile->delete();

        return response()->json(['message' => 'Seller profile deleted successfully']);
    }

    /**
     * Handle file uploads for seller profile documents.
     */
    public function uploadFile(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
            'type' => 'required|in:identity_document,business_certificate,business_logo',
        ]);

        $user = Auth::user();
        $sellerProfile = $user->sellerProfile;

        if (!$sellerProfile) {
            return response()->json(['message' => 'Seller profile not found'], 404);
        }

        // Mock file upload to DigitalOcean Spaces
        $file = $request->file('file');
        $type = $request->input('type');
        
        // In a real implementation, you would upload to DigitalOcean Spaces here
        $mockFilePath = 'spaces/' . $type . '/' . uniqid() . '.' . $file->getClientOriginalExtension();
        
        // Update the seller profile with the file path
        $sellerProfile->update([
            $type => $mockFilePath
        ]);

        return response()->json([
            'message' => 'File uploaded successfully',
            'file_path' => $mockFilePath,
            'type' => $type
        ]);
    }

    /**
     * Get public seller profile details
     */
    public function publicShow(int $id): JsonResponse
    {
        try {
            $seller = SellerProfile::where('id', $id)
                ->where('registration_status', 'approved')
                ->where('active', true)
                ->firstOrFail();
            
            // Calculate seller statistics
            $avgRating = $seller->products()
                ->whereHas('reviews')
                ->with('reviews')
                ->get()
                ->flatMap(fn($product) => $product->reviews)
                ->avg('rating');
            
            $totalReviews = $seller->products()
                ->withCount('reviews')
                ->get()
                ->sum('reviews_count');
            
            return response()->json([
                'status' => 'success',
                'data' => [
                    'id' => $seller->id,
                    'business_name' => $seller->business_name,
                    'business_logo' => $seller->business_logo,
                    'business_email' => $seller->business_email,
                    'business_phone_number' => $seller->business_phone_number,
                    'city' => $seller->city,
                    'state' => $seller->state,
                    'country' => $seller->country,
                    'instagram' => $seller->instagram,
                    'facebook' => $seller->facebook,
                    'selling_since' => $seller->created_at,
                    'avg_rating' => round($avgRating ?: 0, 1),
                    'total_reviews' => $totalReviews,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Seller not found or not active'
            ], 404);
        }
    }
    
    /**
     * Get products from a specific seller
     */
    public function products(int $id): JsonResponse
    {
        try {
            $seller = SellerProfile::where('id', $id)
                ->where('registration_status', 'approved')
                ->where('active', true)
                ->firstOrFail();
            
            $products = $seller->products()
                ->where('status', 'approved')
                ->with('sellerProfile:id,business_name')
                ->orderBy('created_at', 'desc')
                ->paginate(15);
            
            return response()->json([
                'status' => 'success',
                'data' => $products
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Seller not found or not active'
            ], 404);
        }
    }
}
