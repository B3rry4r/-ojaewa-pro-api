<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdminProductController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
    /**
     * List all products paginated
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = Product::with(["sellerProfile.user"]);

        // Filter by status if provided
        if ($request->has("status")) {
            $query->where("status", $request->status);
        }

        // Sort by created_at by default
        $products = $query->orderBy("created_at", "desc")->paginate(15);

        return response()->json([
            "status" => "success",
            "message" => "Products retrieved successfully",
            "data" => [
                "data" => $products->items(),
                "links" => [
                    "first" => $products->url(1),
                    "last" => $products->url($products->lastPage()),
                    "prev" => $products->previousPageUrl(),
                    "next" => $products->nextPageUrl(),
                ],
                "meta" => [
                    "current_page" => $products->currentPage(),
                    "from" => $products->firstItem(),
                    "last_page" => $products->lastPage(),
                    "path" => $products->path(),
                    "per_page" => $products->perPage(),
                    "to" => $products->lastItem(),
                    "total" => $products->total(),
                ],
            ],
        ]);
    }

    /**
     * Get list of pending products
     *
     * @return JsonResponse
     */
    public function pendingProducts(): JsonResponse
    {
        $pendingProducts = Product::with(["sellerProfile.user"])
            ->where("status", "pending")
            ->orderBy("created_at", "asc")
            ->paginate(15);

        return response()->json([
            "status" => "success",
            "message" => "Pending products retrieved successfully",
            "data" => [
                "data" => $pendingProducts->items(),
                "links" => [
                    "first" => $pendingProducts->url(1),
                    "last" => $pendingProducts->url(
                        $pendingProducts->lastPage(),
                    ),
                    "prev" => $pendingProducts->previousPageUrl(),
                    "next" => $pendingProducts->nextPageUrl(),
                ],
                "meta" => [
                    "current_page" => $pendingProducts->currentPage(),
                    "from" => $pendingProducts->firstItem(),
                    "last_page" => $pendingProducts->lastPage(),
                    "path" => $pendingProducts->path(),
                    "per_page" => $pendingProducts->perPage(),
                    "to" => $pendingProducts->lastItem(),
                    "total" => $pendingProducts->total(),
                ],
            ],
        ]);
    }

    /**
     * Approve or reject a product
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function approveProduct(Request $request, $id): JsonResponse
    {
        $request->validate([
            "status" => "required|in:approved,rejected",
            "rejection_reason" => "required_if:status,rejected|string|nullable",
        ]);

        $product = Product::with('sellerProfile.user')->findOrFail($id);
        $oldStatus = $product->status;
        $newStatus = $request->status;
        
        $product->status = $newStatus;

        if (
            $newStatus === "rejected" &&
            $request->has("rejection_reason")
        ) {
            $product->rejection_reason = $request->rejection_reason;
        }

        $product->save();

        // Send notification if status changed
        if ($oldStatus !== $newStatus && $product->sellerProfile && $product->sellerProfile->user) {
            $user = $product->sellerProfile->user;
            
            $this->notificationService->sendEmailAndPush(
                $user,
                'Product Update - Oja Ewa',
                'product_approved',
                $newStatus === 'approved' ? 'Product Approved!' : 'Product Needs Update',
                $newStatus === 'approved' 
                    ? "Great news! Your product '{$product->name}' has been approved and is now live!"
                    : "Your product '{$product->name}' needs some updates before approval.",
                [
                    'product' => $product,
                    'status' => $newStatus,
                    'rejectionReason' => $product->rejection_reason
                ],
                [
                    'product_id' => $product->id,
                    'status' => $newStatus,
                    'deep_link' => "/products/{$product->id}"
                ]
            );
        }

        return response()->json([
            "status" => "success",
            "message" => "Product {$request->status} successfully",
            "data" => $product,
        ]);
    }

    /**
     * Get single product details
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $product = Product::with([
            "sellerProfile.user",
            "reviews.user",
        ])->findOrFail($id);

        return response()->json([
            "status" => "success",
            "message" => "Product details retrieved successfully",
            "data" => [
                "product" => [
                    "id" => $product->id,
                    "name" => $product->name,
                    "description" => $product->description,
                    "gender" => $product->gender,
                    "style" => $product->style,
                    "tribe" => $product->tribe,
                    "fabric_type" => $product->fabric_type,
                    "price" => $product->price,
                    "image" => $product->image,
                    "size" => $product->size,
                    "processing_time_type" => $product->processing_time_type,
                    "processing_days" => $product->processing_days,
                    "status" => $product->status,
                    "rejection_reason" => $product->rejection_reason,
                    "avg_rating" => $product->avg_rating,
                    "total_reviews" => $product->reviews->count(),
                    "created_at" => $product->created_at,
                ],
                "seller" => [
                    "business_name" => $product->sellerProfile->business_name,
                    "email" => $product->sellerProfile->user->email,
                    "phone" => $product->sellerProfile->business_phone_number,
                ],
            ],
        ]);
    }

    /**
     * Update product status (active/inactive)
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function updateStatus(Request $request, $id): JsonResponse
    {
        $request->validate([
            "status" => "required|in:pending,approved,rejected",
        ]);

        $product = Product::findOrFail($id);
        $product->status = $request->status;
        $product->save();

        return response()->json([
            "status" => "success",
            "message" => "Product status updated to {$request->status} successfully",
            "data" => $product,
        ]);
    }
}
