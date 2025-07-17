<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\SellerProfile;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdminSellerController extends Controller
{
    /**
     * List all seller profiles paginated
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = SellerProfile::with('user');
        
        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('registration_status', $request->status);
        }
        
        // Sort by created_at by default
        $sellers = $query->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Seller profiles retrieved successfully',
            'data' => [
                'data' => $sellers->items(),
                'links' => [
                    'first' => $sellers->url(1),
                    'last' => $sellers->url($sellers->lastPage()),
                    'prev' => $sellers->previousPageUrl(),
                    'next' => $sellers->nextPageUrl(),
                ],
                'meta' => [
                    'current_page' => $sellers->currentPage(),
                    'from' => $sellers->firstItem(),
                    'last_page' => $sellers->lastPage(),
                    'path' => $sellers->path(),
                    'per_page' => $sellers->perPage(),
                    'to' => $sellers->lastItem(),
                    'total' => $sellers->total(),
                ],
            ]
        ]);
    }
    
    /**
     * Get list of pending seller profiles
     * 
     * @return JsonResponse
     */
    public function pendingSellers(): JsonResponse
    {
        $pendingSellers = SellerProfile::with('user')
            ->where('registration_status', 'pending')
            ->orderBy('created_at', 'asc')
            ->paginate(15);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Pending seller profiles retrieved successfully',
            'data' => [
                'data' => $pendingSellers->items(),
                'links' => [
                    'first' => $pendingSellers->url(1),
                    'last' => $pendingSellers->url($pendingSellers->lastPage()),
                    'prev' => $pendingSellers->previousPageUrl(),
                    'next' => $pendingSellers->nextPageUrl(),
                ],
                'meta' => [
                    'current_page' => $pendingSellers->currentPage(),
                    'from' => $pendingSellers->firstItem(),
                    'last_page' => $pendingSellers->lastPage(),
                    'path' => $pendingSellers->path(),
                    'per_page' => $pendingSellers->perPage(),
                    'to' => $pendingSellers->lastItem(),
                    'total' => $pendingSellers->total(),
                ],
            ]
        ]);
    }
    
    /**
     * Approve or reject a seller profile
     * 
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function approveSeller(Request $request, $id): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'rejection_reason' => 'required_if:status,rejected|string|nullable',
        ]);
        
        $seller = SellerProfile::findOrFail($id);
        $seller->registration_status = $request->status;
        
        if ($request->status === 'rejected' && $request->has('rejection_reason')) {
            $seller->rejection_reason = $request->rejection_reason;
        }
        
        $seller->save();
        
        return response()->json([
            'status' => 'success',
            'message' => "Seller profile {$request->status} successfully",
            'data' => $seller
        ]);
    }
    
    /**
     * Update seller status (active/inactive)
     * 
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function updateStatus(Request $request, $id): JsonResponse
    {
        $request->validate([
            'active' => 'required|boolean',
        ]);
        
        $seller = SellerProfile::findOrFail($id);
        $seller->active = $request->active;
        $seller->save();
        
        $status = $request->active ? 'activated' : 'deactivated';
        
        return response()->json([
            'status' => 'success',
            'message' => "Seller profile {$status} successfully",
            'data' => $seller
        ]);
    }
}
