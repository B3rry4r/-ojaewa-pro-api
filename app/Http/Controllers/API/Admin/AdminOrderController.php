<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdminOrderController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
    /**
     * List all orders paginated with optional filtering
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = Order::with(['user', 'orderItems.product']);
        
        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        // Sort by created_at by default (newest first)
        $orders = $query->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Orders retrieved successfully',
            'data' => [
                'data' => $orders->items(),
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
            ]
        ]);
    }
    
    /**
     * Show details for a specific order
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $order = Order::with([
            'user', 
            'orderItems.product.sellerProfile.user',
            'reviews'
        ])->findOrFail($id);
        
        // Make sure orderItems are explicitly included in the response
        $orderData = $order->toArray();
        if (!isset($orderData['orderItems']) && $order->orderItems) {
            $orderData['orderItems'] = $order->orderItems->toArray();
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Order retrieved successfully',
            'data' => $orderData
        ]);
    }
    
    /**
     * Update order status
     * 
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function updateStatus(Request $request, $id): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,canceled,paid',
            'tracking_number' => 'nullable|string',
            'cancellation_reason' => 'nullable|string',
        ]);
        
        $order = Order::with(['user', 'orderItems.product'])->findOrFail($id);
        $oldStatus = $order->status;
        $newStatus = $request->status;
        
        $order->update([
            'status' => $newStatus,
            'tracking_number' => $request->tracking_number,
            'cancellation_reason' => $request->cancellation_reason,
            'delivered_at' => $newStatus === 'delivered' ? now() : null,
        ]);
        
        // Send status update notifications if status changed
        if ($oldStatus !== $newStatus) {
            $statusClass = $this->getStatusClass($newStatus);
            
            $this->notificationService->sendEmailAndPush(
                $order->user,
                "Order Status Update - Oja Ewa",
                'order_status_updated',
                'Order Status Updated',
                "Your order #{$order->id} status has been updated to {$newStatus}.",
                ['order' => $order, 'statusClass' => $statusClass],
                ['order_id' => $order->id, 'status' => $newStatus, 'deep_link' => "/orders/{$order->id}"]
            );
        }
        
        return response()->json([
            'status' => 'success',
            'message' => "Order status updated to {$request->status} successfully",
            'data' => $order
        ]);
    }
    
    /**
     * Get CSS class for order status
     */
    private function getStatusClass(string $status): string
    {
        return match($status) {
            'pending' => 'status-warning',
            'processing' => 'status-info',
            'shipped' => 'status-info',
            'delivered' => 'status-success',
            'canceled' => 'status-danger',
            'paid' => 'status-success',
            default => 'status-info'
        };
    }
}
