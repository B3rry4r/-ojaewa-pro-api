<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewRequest;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Store a newly created review in storage.
     *
     * @param  \App\Http\Requests\StoreReviewRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreReviewRequest $request): JsonResponse
    {
        // Get the validated data
        $validated = $request->validated();
        
        // Create the review with the authenticated user's ID
        $review = new Review($validated);
        $review->user_id = Auth::id();
        $review->save();
        
        return response()->json([
            'message' => 'Review created successfully',
            'review' => $review
        ], 201);
    }
    
    /**
     * List all reviews for a specific reviewable entity.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $type
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function byEntity(Request $request, string $type, string $id): JsonResponse
    {
        // Determine the model type from the route parameter
        $modelType = null;
        if ($type === 'product') {
            $modelType = Product::class;
        } elseif ($type === 'order') {
            $modelType = Order::class;
        } else {
            return response()->json([
                'message' => 'Invalid entity type'
            ], 400);
        }
        
        // Check if the entity exists
        $model = null;
        if ($type === 'product') {
            $model = Product::find($id);
        } elseif ($type === 'order') {
            $model = Order::find($id);
            
            // Ensure the user can only see their own order reviews
            if ($model && $model->user_id !== Auth::id()) {
                return response()->json([
                    'message' => 'Unauthorized to view these reviews'
                ], 403);
            }
        }
        
        if (!$model) {
            return response()->json([
                'message' => 'Entity not found'
            ], 404);
        }
        
        // Get the reviews for this entity, with the reviewer information
        $reviews = Review::with('user')
            ->where('reviewable_type', $modelType)
            ->where('reviewable_id', $id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        // Add the average rating to the response using the avg_rating accessor
        $avgRating = $model->avg_rating;
        
        return response()->json([
            'entity' => [
                'id' => $model->id,
                'type' => $type,
                'avg_rating' => $avgRating
            ],
            'reviews' => $reviews
        ]);
    }
}
