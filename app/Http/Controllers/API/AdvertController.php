<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Advert;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdvertController extends Controller
{
    /**
     * Get active adverts (public)
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'position' => 'nullable|in:banner,sidebar,footer,popup',
            'per_page' => 'nullable|integer|min:1|max:50'
        ]);
        
        $query = Advert::where('status', 'active')
                      ->where(function($q) {
                          $q->whereNull('start_date')
                            ->orWhere('start_date', '<=', Carbon::now());
                      })
                      ->where(function($q) {
                          $q->whereNull('end_date')
                            ->orWhere('end_date', '>=', Carbon::now());
                      });
        
        // Filter by position if provided
        if ($request->filled('position')) {
            $query->where('position', $request->position);
        }
        
        $perPage = $request->input('per_page', 10);
        $adverts = $query->orderBy('priority', 'desc')->paginate($perPage);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Adverts retrieved successfully',
            'data' => $adverts
        ]);
    }
    
    /**
     * Get single advert (public)
     */
    public function show(int $id): JsonResponse
    {
        $advert = Advert::where('id', $id)
                       ->where('status', 'active')
                       ->firstOrFail();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Advert retrieved successfully',
            'data' => $advert
        ]);
    }
}
