<?php

namespace Tests\Feature\Admin;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminOrderTest extends AdminTestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_all_orders()
    {
        // Set up admin authentication
        $this->setUpAdmin();

        // Create test data
        $user = User::factory()->create();
        $sellerProfile = SellerProfile::factory()->create();
        $product = Product::factory()->create([
            'seller_profile_id' => $sellerProfile->id
        ]);
        
        // Create 3 orders
        $orders = Order::factory(3)->create([
            'user_id' => $user->id
        ]);
        
        // Add order items to each order
        foreach ($orders as $order) {
            OrderItem::factory()->create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => 2,
                'unit_price' => $product->price
            ]);
        }

        // Make request to list all orders
        $response = $this->getJson('/api/admin/orders');

        // Assert successful response
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'data', // Paginated data
                    'links',
                    'meta'
                ]
            ])
            ->assertJson([
                'status' => 'success',
                'message' => 'Orders retrieved successfully',
            ]);
            
        // Verify the number of orders returned
        $this->assertCount(count($orders), $response->json('data.data'));
    }
    
    public function test_admin_can_view_order_details()
    {
        // Set up admin authentication
        $this->setUpAdmin();

        // Create test data
        $user = User::factory()->create();
        $sellerProfile = SellerProfile::factory()->create();
        $product = Product::factory()->create([
            'seller_profile_id' => $sellerProfile->id
        ]);
        
        // Create an order with items
        $order = Order::factory()->create([
            'user_id' => $user->id
        ]);
        
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price' => $product->price
        ]);

        // Make request to view order details
        $response = $this->getJson("/api/admin/order/{$order->id}");

        // Assert successful response
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id',
                    'user_id',
                    'total_price',
                    'status',
                    'created_at',
                    'updated_at',
                    'orderItems',
                    'user'
                ]
            ])
            ->assertJson([
                'status' => 'success',
                'message' => 'Order retrieved successfully',
                'data' => [
                    'id' => $order->id,
                    'user_id' => $user->id,
                    'status' => $order->status
                ]
            ]);
    }
    
    public function test_admin_can_update_order_status()
    {
        // Set up admin authentication
        $this->setUpAdmin();

        // Create test data
        $user = User::factory()->create();
        
        // Create an order
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending'
        ]);

        // Make request to update order status
        $response = $this->patchJson("/api/admin/order/{$order->id}/status", [
            'status' => 'paid'
        ]);

        // Assert successful response
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data'
            ])
            ->assertJson([
                'status' => 'success',
                'message' => 'Order status updated to paid successfully',
                'data' => [
                    'id' => $order->id,
                    'status' => 'paid'
                ]
            ]);
            
        // Verify the order status is actually updated in the database
        $this->assertEquals(
            'paid',
            Order::find($order->id)->status
        );
    }
    
    public function test_admin_cannot_update_order_with_invalid_status()
    {
        // Set up admin authentication
        $this->setUpAdmin();

        // Create test data
        $user = User::factory()->create();
        
        // Create an order
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending'
        ]);

        // Make request with invalid status
        $response = $this->patchJson("/api/admin/order/{$order->id}/status", [
            'status' => 'invalid_status'
        ]);

        // Assert validation error
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
            
        // Verify the order status is not changed in the database
        $this->assertEquals(
            'pending',
            Order::find($order->id)->status
        );
    }
}
