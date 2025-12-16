<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Advert;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AdvertController extends Controller
{
    /**
     * Get all adverts
     */
    public function index(Request $request): JsonResponse
    {
        $query = Advert::with('admin:id,firstname,lastname');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by position
        if ($request->filled('position')) {
            $query->where('position', $request->position);
        }

        $adverts = $query->orderBy('priority', 'desc')
                        ->orderBy('created_at', 'desc')
                        ->paginate($request->input('per_page', 10));

        return response()->json([
            'status' => 'success',
            'message' => 'Adverts retrieved successfully',
            'data' => $adverts
        ]);
    }

    /**
     * Create a new advert
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image_url' => 'required|url',
            'action_url' => 'nullable|url',
            'position' => 'required|in:banner,sidebar,footer,popup',
            'status' => 'required|in:active,inactive,scheduled',
            'priority' => 'nullable|integer|min:0|max:100',
            'start_date' => 'nullable|date|after_or_equal:today',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        $admin = Auth::guard('admin')->user();

        $advert = Advert::create([
            'title' => $request->title,
            'description' => $request->description,
            'image_url' => $request->image_url,
            'action_url' => $request->action_url,
            'position' => $request->position,
            'status' => $request->status,
            'priority' => $request->priority ?? 0,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'created_by' => $admin->id,
        ]);

        $advert->load('admin:id,firstname,lastname');

        return response()->json([
            'status' => 'success',
            'message' => 'Advert created successfully',
            'data' => $advert
        ], 201);
    }

    /**
     * Update an advert
     */
    public function update(Request $request, Advert $advert): JsonResponse
    {
        $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'image_url' => 'sometimes|url',
            'action_url' => 'nullable|url',
            'position' => 'sometimes|in:banner,sidebar,footer,popup',
            'status' => 'sometimes|in:active,inactive,scheduled',
            'priority' => 'nullable|integer|min:0|max:100',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        $advert->update($request->validated());
        $advert->load('admin:id,firstname,lastname');

        return response()->json([
            'status' => 'success',
            'message' => 'Advert updated successfully',
            'data' => $advert
        ]);
    }

    /**
     * Delete an advert
     */
    public function destroy(Advert $advert): JsonResponse
    {
        $advert->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Advert deleted successfully'
        ]);
    }
}