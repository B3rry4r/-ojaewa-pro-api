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

        return response()->json($sellerProfile);
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
}
