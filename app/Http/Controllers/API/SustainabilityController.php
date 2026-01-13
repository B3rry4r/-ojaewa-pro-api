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
}
