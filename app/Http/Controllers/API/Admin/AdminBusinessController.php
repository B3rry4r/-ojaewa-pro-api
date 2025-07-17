<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessProfile;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdminBusinessController extends Controller
{
    /**
     * List business profiles by category, paginated
     * 
     * @param Request $request
     * @param string $category beauty|brand|school|music
     * @return JsonResponse
     */
    public function index(Request $request, $category): JsonResponse
    {
        // Validate category
        if (!in_array($category, ['beauty', 'brand', 'school', 'music'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid business category'
            ], 422);
        }
        
        $query = BusinessProfile::with('user')
            ->where('category', $category);
        
        // Filter by status if provided
        if ($request->has('store_status')) {
            $query->where('store_status', $request->store_status);
        }
        
        // Sort by created_at by default
        $businesses = $query->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return response()->json([
            'status' => 'success',
            'message' => "{$category} businesses retrieved successfully",
            'data' => $businesses
        ]);
    }
    
    /**
     * Show details for a specific business profile
     * 
     * @param string $category beauty|brand|school|music
     * @param int $id
     * @return JsonResponse
     */
    public function show($category, $id): JsonResponse
    {
        // Validate category
        if (!in_array($category, ['beauty', 'brand', 'school', 'music'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid business category'
            ], 422);
        }
        
        $business = BusinessProfile::with('user')
            ->where('category', $category)
            ->findOrFail($id);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Business profile retrieved successfully',
            'data' => $business
        ]);
    }
    
    /**
     * Update business profile status
     * 
     * @param Request $request
     * @param string $category beauty|brand|school|music
     * @param int $id
     * @return JsonResponse
     */
    public function updateStatus(Request $request, $category, $id): JsonResponse
    {
        // Validate category
        if (!in_array($category, ['beauty', 'brand', 'school', 'music'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid business category'
            ], 422);
        }
        
        $request->validate([
            'store_status' => 'required|in:pending,approved,deactivated',
            'rejection_reason' => 'required_if:store_status,rejected|string|nullable',
        ]);
        
        $business = BusinessProfile::where('category', $category)
            ->findOrFail($id);
        
        $business->store_status = $request->store_status;
        
        if ($request->store_status === 'deactivated' && $request->has('rejection_reason')) {
            $business->rejection_reason = $request->rejection_reason;
        }
        
        $business->save();
        
        return response()->json([
            'status' => 'success',
            'message' => "Business profile status updated to {$request->store_status} successfully",
            'data' => $business
        ]);
    }
}
