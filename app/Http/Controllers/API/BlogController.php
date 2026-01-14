<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BlogController extends Controller
{
    /**
     * Get all published blogs.
     */
    public function index(Request $request): JsonResponse
    {
        $blogs = Blog::published()
            ->with('admin:id,firstname,lastname')
            ->latest('published_at')
            ->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $blogs,
        ]);
    }

    /**
     * Get a specific blog by slug.
     */
    public function show(string $slug): JsonResponse
    {
        try {
            $blog = Blog::where('slug', $slug)
                ->published()
                ->with('admin:id,firstname,lastname,email')
                ->first();

            if (!$blog) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Blog post not found',
                ], 404);
            }

            // Get related blog posts (same category or similar tags)
            $relatedPosts = Blog::published()
                ->where('id', '!=', $blog->id)
                ->where('category', $blog->category)
                ->with('admin:id,firstname,lastname')
                ->latest('published_at')
                ->take(3)
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $blog,
                'related_posts' => $relatedPosts
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve blog post',
                'error' => config('app.debug') ? $e->getMessage() : 'An error occurred'
            ], 500);
        }
    }

    /**
     * Get latest blog posts (for homepage/featured).
     */
    public function latest(): JsonResponse
    {
        $blogs = Blog::published()
            ->with('admin:id,firstname,lastname')
            ->latest('published_at')
            ->take(5)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $blogs,
        ]);
    }

    /**
     * Search blogs by title or content.
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|min:3',
        ]);

        $query = $request->query('query');
        
        $blogs = Blog::published()
            ->where(function ($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                  ->orWhere('body', 'LIKE', "%{$query}%");
            })
            ->with('admin:id,firstname,lastname')
            ->latest('published_at')
            ->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $blogs,
        ]);
    }
}
