<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdminOrderController extends Controller
{
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
        ]);
        
        $order = Order::findOrFail($id);
        $order->status = $request->status;
        $order->save();
        
        return response()->json([
            'status' => 'success',
            'message' => "Order status updated to {$request->status} successfully",
            'data' => $order
        ]);
    }
}
