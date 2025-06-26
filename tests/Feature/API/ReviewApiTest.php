<?php

namespace Tests\Feature\API;

use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ReviewApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    
    protected User $user;
    protected string $token;
    protected Product $product;
    protected Order $order;

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
        $sellerProfile = SellerProfile::factory()->create([
            'registration_status' => 'approved',
        ]);
        
        // Create a product
        $this->product = Product::factory()->create([
            'seller_profile_id' => $sellerProfile->id,
            'status' => 'approved',
        ]);
        
        // Create an order for the user
        $this->order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'paid',  // Only paid orders can be reviewed
        ]);
        
        // Add product to order
        $this->order->orderItems()->create([
            'product_id' => $this->product->id,
            'quantity' => 1,
            'unit_price' => $this->product->price,
        ]);
    }

    /**
     * Test creating a product review.
     */
    public function test_can_create_product_review(): void
    {
        $reviewData = [
            'reviewable_id' => $this->product->id,
            'reviewable_type' => Product::class,
            'rating' => 5,
            'headline' => 'Excellent product!',
            'body' => 'This is one of the best products I have ever purchased. Highly recommended.',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ])->postJson('/api/reviews', $reviewData);

        $response->assertStatus(201)
                 ->assertJsonPath('message', 'Review created successfully')
                 ->assertJsonStructure([
                     'message',
                     'review' => [
                         'user_id',
                         'reviewable_id',
                         'reviewable_type',
                         'rating',
                         'headline',
                         'body',
                         'id',
                         'created_at',
                         'updated_at',
                     ]
                 ]);
        
        // Check review created in database
        $this->assertDatabaseHas('reviews', [
            'user_id' => $this->user->id,
            'reviewable_id' => $this->product->id,
            'reviewable_type' => Product::class,
            'rating' => 5,
            'headline' => 'Excellent product!',
        ]);
    }
    
    /**
     * Test creating an order review.
     */
    public function test_can_create_order_review(): void
    {
        $reviewData = [
            'reviewable_id' => $this->order->id,
            'reviewable_type' => Order::class,
            'rating' => 4,
            'headline' => 'Good service',
            'body' => 'The order was processed quickly and arrived well packaged.',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ])->postJson('/api/reviews', $reviewData);

        $response->assertStatus(201)
                 ->assertJsonPath('message', 'Review created successfully');
        
        // Check review created in database
        $this->assertDatabaseHas('reviews', [
            'user_id' => $this->user->id,
            'reviewable_id' => $this->order->id,
            'reviewable_type' => Order::class,
            'rating' => 4,
            'headline' => 'Good service',
        ]);
    }

    /**
     * Test that a user cannot review another user's order.
     */
    public function test_cannot_review_another_users_order(): void
    {
        // Create another user
        $anotherUser = User::factory()->create();
        
        // Create an order for the other user
        $otherOrder = Order::factory()->create([
            'user_id' => $anotherUser->id,
            'status' => 'paid',
        ]);
        
        $reviewData = [
            'reviewable_id' => $otherOrder->id,
            'reviewable_type' => Order::class,
            'rating' => 3,
            'headline' => 'Unauthorized review',
            'body' => 'This should not be allowed.',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ])->postJson('/api/reviews', $reviewData);

        // Should get a 403 Forbidden response as authorization should fail
        $response->assertStatus(403);
        
        // Check review not created in database
        $this->assertDatabaseMissing('reviews', [
            'reviewable_id' => $otherOrder->id,
            'reviewable_type' => Order::class,
            'headline' => 'Unauthorized review',
        ]);
    }

    /**
     * Test listing reviews for a product.
     */
    public function test_can_list_product_reviews(): void
    {
        // Create multiple reviews for the product
        Review::factory()->count(3)->create([
            'reviewable_id' => $this->product->id,
            'reviewable_type' => Product::class,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ])->getJson('/api/reviews/product/' . $this->product->id);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'entity' => [
                         'id',
                         'type',
                         'avg_rating'
                     ],
                     'reviews' => [
                         'data'
                     ]
                 ])
                 ->assertJsonCount(3, 'reviews.data');
    }

    /**
     * Test listing reviews for an order.
     */
    public function test_can_list_order_reviews(): void
    {
        // Create a review for the order
        Review::factory()->create([
            'reviewable_id' => $this->order->id,
            'reviewable_type' => Order::class,
            'user_id' => $this->user->id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ])->getJson('/api/reviews/order/' . $this->order->id);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'entity' => [
                         'id',
                         'type',
                         'avg_rating'
                     ],
                     'reviews'
                 ])
                 ->assertJsonCount(1, 'reviews.data');
    }

    /**
     * Test that a user cannot view reviews for another user's order.
     */
    public function test_cannot_view_another_users_order_reviews(): void
    {
        // Create another user
        $anotherUser = User::factory()->create();
        
        // Create an order for the other user
        $otherOrder = Order::factory()->create([
            'user_id' => $anotherUser->id,
        ]);
        
        // Create a review for the order
        Review::factory()->create([
            'reviewable_id' => $otherOrder->id,
            'reviewable_type' => Order::class,
            'user_id' => $anotherUser->id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ])->getJson('/api/reviews/order/' . $otherOrder->id);

        // Should get a 403 Forbidden response
        $response->assertStatus(403);
    }
}
