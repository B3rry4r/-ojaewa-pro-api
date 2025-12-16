<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\SustainabilityInitiative;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class SustainabilityController extends Controller
{
    /**
     * Get all sustainability initiatives
     */
    public function index(Request $request): JsonResponse
    {
        $query = SustainabilityInitiative::with('admin:id,firstname,lastname');

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $initiatives = $query->orderBy('created_at', 'desc')
                            ->paginate($request->input('per_page', 10));

        return response()->json([
            'status' => 'success',
            'message' => 'Sustainability initiatives retrieved successfully',
            'data' => $initiatives
        ]);
    }

    /**
     * Create a new sustainability initiative
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image_url' => 'nullable|url',
            'category' => 'required|in:environmental,social,economic,governance',
            'status' => 'required|in:active,completed,planned,cancelled',
            'target_amount' => 'nullable|numeric|min:0',
            'current_amount' => 'nullable|numeric|min:0',
            'impact_metrics' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'partners' => 'nullable|array',
            'participant_count' => 'nullable|integer|min:0',
            'progress_notes' => 'nullable|string',
        ]);

        $admin = Auth::guard('admin')->user();

        $initiative = SustainabilityInitiative::create([
            'title' => $request->title,
            'description' => $request->description,
            'image_url' => $request->image_url,
            'category' => $request->category,
            'status' => $request->status,
            'target_amount' => $request->target_amount,
            'current_amount' => $request->current_amount ?? 0,
            'impact_metrics' => $request->impact_metrics,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'partners' => $request->partners,
            'participant_count' => $request->participant_count ?? 0,
            'progress_notes' => $request->progress_notes,
            'created_by' => $admin->id,
        ]);

        $initiative->load('admin:id,firstname,lastname');

        return response()->json([
            'status' => 'success',
            'message' => 'Sustainability initiative created successfully',
            'data' => $initiative
        ], 201);
    }

    /**
     * Update a sustainability initiative
     */
    public function update(Request $request, SustainabilityInitiative $sustainabilityInitiative): JsonResponse
    {
        $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'image_url' => 'nullable|url',
            'category' => 'sometimes|in:environmental,social,economic,governance',
            'status' => 'sometimes|in:active,completed,planned,cancelled',
            'target_amount' => 'nullable|numeric|min:0',
            'current_amount' => 'nullable|numeric|min:0',
            'impact_metrics' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'partners' => 'nullable|array',
            'participant_count' => 'nullable|integer|min:0',
            'progress_notes' => 'nullable|string',
        ]);

        $sustainabilityInitiative->update($request->validated());
        $sustainabilityInitiative->load('admin:id,firstname,lastname');

        return response()->json([
            'status' => 'success',
            'message' => 'Sustainability initiative updated successfully',
            'data' => $sustainabilityInitiative
        ]);
    }

    /**
     * Delete a sustainability initiative
     */
    public function destroy(SustainabilityInitiative $sustainabilityInitiative): JsonResponse
    {
        $sustainabilityInitiative->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Sustainability initiative deleted successfully'
        ]);
    }
}