<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogFavorite;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class BlogFavoriteController extends Controller
{
    /**
     * Get user's favorite blogs
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();
        
        $favorites = BlogFavorite::where('user_id', $user->id)
                                ->with([
                                    'blog' => function($query) {
                                        $query->published()->with('admin:id,firstname,lastname');
                                    }
                                ])
                                ->latest()
                                ->paginate(10);

        // Filter out any null blogs (in case blog was deleted)
        $favorites->getCollection()->transform(function ($favorite) {
            return $favorite->blog ? $favorite->blog : null;
        })->filter();

        return response()->json([
            'status' => 'success',
            'message' => 'Favorite blogs retrieved successfully',
            'data' => $favorites
        ]);
    }

    /**
     * Add blog to favorites
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'blog_id' => 'required|integer|exists:blogs,id'
        ]);

        $user = Auth::user();
        $blogId = $request->blog_id;

        // Check if blog exists and is published
        $blog = Blog::published()->findOrFail($blogId);

        // Check if already favorited
        $existingFavorite = BlogFavorite::where('user_id', $user->id)
                                      ->where('blog_id', $blogId)
                                      ->first();

        if ($existingFavorite) {
            return response()->json([
                'status' => 'error',
                'message' => 'Blog is already in your favorites'
            ], 400);
        }

        // Add to favorites
        BlogFavorite::create([
            'user_id' => $user->id,
            'blog_id' => $blogId
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Blog added to favorites successfully'
        ], 201);
    }

    /**
     * Remove blog from favorites
     */
    public function destroy(Request $request): JsonResponse
    {
        $request->validate([
            'blog_id' => 'required|integer|exists:blogs,id'
        ]);

        $user = Auth::user();
        $blogId = $request->blog_id;

        $favorite = BlogFavorite::where('user_id', $user->id)
                                ->where('blog_id', $blogId)
                                ->first();

        if (!$favorite) {
            return response()->json([
                'status' => 'error',
                'message' => 'Blog is not in your favorites'
            ], 404);
        }

        $favorite->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Blog removed from favorites successfully'
        ]);
    }
}