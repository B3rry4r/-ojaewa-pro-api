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

    /**
     * Get all approved business profiles (public)
     */
    public function publicIndex(Request $request): JsonResponse
    {
        $query = BusinessProfile::where('store_status', 'approved')
                               ->with('user:id,firstname,lastname');
        
        // Optional filters
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }
        
        if ($request->has('offering_type')) {
            $query->where('offering_type', $request->offering_type);
        }
        
        $perPage = $request->input('per_page', 15);
        $businesses = $query->latest()->paginate($perPage);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Public business profiles retrieved successfully',
            'data' => $businesses
        ]);
    }

    /**
     * Get single approved business profile (public)
     */
    public function publicShow(string $id): JsonResponse
    {
        $business = BusinessProfile::where('id', $id)
                                   ->where('store_status', 'approved')
                                   ->with('user:id,firstname,lastname')
                                   ->firstOrFail();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Business profile retrieved successfully',
            'data' => $business
        ]);
    }

    /**
     * Search business profiles (public)
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'required|string|min:1|max:255',
            'category' => 'nullable|string|max:100',
            'category_id' => 'nullable|integer|exists:categories,id',
            'category_slug' => 'nullable|string|max:255',
            'offering_type' => 'nullable|in:providing_service,selling_product',
            'state' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'sort' => 'nullable|in:newest,oldest,name_asc,name_desc',
            'per_page' => 'nullable|integer|min:1|max:50'
        ]);

        $query = BusinessProfile::where('store_status', 'approved');
        
        // Search in business_name and business_description
        $searchTerm = $request->input('q');
        $query->where(function($q) use ($searchTerm) {
            $q->where('business_name', 'like', '%' . $searchTerm . '%')
              ->orWhere('business_description', 'like', '%' . $searchTerm . '%');
        });

        // Apply filters
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // New category system (recommended)
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('category_slug')) {
            $category = \App\Models\Category::where('slug', $request->category_slug)->first();
            if ($category) {
                $ids = $category->getSelfAndDescendantIds();
                $query->whereIn('category_id', $ids);
            }
        }

        if ($request->filled('offering_type')) {
            $query->where('offering_type', $request->offering_type);
        }

        if ($request->filled('state')) {
            $query->where('state', 'like', '%' . $request->state . '%');
        }

        if ($request->filled('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }

        // Sorting
        switch ($request->input('sort', 'newest')) {
            case 'oldest':
                $query->oldest();
                break;
            case 'name_asc':
                $query->orderBy('business_name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('business_name', 'desc');
                break;
            case 'newest':
            default:
                $query->latest();
                break;
        }

        $perPage = $request->input('per_page', 10);
        $businesses = $query->with('user:id,firstname,lastname')->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => $businesses
        ]);
    }

    /**
     * Get business profile filters metadata (public)
     */
    public function filters(): JsonResponse
    {
        $filters = [
            // Legacy categories (enum)
            'categories' => BusinessProfile::where('store_status', 'approved')
                                          ->distinct()
                                          ->pluck('category')
                                          ->filter()
                                          ->values(),

            // New category trees (recommended for client)
            'category_trees' => [
                'school' => \App\Models\Category::where('type', 'school')->whereNull('parent_id')->with('children.children.children')->orderBy('order')->get(),
                'art' => \App\Models\Category::where('type', 'art')->whereNull('parent_id')->with('children.children.children')->orderBy('order')->get(),
                // afro_beauty_services disabled for now
            'afro_beauty_services' => collect(),
            ],
            
            'offering_types' => BusinessProfile::where('store_status', 'approved')
                                              ->distinct()
                                              ->pluck('offering_type')
                                              ->filter()
                                              ->values(),
            
            'states' => BusinessProfile::where('store_status', 'approved')
                                      ->distinct()
                                      ->pluck('state')
                                      ->filter()
                                      ->values(),
            
            'cities' => BusinessProfile::where('store_status', 'approved')
                                      ->distinct()
                                      ->pluck('city')
                                      ->filter()
                                      ->values(),
            
            'sort_options' => [
                ['value' => 'newest', 'label' => 'Newest First'],
                ['value' => 'oldest', 'label' => 'Oldest First'],
                ['value' => 'name_asc', 'label' => 'Name: A to Z'],
                ['value' => 'name_desc', 'label' => 'Name: Z to A']
            ]
        ];
        
        return response()->json([
            'status' => 'success',
            'data' => $filters
        ]);
    }
}
