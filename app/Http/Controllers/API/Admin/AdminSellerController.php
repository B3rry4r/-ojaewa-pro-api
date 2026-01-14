<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\SellerProfile;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdminSellerController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * List all seller profiles paginated
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = SellerProfile::with("user");

        // Filter by status if provided
        if ($request->has("status")) {
            $query->where("registration_status", $request->status);
        }

        // Sort by created_at by default
        $sellers = $query->orderBy("created_at", "desc")->paginate(15);

        return response()->json([
            "status" => "success",
            "message" => "Seller profiles retrieved successfully",
            "data" => [
                "data" => $sellers->items(),
                "links" => [
                    "first" => $sellers->url(1),
                    "last" => $sellers->url($sellers->lastPage()),
                    "prev" => $sellers->previousPageUrl(),
                    "next" => $sellers->nextPageUrl(),
                ],
                "meta" => [
                    "current_page" => $sellers->currentPage(),
                    "from" => $sellers->firstItem(),
                    "last_page" => $sellers->lastPage(),
                    "path" => $sellers->path(),
                    "per_page" => $sellers->perPage(),
                    "to" => $sellers->lastItem(),
                    "total" => $sellers->total(),
                ],
            ],
        ]);
    }

    /**
     * Get list of pending seller profiles
     *
     * @return JsonResponse
     */
    public function pendingSellers(): JsonResponse
    {
        $pendingSellers = SellerProfile::with("user")
            ->where("registration_status", "pending")
            ->orderBy("created_at", "asc")
            ->paginate(15);

        return response()->json([
            "status" => "success",
            "message" => "Pending seller profiles retrieved successfully",
            "data" => [
                "data" => $pendingSellers->items(),
                "links" => [
                    "first" => $pendingSellers->url(1),
                    "last" => $pendingSellers->url($pendingSellers->lastPage()),
                    "prev" => $pendingSellers->previousPageUrl(),
                    "next" => $pendingSellers->nextPageUrl(),
                ],
                "meta" => [
                    "current_page" => $pendingSellers->currentPage(),
                    "from" => $pendingSellers->firstItem(),
                    "last_page" => $pendingSellers->lastPage(),
                    "path" => $pendingSellers->path(),
                    "per_page" => $pendingSellers->perPage(),
                    "to" => $pendingSellers->lastItem(),
                    "total" => $pendingSellers->total(),
                ],
            ],
        ]);
    }

    /**
     * Approve or reject a seller profile
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function approveSeller(Request $request, $id): JsonResponse
    {
        $request->validate([
            "status" => "required|in:approved,rejected",
            "rejection_reason" => "required_if:status,rejected|string|nullable",
        ]);

        $seller = SellerProfile::with('user')->findOrFail($id);
        $oldStatus = $seller->registration_status;
        $newStatus = $request->status;
        
        $seller->registration_status = $newStatus;

        if (
            $newStatus === "rejected" &&
            $request->has("rejection_reason")
        ) {
            $seller->rejection_reason = $request->rejection_reason;
        }

        $seller->save();

        // Send notification if status changed
        if ($oldStatus !== $newStatus && $seller->user) {
            $this->notificationService->sendEmailAndPush(
                $seller->user,
                'Seller Profile Update - Oja Ewa',
                'seller_approved',
                $newStatus === 'approved' ? 'Seller Profile Approved!' : 'Seller Profile Needs Update',
                $newStatus === 'approved' 
                    ? "Congratulations! Your seller profile '{$seller->business_name}' has been approved. You can now start listing products!"
                    : "Your seller profile '{$seller->business_name}' needs some updates before approval.",
                [
                    'seller' => $seller,
                    'status' => $newStatus,
                    'rejectionReason' => $seller->rejection_reason
                ],
                [
                    'seller_id' => $seller->id,
                    'status' => $newStatus,
                    'deep_link' => "/seller/profile"
                ]
            );
        }

        return response()->json([
            "status" => "success",
            "message" => "Seller profile {$request->status} successfully",
            "data" => $seller,
        ]);
    }

    /**
     * Update seller status (active/inactive)
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    /**
     * Get single seller details
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $seller = SellerProfile::with([
            "user",
            "products" => function ($query) {
                $query->select(
                    "id",
                    "seller_profile_id",
                    "name",
                    "price",
                    "status",
                    "created_at",
                );
            },
        ])->findOrFail($id);

        return response()->json([
            "status" => "success",
            "message" => "Seller details retrieved successfully",
            "data" => [
                "seller" => [
                    "id" => $seller->id,
                    "user" => [
                        "firstname" => $seller->user->firstname,
                        "lastname" => $seller->user->lastname,
                        "email" => $seller->user->email,
                        "phone" => $seller->user->phone,
                    ],
                    "business_name" => $seller->business_name,
                    "business_email" => $seller->business_email,
                    "business_phone_number" => $seller->business_phone_number,
                    "country" => $seller->country,
                    "state" => $seller->state,
                    "city" => $seller->city,
                    "address" => $seller->address,
                    "instagram" => $seller->instagram,
                    "facebook" => $seller->facebook,
                    "business_registration_number" =>
                        $seller->business_registration_number,
                    "bank_name" => $seller->bank_name,
                    "account_number" => $seller->account_number,
                    "registration_status" => $seller->registration_status,
                    "documents" => [
                        "identity_document" => $seller->identity_document,
                        "business_certificate" => $seller->business_certificate,
                        "business_logo" => $seller->business_logo,
                    ],
                    "created_at" => $seller->created_at,
                    "products_count" => $seller->products->count(),
                    "products" => $seller->products,
                ],
            ],
        ]);
    }

    /**
     * Update seller status (active/inactive)
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function updateStatus(Request $request, $id): JsonResponse
    {
        $request->validate([
            "active" => "required|boolean",
        ]);

        $seller = SellerProfile::findOrFail($id);
        $seller->active = $request->active;
        $seller->save();

        $status = $request->active ? "activated" : "deactivated";

        return response()->json([
            "status" => "success",
            "message" => "Seller profile {$status} successfully",
            "data" => $seller,
        ]);
    }
}
