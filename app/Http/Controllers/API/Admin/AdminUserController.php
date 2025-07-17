<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdminUserController extends Controller
{
    /**
     * List all users with pagination
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::query();
        
        // Search by firstname, lastname, email, or phone if provided
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('firstname', 'like', "%{$searchTerm}%")
                  ->orWhere('lastname', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%")
                  ->orWhere('phone', 'like', "%{$searchTerm}%");
            });
        }
        
        // Get users with related data
        $users = $query->withCount(['sellerProfile', 'businessProfiles', 'orders'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        // Add computed name attribute to each user
        $items = $users->through(function ($user) {
            $user->setAttribute('name', $user->name);
            return $user;
        });
        
        return response()->json([
            'status' => 'success',
            'message' => 'Users retrieved successfully',
            'data' => [
                'data' => $users->items(),
                'links' => [
                    'first' => $users->url(1),
                    'last' => $users->url($users->lastPage()),
                    'prev' => $users->previousPageUrl(),
                    'next' => $users->nextPageUrl(),
                ],
                'meta' => [
                    'current_page' => $users->currentPage(),
                    'from' => $users->firstItem(),
                    'last_page' => $users->lastPage(),
                    'path' => $users->path(),
                    'per_page' => $users->perPage(),
                    'to' => $users->lastItem(),
                    'total' => $users->total(),
                ],
            ]
        ]);
    }
}
