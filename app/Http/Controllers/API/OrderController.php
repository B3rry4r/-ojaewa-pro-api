<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
    /**
     * Display a listing of orders for the authenticated user.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $user = Auth::user();
            
            $orders = Order::with(['orderItems.product.sellerProfile:id,business_name'])
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->paginate(10);
                
            return response()->json([
                'status' => 'success',
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
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve orders',
                'error' => config('app.debug') ? $e->getMessage() : 'An error occurred'
            ], 500);
        }
    }

    /**
     * Store a newly created order in storage.
     * 
     * @param \App\Http\Requests\StoreOrderRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        try {
            // Start a database transaction
            DB::beginTransaction();
            
            $user = Auth::user();
            $items = $request->validated()['items'];
            $totalPrice = 0;
            
            // Create the order
            $order = Order::create([
                'user_id' => $user->id,
                'total_price' => 0, // We'll update this after calculating item totals
                'status' => 'pending', // Default status
            ]);
            
            // Create order items and calculate total price
            foreach ($items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $quantity = $item['quantity'];
                $unitPrice = $product->price;
                $itemTotal = $quantity * $unitPrice;
                
                // Create the order item
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice
                ]);
                
                $totalPrice += $itemTotal;
            }
            
            // Update the order with the calculated total price
            $order->update(['total_price' => $totalPrice]);
            
            // Commit the transaction
            DB::commit();
            
            // Send order creation notifications
            $this->notificationService->sendEmailAndPush(
                $user,
                'Order Confirmation - Oja Ewa',
                'order_created',
                'Order Confirmed!',
                "Your order #{$order->id} has been confirmed and is being processed.",
                ['order' => $order->load('orderItems.product')],
                ['order_id' => $order->id, 'deep_link' => "/orders/{$order->id}"]
            );
            
            return response()->json([
                'message' => 'Order created successfully',
                'order' => $order->load('orderItems.product')
            ], 201);
            
        } catch (\Exception $e) {
            // Rollback the transaction if anything goes wrong
            DB::rollBack();
            
            return response()->json([
                'message' => 'Failed to create order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified order.
     * 
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $user = Auth::user();
        
        $order = Order::with(['orderItems.product', 'reviews'])
            ->where('user_id', $user->id)
            ->findOrFail($id);
        
        // No need to manually set avg_rating as it's already provided by the accessor
        
        return response()->json($order);
    }

    /**
     * Update order status (Admin only)
     * 
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'tracking_number' => 'nullable|string',
            'cancellation_reason' => 'nullable|string',
        ]);

        $order = Order::with(['user', 'orderItems.product'])->findOrFail($id);
        $oldStatus = $order->status;
        $newStatus = $request->status;

        // Update order status
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
            'message' => 'Order status updated successfully',
            'order' => $order
        ]);
    }

    /**
     * Get order tracking status
     * 
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function tracking(string $id): JsonResponse
    {
        $user = Auth::user();
        
        $order = Order::where('user_id', $user->id)->findOrFail($id);
        
        // Define tracking stages based on status
        $trackingStages = [
            'pending' => [
                'title' => 'Order Placed',
                'description' => 'Your order has been placed and is awaiting processing',
                'completed' => true,
                'date' => $order->created_at->format('M d, Y H:i')
            ],
            'processing' => [
                'title' => 'Processing',
                'description' => 'Your order is being prepared',
                'completed' => in_array($order->status, ['processing', 'shipped', 'delivered']),
                'date' => $order->status === 'processing' ? 'Current stage' : null
            ],
            'shipped' => [
                'title' => 'Shipped',
                'description' => $order->tracking_number ? "Tracking: {$order->tracking_number}" : 'Your order has been shipped',
                'completed' => in_array($order->status, ['shipped', 'delivered']),
                'date' => $order->status === 'shipped' ? 'Current stage' : null
            ],
            'delivered' => [
                'title' => 'Delivered',
                'description' => 'Your order has been delivered',
                'completed' => $order->status === 'delivered',
                'date' => $order->delivered_at ? $order->delivered_at->format('M d, Y H:i') : null
            ]
        ];
        
        // Handle cancelled orders
        if ($order->status === 'cancelled') {
            $trackingStages = [
                'pending' => $trackingStages['pending'],
                'cancelled' => [
                    'title' => 'Cancelled',
                    'description' => $order->cancellation_reason ?: 'Order was cancelled',
                    'completed' => true,
                    'date' => $order->updated_at->format('M d, Y H:i')
                ]
            ];
        }
        
        return response()->json([
            'status' => 'success',
            'data' => [
                'order_id' => $order->id,
                'current_status' => $order->status,
                'tracking_number' => $order->tracking_number,
                'stages' => array_values($trackingStages),
                'estimated_delivery' => $this->calculateEstimatedDelivery($order),
            ]
        ]);
    }

    /**
     * Calculate estimated delivery date
     */
    private function calculateEstimatedDelivery(Order $order): ?string
    {
        if ($order->status === 'delivered') {
            return null; // Already delivered
        }
        
        if ($order->status === 'cancelled') {
            return null; // Cancelled orders have no delivery
        }
        
        // Estimate 3-7 business days from created date
        $estimatedDays = 5; // Average delivery time
        $deliveryDate = $order->created_at->addDays($estimatedDays);
        
        return $deliveryDate->format('M d, Y');
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
            'cancelled' => 'status-danger',
            default => 'status-info'
        };
    }
}

    /**
     * Cancel order (user can cancel pending/processing orders)
     */
    public function cancel(Request $request, int $id): JsonResponse
    {
        $request->validate([
            "cancellation_reason" => "required|string|max:500"
        ]);
        
        $user = Auth::user();
        $order = Order::where("id", $id)
                     ->where("user_id", $user->id)
                     ->firstOrFail();
        
        // Business rules: can only cancel pending or processing orders
        if (!in_array($order->status, ["pending", "processing"])) {
            return response()->json([
                "status" => "error",
                "message" => "Cannot cancel order with status '{$order->status}'. Only pending or processing orders can be cancelled."
            ], 400);
        }
        
        // Update order
        $order->status = "cancelled";
        $order->cancellation_reason = $request->cancellation_reason;
        $order->save();
        
        // Send notification
        $this->notificationService->sendEmailAndPush(
            $user,
            "Order Cancelled - Oja Ewa",
            "order_cancelled",
            "Order Cancelled",
            "Your order #{$order->id} has been cancelled.",
            ["order" => $order],
            ["order_id" => $order->id, "deep_link" => "/orders/{$order->id}"]
        );
        
        return response()->json([
            "status" => "success",
            "message" => "Order cancelled successfully",
            "data" => $order
        ]);
    }
}
