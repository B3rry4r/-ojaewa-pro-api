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
        $user = Auth::user();
        
        $orders = Order::with('orderItems.product')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return response()->json([
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
