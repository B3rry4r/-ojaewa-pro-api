<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\SustainabilityInitiative;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SustainabilityController extends Controller
{
    /**
     * Get all active sustainability initiatives (public)
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'category' => 'nullable|in:environmental,social,economic,governance',
            'per_page' => 'nullable|integer|min:1|max:50'
        ]);
        
        $query = SustainabilityInitiative::where('status', 'active')
                                        ->with('admin:id,firstname,lastname');
        
        // Filter by category if provided
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        
        $perPage = $request->input('per_page', 10);
        $initiatives = $query->latest()->paginate($perPage);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Sustainability initiatives retrieved successfully',
            'data' => $initiatives
        ]);
    }
    
    /**
     * Get single sustainability initiative (public)
     */
    public function show(int $id): JsonResponse
    {
        $initiative = SustainabilityInitiative::where('id', $id)
                                             ->where('status', 'active')
                                             ->with('admin:id,firstname,lastname')
                                             ->firstOrFail();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Sustainability initiative retrieved successfully',
            'data' => $initiative
        ]);
    }

    /**
     * Search sustainability initiatives (public)
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'required|string|min:1|max:255',
            'category' => 'nullable|in:environmental,social,economic,governance',
            'sort' => 'nullable|in:newest,oldest,target_asc,target_desc,progress',
            'per_page' => 'nullable|integer|min:1|max:50'
        ]);

        $query = SustainabilityInitiative::where('status', 'active');
        
        // Search in title and description
        $searchTerm = $request->input('q');
        $query->where(function($q) use ($searchTerm) {
            $q->where('title', 'like', '%' . $searchTerm . '%')
              ->orWhere('description', 'like', '%' . $searchTerm . '%');
        });

        // Apply filters
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Sorting
        switch ($request->input('sort', 'newest')) {
            case 'oldest':
                $query->oldest();
                break;
            case 'target_asc':
                $query->orderBy('target_amount', 'asc');
                break;
            case 'target_desc':
                $query->orderBy('target_amount', 'desc');
                break;
            case 'progress':
                $query->orderByRaw('(current_amount / target_amount) DESC');
                break;
            case 'newest':
            default:
                $query->latest();
                break;
        }

        $perPage = $request->input('per_page', 10);
        $initiatives = $query->with('admin:id,firstname,lastname')->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => $initiatives
        ]);
    }

    /**
     * Get sustainability filters metadata (public)
     */
    public function filters(): JsonResponse
    {
        $filters = [
            'categories' => SustainabilityInitiative::where('status', 'active')
                                                    ->distinct()
                                                    ->pluck('category')
                                                    ->filter()
                                                    ->values(),
            
            'target_range' => SustainabilityInitiative::where('status', 'active')
                                                      ->selectRaw('MIN(target_amount) as min, MAX(target_amount) as max')
                                                      ->first(),
            
            'sort_options' => [
                ['value' => 'newest', 'label' => 'Newest First'],
                ['value' => 'oldest', 'label' => 'Oldest First'],
                ['value' => 'target_asc', 'label' => 'Target: Low to High'],
                ['value' => 'target_desc', 'label' => 'Target: High to Low'],
                ['value' => 'progress', 'label' => 'Most Progress']
            ]
        ];
        
        return response()->json([
            'status' => 'success',
            'data' => $filters
        ]);
    }
}
