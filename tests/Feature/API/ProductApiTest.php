<?php

namespace Tests\Feature\API;

use App\Models\Product;
use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    
    protected User $user;
    protected SellerProfile $sellerProfile;
    protected string $token;

    /**
     * Set up the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a user with a seller profile
        $this->user = User::factory()->create();
        $this->sellerProfile = SellerProfile::factory()->create([
            'user_id' => $this->user->id,
            'registration_status' => 'approved',
        ]);
        
        // Create a token for the user
        $this->token = $this->user->createToken('test-token')->plainTextToken;
    }

    /**
     * Test creating a product.
     */
    public function test_can_create_product(): void
    {
        $productData = [
            'name' => 'Test Agbada',
            'gender' => 'male',
            'style' => 'traditional',
            'tribe' => 'yoruba',
            'description' => 'A beautiful traditional agbada',
            'image' => 'https://example.com/image.jpg',
            'size' => 'L',
            'processing_time_type' => 'normal',
            'processing_days' => 3,
            'price' => 15000.00,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ])->postJson('/api/products', $productData);

        $response->assertStatus(201)
                 ->assertJsonFragment([
                     'name' => $productData['name'],
                     'status' => 'pending', // Default status for new products
                 ]);
        
        $this->assertDatabaseHas('products', [
            'name' => $productData['name'],
            'seller_profile_id' => $this->sellerProfile->id,
        ]);
    }

    /**
     * Test listing products for a seller.
     */
    public function test_can_list_products(): void
    {
        // Create some products for the seller profile
        Product::factory()->count(3)->create([
            'seller_profile_id' => $this->sellerProfile->id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ])->getJson('/api/products');

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data');
    }

    /**
     * Test viewing a single product.
     */
    public function test_can_view_product(): void
    {
        $product = Product::factory()->create([
            'seller_profile_id' => $this->sellerProfile->id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ])->getJson('/api/products/' . $product->id);

        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $product->id,
                     'name' => $product->name,
                 ]);
    }

    /**
     * Test updating a product.
     */
    public function test_can_update_product(): void
    {
        $product = Product::factory()->create([
            'seller_profile_id' => $this->sellerProfile->id,
        ]);

        $updateData = [
            'name' => 'Updated Agbada',
            'price' => 18000.00,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ])->putJson('/api/products/' . $product->id, $updateData);

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Product updated successfully',
                     'product' => [
                         'name' => $updateData['name'],
                         'price' => $updateData['price'],
                     ]
                 ]);
        
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => $updateData['name'],
        ]);
    }

    /**
     * Test deleting a product.
     */
    public function test_can_delete_product(): void
    {
        $product = Product::factory()->create([
            'seller_profile_id' => $this->sellerProfile->id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ])->deleteJson('/api/products/' . $product->id);

        $response->assertStatus(200);
        
        // Since we're using soft deletes, the record should still exist but have a deleted_at timestamp
        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    /**
     * Test getting product suggestions.
     */
    public function test_can_get_product_suggestions(): void
    {
        // Create some products with specific attributes for filtering
        Product::factory()->count(5)->create([
            'gender' => 'female',
            'status' => 'approved',
            'tribe' => 'yoruba',
        ]);
        
        Product::factory()->count(3)->create([
            'gender' => 'male',
            'status' => 'approved',
            'tribe' => 'igbo',
        ]);

        // Test suggestions with filters
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ])->getJson('/api/products/suggestions?gender=female&tribe=yoruba&count=3');

        $response->assertStatus(200)
                 ->assertJsonCount(3);

        // Verify all products match the filter criteria
        $responseData = $response->json();
        foreach ($responseData as $product) {
            $this->assertEquals('female', $product['gender']);
            $this->assertEquals('yoruba', $product['tribe']);
        }
    }

    /**
     * Test authorization - can't update another seller's product.
     */
    public function test_cannot_update_other_sellers_product(): void
    {
        // Create another user with a seller profile
        $otherUser = User::factory()->create();
        $otherSellerProfile = SellerProfile::factory()->create([
            'user_id' => $otherUser->id,
            'registration_status' => 'approved',
        ]);
        
        // Create a product for the other seller
        $otherProduct = Product::factory()->create([
            'seller_profile_id' => $otherSellerProfile->id,
        ]);

        // Try to update the other seller's product with our token
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ])->putJson('/api/products/' . $otherProduct->id, ['name' => 'Unauthorized Update']);

        // Should get a 403 Forbidden response
        $response->assertStatus(403);
        
        // Ensure the product wasn't updated
        $this->assertDatabaseMissing('products', [
            'id' => $otherProduct->id,
            'name' => 'Unauthorized Update',
        ]);
    }
}
