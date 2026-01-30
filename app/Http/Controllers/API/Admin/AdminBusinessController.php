<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessProfile;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdminBusinessController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
    /**
     * List business profiles by category, paginated
     * 
     * @param Request $request
     * @param string $category school only (art is products now)
     * @return JsonResponse
     */
    public function index(Request $request, $category): JsonResponse
    {
        // Validate category - only school is a business directory now
        if ($category !== 'school') {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid business category. Only "school" is supported.'
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
     * @param string $category school only (art is products now)
     * @param int $id
     * @return JsonResponse
     */
    public function show($category, $id): JsonResponse
    {
        // Validate category - only school is a business directory now
        if ($category !== 'school') {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid business category. Only "school" is supported.'
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
     * @param string $category school only (art is products now)
     * @param int $id
     * @return JsonResponse
     */
    public function updateStatus(Request $request, $category, $id): JsonResponse
    {
        // Validate category - only school is a business directory now
        if ($category !== 'school') {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid business category. Only "school" is supported.'
            ], 422);
        }
        
        $request->validate([
            'store_status' => 'required|in:pending,approved,deactivated',
            'rejection_reason' => 'nullable|string',
        ]);
        
        $business = BusinessProfile::with('user')->where('category', $category)
            ->findOrFail($id);
        
        $oldStatus = $business->store_status;
        $newStatus = $request->store_status;
        
        $business->store_status = $newStatus;
        
        if ($newStatus === 'deactivated' && $request->has('rejection_reason')) {
            $business->rejection_reason = $request->rejection_reason;
        }
        
        $business->save();
        
        // Send business approval/rejection notifications if status changed
        if ($oldStatus !== $newStatus && in_array($newStatus, ['approved', 'deactivated'])) {
            $this->notificationService->sendEmailAndPush(
                $business->user,
                'Business Profile Update - Oja Ewa',
                'business_approved',
                $newStatus === 'approved' ? 'Business Approved!' : 'Business Profile Needs Update',
                $newStatus === 'approved' 
                    ? "Congratulations! Your {$business->business_name} profile has been approved."
                    : "Your {$business->business_name} profile needs some updates before approval.",
                [
                    'business' => $business,
                    'status' => $newStatus,
                    'rejectionReason' => $business->rejection_reason
                ],
                [
                    'business_id' => $business->id,
                    'status' => $newStatus,
                    'deep_link' => "/business/{$business->id}"
                ]
            );
        }
        
        return response()->json([
            'status' => 'success',
            'message' => "Business profile status updated to {$request->store_status} successfully",
            'data' => $business
        ]);
    }
}
