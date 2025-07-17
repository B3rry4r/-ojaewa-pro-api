<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\BusinessProfile;
use App\Models\Wishlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class WishlistTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Product $product;
    private BusinessProfile $businessProfile;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->product = Product::factory()->create();
        $this->businessProfile = BusinessProfile::factory()->create();
    }

    public function test_user_can_get_wishlist_items()
    {
        // Create wishlist items
        Wishlist::create([
            'user_id' => $this->user->id,
            'wishlistable_id' => $this->product->id,
            'wishlistable_type' => Product::class,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/wishlist');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'data' => [
                        '*' => [
                            'id',
                            'user_id',
                            'wishlistable_id',
                            'wishlistable_type',
                            'wishlistable',
                            'created_at',
                            'updated_at'
                        ]
                    ]
                ]
            ]);
    }

    public function test_user_can_add_product_to_wishlist()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/wishlist', [
                'wishlistable_type' => 'product',
                'wishlistable_id' => $this->product->id,
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'status' => 'success',
                'message' => 'Item added to wishlist'
            ]);

        $this->assertDatabaseHas('wishlists', [
            'user_id' => $this->user->id,
            'wishlistable_id' => $this->product->id,
            'wishlistable_type' => Product::class,
        ]);
    }

    public function test_user_can_add_business_profile_to_wishlist()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/wishlist', [
                'wishlistable_type' => 'business_profile',
                'wishlistable_id' => $this->businessProfile->id,
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'status' => 'success',
                'message' => 'Item added to wishlist'
            ]);

        $this->assertDatabaseHas('wishlists', [
            'user_id' => $this->user->id,
            'wishlistable_id' => $this->businessProfile->id,
            'wishlistable_type' => BusinessProfile::class,
        ]);
    }

    public function test_cannot_add_duplicate_item_to_wishlist()
    {
        // Add item first time
        Wishlist::create([
            'user_id' => $this->user->id,
            'wishlistable_id' => $this->product->id,
            'wishlistable_type' => Product::class,
        ]);

        // Try to add same item again
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/wishlist', [
                'wishlistable_type' => 'product',
                'wishlistable_id' => $this->product->id,
            ]);

        $response->assertStatus(409)
            ->assertJson([
                'status' => 'error',
                'message' => 'Item already in wishlist'
            ]);
    }

    public function test_cannot_add_nonexistent_item_to_wishlist()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/wishlist', [
                'wishlistable_type' => 'product',
                'wishlistable_id' => 99999,
            ]);

        $response->assertStatus(404)
            ->assertJson([
                'status' => 'error',
                'message' => 'Item not found'
            ]);
    }

    public function test_user_can_remove_item_from_wishlist()
    {
        // Add item to wishlist
        Wishlist::create([
            'user_id' => $this->user->id,
            'wishlistable_id' => $this->product->id,
            'wishlistable_type' => Product::class,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson('/api/wishlist', [
                'wishlistable_type' => 'product',
                'wishlistable_id' => $this->product->id,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Item removed from wishlist'
            ]);

        $this->assertDatabaseMissing('wishlists', [
            'user_id' => $this->user->id,
            'wishlistable_id' => $this->product->id,
            'wishlistable_type' => Product::class,
        ]);
    }

    public function test_cannot_remove_nonexistent_item_from_wishlist()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson('/api/wishlist', [
                'wishlistable_type' => 'product',
                'wishlistable_id' => $this->product->id,
            ]);

        $response->assertStatus(404)
            ->assertJson([
                'status' => 'error',
                'message' => 'Item not found in wishlist'
            ]);
    }

    public function test_wishlist_requires_authentication()
    {
        $response = $this->getJson('/api/wishlist');
        $response->assertStatus(401);

        $response = $this->postJson('/api/wishlist', [
            'wishlistable_type' => 'product',
            'wishlistable_id' => $this->product->id,
        ]);
        $response->assertStatus(401);

        $response = $this->deleteJson('/api/wishlist', [
            'wishlistable_type' => 'product',
            'wishlistable_id' => $this->product->id,
        ]);
        $response->assertStatus(401);
    }

    public function test_wishlist_validation_rules()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/wishlist', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['wishlistable_type', 'wishlistable_id']);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/wishlist', [
                'wishlistable_type' => 'invalid_type',
                'wishlistable_id' => 'not_integer',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['wishlistable_type', 'wishlistable_id']);
    }
}
