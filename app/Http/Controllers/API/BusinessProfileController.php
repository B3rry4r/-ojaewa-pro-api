<?php

namespace App\Http\Controllers\API;

use App\Models\BusinessProfile;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\StoreBusinessProfileRequest;
use App\Http\Requests\UpdateBusinessProfileRequest;

class BusinessProfileController extends Controller
{
    /**
     * Display a listing of the business profiles for the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();
        $businesses = BusinessProfile::where('user_id', $user->id)->get();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Business profiles retrieved successfully',
            'data' => $businesses
        ]);
    }

    /**
     * Store a newly created business profile in storage.
     *
     * @param  \App\Http\Requests\StoreBusinessProfileRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreBusinessProfileRequest $request): JsonResponse
    {
        $user = Auth::user();
        
        // Check if user already has a business in this category
        $existingCategory = BusinessProfile::where('user_id', $user->id)
            ->where('category', $request->category)
            ->exists();
            
        if ($existingCategory) {
            return response()->json([
                'message' => 'You already have a business in this category.'
            ], 422);
        }
        
        // Create a new business profile
        $businessData = $request->validated();
        $businessData['user_id'] = $user->id;
        
        // Handle file uploads if they exist
        if ($request->hasFile('business_logo')) {
            // Stub for file upload - would actually upload to DigitalOcean Spaces
            $businessData['business_logo'] = 'storage/logos/' . uniqid() . '_' . $request->file('business_logo')->getClientOriginalName();
        }
        
        if ($request->hasFile('identity_document')) {
            // Stub for file upload - would actually upload to DigitalOcean Spaces
            $businessData['identity_document'] = 'storage/documents/' . uniqid() . '_' . $request->file('identity_document')->getClientOriginalName();
        }
        
        $business = BusinessProfile::create($businessData);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Business profile created successfully',
            'data' => $business
        ], 201);
    }

    /**
     * Display the specified business profile.
     *
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $user = Auth::user();
        $business = BusinessProfile::findOrFail($id);
        
        // Check if the business belongs to the authenticated user
        if ($business->user_id !== $user->id) {
            return response()->json([
                'message' => 'Unauthorized access to this business profile'
            ], 403);
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Business profile retrieved successfully',
            'data' => $business
        ]);
    }

    /**
     * Update the specified business profile in storage.
     *
     * @param  \App\Http\Requests\UpdateBusinessProfileRequest  $request
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateBusinessProfileRequest $request, string $id): JsonResponse
    {
        $user = Auth::user();
        $business = BusinessProfile::findOrFail($id);
        
        // Check if the business belongs to the authenticated user
        if ($business->user_id !== $user->id) {
            return response()->json([
                'message' => 'Unauthorized access to this business profile'
            ], 403);
        }
        
        // Update business profile
        $businessData = $request->validated();
        
        // Handle file uploads if they exist
        if ($request->hasFile('business_logo')) {
            // Stub for file upload - would actually upload to DigitalOcean Spaces
            $businessData['business_logo'] = 'storage/logos/' . uniqid() . '_' . $request->file('business_logo')->getClientOriginalName();
        }
        
        if ($request->hasFile('identity_document')) {
            // Stub for file upload - would actually upload to DigitalOcean Spaces
            $businessData['identity_document'] = 'storage/documents/' . uniqid() . '_' . $request->file('identity_document')->getClientOriginalName();
        }
        
        $business->update($businessData);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Business profile updated successfully',
            'data' => $business
        ]);
    }

    /**
     * Remove the specified business profile from storage (soft delete).
     *
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        $user = Auth::user();
        $business = BusinessProfile::findOrFail($id);
        
        // Check if the business belongs to the authenticated user
        if ($business->user_id !== $user->id) {
            return response()->json([
                'message' => 'Unauthorized access to this business profile'
            ], 403);
        }
        
        // Soft delete the business profile
        $business->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Business profile deleted successfully'
        ]);
    }
    
    /**
     * Deactivate the specified business profile.
     *
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deactivate(string $id): JsonResponse
    {
        $user = Auth::user();
        $business = BusinessProfile::findOrFail($id);
        
        // Check if the business belongs to the authenticated user
        if ($business->user_id !== $user->id) {
            return response()->json([
                'message' => 'Unauthorized access to this business profile'
            ], 403);
        }
        
        // Update store_status to deactivated
        $business->update(['store_status' => 'deactivated']);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Business profile deactivated successfully',
            'data' => $business
        ]);
    }
    
    /**
     * Upload a file for a business profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request, string $id): JsonResponse
    {
        $user = Auth::user();
        $business = BusinessProfile::findOrFail($id);
        
        // Check if the business belongs to the authenticated user
        if ($business->user_id !== $user->id) {
            return response()->json([
                'message' => 'Unauthorized access to this business profile'
            ], 403);
        }
        
        $request->validate([
            'file' => 'required|file|max:10240', // Max 10MB
            'file_type' => 'required|string|in:business_logo,business_certificates,identity_document',
        ]);
        
        $file = $request->file('file');
        $fileType = $request->file_type;
        
        // Determine storage path based on file type
        if ($fileType === 'business_logo') {
            $path = $file->store('business_logos', 'public');
            $business->update(['business_logo' => 'storage/' . $path]);
        } elseif ($fileType === 'business_certificates') {
            $path = $file->store('business_documents', 'public');
            // For certificates, we'd typically update a JSON field, but for this stub just store the path
            $business->update(['business_certificates' => json_encode(['storage/' . $path])]);
        } elseif ($fileType === 'identity_document') {
            $path = $file->store('business_documents', 'public');
            $business->update(['identity_document' => 'storage/' . $path]);
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'File uploaded successfully',
            'data' => [
                'file_path' => 'storage/' . $path,
                'file_type' => $fileType
            ]
        ]);
    }
}
