<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
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
}
