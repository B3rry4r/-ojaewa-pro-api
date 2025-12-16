<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolRegistration;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SchoolRegistrationController extends Controller
{
    /**
     * Get all school registrations
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 10);
        $status = $request->input('status');

        $query = SchoolRegistration::query();

        if ($status) {
            $query->where('status', $status);
        }

        $registrations = $query->orderBy('submitted_at', 'desc')
                              ->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'message' => 'School registrations retrieved successfully',
            'data' => $registrations
        ]);
    }

    /**
     * Update school registration status
     */
    public function update(Request $request, SchoolRegistration $schoolRegistration): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:pending,processing,approved,rejected'
        ]);

        $schoolRegistration->update([
            'status' => $request->status
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'School registration status updated successfully',
            'data' => $schoolRegistration
        ]);
    }

    /**
     * Get school registration details
     */
    public function show(SchoolRegistration $schoolRegistration): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => 'School registration retrieved successfully',
            'data' => $schoolRegistration
        ]);
    }

    /**
     * Delete school registration
     */
    public function destroy(SchoolRegistration $schoolRegistration): JsonResponse
    {
        $schoolRegistration->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'School registration deleted successfully'
        ]);
    }
}