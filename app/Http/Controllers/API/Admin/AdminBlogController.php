<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AdminBlogController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display a listing of blogs.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Blog::with('admin:id,firstname,lastname');

        // Filter by published status
        if ($request->has('published')) {
            if ($request->published === 'true') {
                $query->whereNotNull('published_at');
            } else {
                $query->whereNull('published_at');
            }
        }

        $blogs = $query->latest('created_at')->paginate(15);

        return response()->json([
            'status' => 'success',
            'data' => $blogs,
        ]);
    }

    /**
     * Store a newly created blog.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'featured_image' => 'nullable|url',
            'published' => 'boolean',
        ]);

        $admin = Auth::user();
        
        $blog = Blog::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'body' => $request->body,
            'featured_image' => $request->featured_image,
            'admin_id' => $admin->id,
            'published_at' => $request->published ? now() : null,
        ]);

        // Send notification if blog is published
        if ($request->published) {
            $this->notificationService->sendPushToAllUsers(
                'New Blog Post',
                "Check out our latest blog post: {$blog->title}",
                [
                    'blog_id' => $blog->id,
                    'blog_slug' => $blog->slug,
                    'deep_link' => "/blogs/{$blog->slug}"
                ]
            );
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Blog created successfully',
            'data' => $blog->load('admin:id,firstname,lastname'),
        ], 201);
    }

    /**
     * Display the specified blog.
     */
    public function show(string $id): JsonResponse
    {
        $blog = Blog::with('admin:id,firstname,lastname')->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $blog,
        ]);
    }

    /**
     * Update the specified blog.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'featured_image' => 'nullable|url',
            'published' => 'boolean',
        ]);

        $blog = Blog::findOrFail($id);
        $wasPublished = !is_null($blog->published_at);
        $willBePublished = $request->published;

        $blog->update([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'body' => $request->body,
            'featured_image' => $request->featured_image,
            'published_at' => $willBePublished ? ($wasPublished ? $blog->published_at : now()) : null,
        ]);

        // Send notification if blog is newly published
        if (!$wasPublished && $willBePublished) {
            $this->notificationService->sendPushToAllUsers(
                'New Blog Post',
                "Check out our latest blog post: {$blog->title}",
                [
                    'blog_id' => $blog->id,
                    'blog_slug' => $blog->slug,
                    'deep_link' => "/blogs/{$blog->slug}"
                ]
            );
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Blog updated successfully',
            'data' => $blog->load('admin:id,firstname,lastname'),
        ]);
    }

    /**
     * Remove the specified blog.
     */
    public function destroy(string $id): JsonResponse
    {
        $blog = Blog::findOrFail($id);
        $blog->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Blog deleted successfully',
        ]);
    }

    /**
     * Toggle blog publication status.
     */
    public function togglePublish(string $id): JsonResponse
    {
        $blog = Blog::findOrFail($id);
        $wasPublished = !is_null($blog->published_at);

        $blog->update([
            'published_at' => $wasPublished ? null : now(),
        ]);

        // Send notification if blog is newly published
        if (!$wasPublished) {
            $this->notificationService->sendPushToAllUsers(
                'New Blog Post',
                "Check out our latest blog post: {$blog->title}",
                [
                    'blog_id' => $blog->id,
                    'blog_slug' => $blog->slug,
                    'deep_link' => "/blogs/{$blog->slug}"
                ]
            );
        }

        return response()->json([
            'status' => 'success',
            'message' => $wasPublished ? 'Blog unpublished successfully' : 'Blog published successfully',
            'data' => $blog->load('admin:id,firstname,lastname'),
        ]);
    }
}
