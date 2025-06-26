<?php

namespace Tests\Feature\API;

use App\Models\Order;
use App\Models\Product;
use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    
    protected User $user;
    protected SellerProfile $sellerProfile;
    protected string $token;
    protected $products;

    /**
     * Set up the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a user
        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('test-token')->plainTextToken;
        
        // Create a seller profile
        $this->sellerProfile = SellerProfile::factory()->create([
            'registration_status' => 'approved',
        ]);
        
        // Create products for testing
        $this->products = Product::factory()->count(3)->create([
            'seller_profile_id' => $this->sellerProfile->id,
            'status' => 'approved',
            'price' => 5000,
        ]);
    }

    /**
     * Test creating an order.
     */
    public function test_can_create_order(): void
    {
        $orderData = [
            'items' => [
                [
                    'product_id' => $this->products[0]->id,
                    'quantity' => 2
                ],
                [
                    'product_id' => $this->products[1]->id,
                    'quantity' => 1
                ]
            ]
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ])->postJson('/api/orders', $orderData);

        $response->assertStatus(201)
                 ->assertJsonPath('message', 'Order created successfully')
                 ->assertJsonStructure([
                     'message',
                     'order' => [
                         'id',
                         'user_id',
                         'total_price',
                         'status',
                         'created_at',
                         'updated_at',
                         'order_items' => [
                             '*' => [
                                 'id',
                                 'order_id',
                                 'product_id',
                                 'quantity',
                                 'unit_price',
                                 'product' => [
                                     'id',
                                     'name',
                                 ]
                             ]
                         ]
                     ]
                 ]);

        // Calculate expected total
        $expectedTotal = ($this->products[0]->price * 2) + ($this->products[1]->price * 1);
        
        // Check order created in database
        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->id,
            'total_price' => $expectedTotal,
            'status' => 'pending'
        ]);
        
        // Check order items created in database
        $order = Order::latest()->first();
        $this->assertEquals(2, $order->orderItems()->count());
    }

    /**
     * Test listing all orders for the authenticated user.
     */
    public function test_can_list_orders(): void
    {
        // Create some orders for the user
        $orders = Order::factory()
            ->count(3)
            ->create([
                'user_id' => $this->user->id
            ]);
            
        // Add order items
        foreach ($orders as $order) {
            foreach ($this->products as $index => $product) {
                $order->orderItems()->create([
                    'product_id' => $product->id,
                    'quantity' => $index + 1,
                    'unit_price' => $product->price
                ]);
            }
        }

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ])->getJson('/api/orders');

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data')
                 ->assertJsonStructure([
                     'data' => [
                         '*' => [
                             'id',
                             'user_id',
                             'total_price',
                             'status',
                             'created_at',
                             'updated_at',
                             'order_items' => [
                                 '*' => [
                                     'id',
                                     'product' => [
                                         'id',
                                         'name'
                                     ]
                                 ]
                             ]
                         ]
                     ],
                     'links',
                     'meta'
                 ]);
    }

    /**
     * Test viewing a single order.
     */
    public function test_can_view_order(): void
    {
        // Create an order
        $order = Order::factory()->create([
            'user_id' => $this->user->id
        ]);
        
        // Add some order items
        foreach ($this->products as $index => $product) {
            $order->orderItems()->create([
                'product_id' => $product->id,
                'quantity' => $index + 1,
                'unit_price' => $product->price
            ]);
        }

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ])->getJson('/api/orders/' . $order->id);

        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $order->id,
                     'user_id' => $this->user->id,
                     'status' => $order->status
                 ])
                 ->assertJsonStructure([
                     'id',
                     'user_id',
                     'total_price',
                     'status',
                     'created_at',
                     'updated_at',
                     'order_items',
                     'reviews',
                     'avg_rating'
                 ]);
    }

    /**
     * Test that a user cannot view another user's order.
     */
    public function test_cannot_view_another_users_order(): void
    {
        // Create another user
        $anotherUser = User::factory()->create();
        
        // Create an order for the other user
        $otherOrder = Order::factory()->create([
            'user_id' => $anotherUser->id
        ]);
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ])->getJson('/api/orders/' . $otherOrder->id);

        // Should get a 404 Not Found since we filter by user_id in the query
        $response->assertStatus(404);
    }
}
