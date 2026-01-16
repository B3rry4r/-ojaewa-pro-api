<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SellerOrderController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * List orders that contain products owned by the authenticated seller.
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'status' => 'nullable|in:pending,processing,shipped,delivered,cancelled',
            'per_page' => 'nullable|integer|min:1|max:50',
        ]);

        $seller = Auth::user()?->sellerProfile;
        if (!$seller) {
            return response()->json([
                'status' => 'error',
                'message' => 'Seller profile not found'
            ], 404);
        }

        $orderIds = OrderItem::whereHas('product', function ($q) use ($seller) {
                $q->where('seller_profile_id', $seller->id);
            })
            ->pluck('order_id');

        $query = Order::with(['orderItems.product'])
            ->whereIn('id', $orderIds)
            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $perPage = $request->input('per_page', 10);
        $orders = $query->paginate($perPage);

        $data = $orders->getCollection()->map(function (Order $order) use ($seller) {
            $items = $order->orderItems->filter(function ($item) use ($seller) {
                return $item->product?->seller_profile_id === $seller->id;
            })->values()->map(function ($item) {
                return [
                    'product_id' => $item->product_id,
                    'product_name' => $item->product?->name,
                    'product_image' => $item->product?->image,
                    'quantity' => $item->quantity,
                    'size' => $item->product?->size,
                    'price' => $item->unit_price,
                ];
            });

            return [
                'id' => $order->id,
                'order_number' => 'ORD-' . str_pad((string)$order->id, 6, '0', STR_PAD_LEFT),
                'status' => $order->status,
                'created_at' => $order->created_at,
                'customer_name' => $order->shipping_name ?? $order->user?->firstname . ' ' . $order->user?->lastname,
                'total_price' => $order->total_price,
                'processing_days' => $order->orderItems->max('product.processing_days'),
                'items' => $items,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $data,
            'links' => [
                'first' => $orders->url(1),
                'last' => $orders->url($orders->lastPage()),
                'prev' => $orders->previousPageUrl(),
                'next' => $orders->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $orders->currentPage(),
                'from' => $orders->firstItem(),
                'last_page' => $orders->lastPage(),
                'path' => $orders->path(),
                'per_page' => $orders->perPage(),
                'to' => $orders->lastItem(),
                'total' => $orders->total(),
            ],
        ]);
    }

    /**
     * Show details for a specific seller order.
     */
    public function show(int $orderId): JsonResponse
    {
        $seller = Auth::user()?->sellerProfile;
        if (!$seller) {
            return response()->json([
                'status' => 'error',
                'message' => 'Seller profile not found'
            ], 404);
        }

        $order = Order::with(['orderItems.product', 'user'])->findOrFail($orderId);

        $sellerItems = $order->orderItems->filter(function ($item) use ($seller) {
            return $item->product?->seller_profile_id === $seller->id;
        })->values();

        if ($sellerItems->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Order not found for this seller'
            ], 404);
        }

        $items = $sellerItems->map(function ($item) {
            return [
                'product_id' => $item->product_id,
                'product_name' => $item->product?->name,
                'product_image' => $item->product?->image,
                'quantity' => $item->quantity,
                'size' => $item->product?->size,
                'price' => $item->unit_price,
                'processing_days' => $item->product?->processing_days,
            ];
        });

        $customer = [
            'name' => trim(($order->shipping_name ?: ($order->user?->firstname . ' ' . $order->user?->lastname)) ?? ''),
            'phone' => $order->shipping_phone,
            'address' => $order->shipping_address,
            'city' => $order->shipping_city,
            'state' => $order->shipping_state,
            'country' => $order->shipping_country,
        ];

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $order->id,
                'order_number' => 'ORD-' . str_pad((string)$order->id, 6, '0', STR_PAD_LEFT),
                'status' => $order->status,
                'created_at' => $order->created_at,
                'processing_days' => $items->max('processing_days'),
                'customer' => $customer,
                'items' => $items,
                'total_price' => $order->total_price,
                'tracking_number' => $order->tracking_number,
                'shipped_at' => $order->status === 'shipped' ? $order->updated_at : null,
                'delivered_at' => $order->delivered_at,
                'payment_status' => $order->status === 'paid' ? 'paid' : $order->status,
            ],
        ]);
    }

    /**
     * Update order status by seller.
     */
    public function updateStatus(Request $request, int $orderId): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:processing,shipped,delivered,cancelled',
            'tracking_number' => 'nullable|string|max:255',
            'reason' => 'nullable|string|max:500',
        ]);

        $seller = Auth::user()?->sellerProfile;
        if (!$seller) {
            return response()->json([
                'status' => 'error',
                'message' => 'Seller profile not found'
            ], 404);
        }

        $order = Order::with(['orderItems.product', 'user'])->findOrFail($orderId);

        $sellerHasItems = $order->orderItems->contains(function ($item) use ($seller) {
            return $item->product?->seller_profile_id === $seller->id;
        });

        if (!$sellerHasItems) {
            return response()->json([
                'status' => 'error',
                'message' => 'Order not found for this seller'
            ], 404);
        }

        $newStatus = $request->status;

        $order->status = $newStatus;
        $order->tracking_number = $request->tracking_number ?? $order->tracking_number;
        $order->cancellation_reason = $request->reason ?? $order->cancellation_reason;
        $order->delivered_at = $newStatus === 'delivered' ? now() : $order->delivered_at;
        $order->save();

        // Notify buyer
        $this->notificationService->sendEmailAndPush(
            $order->user,
            'Order Status Update - Oja Ewa',
            'order_status_updated',
            'Order Status Updated',
            "Your order #{$order->id} status has been updated to {$newStatus}.",
            ['order' => $order],
            ['order_id' => $order->id, 'status' => $newStatus, 'deep_link' => "/orders/{$order->id}"]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Order status updated',
            'data' => $order,
        ]);
    }
}
