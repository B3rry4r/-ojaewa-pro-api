<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\SellerProfile;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminProductTest extends AdminTestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_all_products()
    {
        // Set up admin authentication
        $this->setUpAdmin();

        // Create test data
        $user = User::factory()->create();
        $sellerProfile = SellerProfile::factory()->create([
            'user_id' => $user->id,
            'registration_status' => 'approved'
        ]);
        
        // Create 3 products for this seller
        $products = Product::factory(3)->create([
            'seller_profile_id' => $sellerProfile->id,
            'status' => 'approved'
        ]);

        // Make request to list all products
        $response = $this->getJson('/api/admin/market/products');

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
                'message' => 'Products retrieved successfully',
            ]);
            
        // Verify the number of products returned
        $this->assertCount(count($products), $response->json('data.data'));
    }
    
    public function test_admin_can_view_pending_products()
    {
        // Set up admin authentication
        $this->setUpAdmin();

        // Create test data
        $user = User::factory()->create();
        $sellerProfile = SellerProfile::factory()->create([
            'user_id' => $user->id,
            'registration_status' => 'approved'
        ]);
        
        // Create 2 pending products
        Product::factory(2)->create([
            'seller_profile_id' => $sellerProfile->id,
            'status' => 'pending'
        ]);
        
        // Create 1 active product
        Product::factory()->create([
            'seller_profile_id' => $sellerProfile->id,
            'status' => 'approved'
        ]);

        // Make request to pending products endpoint
        $response = $this->getJson('/api/admin/pending/products');

        // Assert successful response
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data'
            ])
            ->assertJson([
                'status' => 'success',
                'message' => 'Pending products retrieved successfully',
            ]);
            
        // Verify only pending products are returned (should be 2)
        $this->assertCount(2, $response->json('data.data'));
        
        // Verify all returned products have pending status
        foreach ($response->json('data.data') as $product) {
            $this->assertEquals('pending', $product['status']);
        }
    }
    
    public function test_admin_can_approve_product()
    {
        // Set up admin authentication
        $this->setUpAdmin();

        // Create test data
        $user = User::factory()->create();
        $sellerProfile = SellerProfile::factory()->create([
            'user_id' => $user->id,
            'registration_status' => 'approved'
        ]);
        
        // Create a pending product
        $product = Product::factory()->create([
            'seller_profile_id' => $sellerProfile->id,
            'status' => 'pending'
        ]);

        // Make request to approve the product
        $response = $this->patchJson("/api/admin/product/{$product->id}/approve", [
            'status' => 'approved'
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
                'message' => 'Product approved successfully',
                'data' => [
                    'status' => 'approved'
                ]
            ]);
            
        // Verify the product is actually updated in the database
        $this->assertEquals(
            'approved',
            Product::find($product->id)->status
        );
    }
    
    public function test_admin_can_reject_product()
    {
        // Set up admin authentication
        $this->setUpAdmin();

        // Create test data
        $user = User::factory()->create();
        $sellerProfile = SellerProfile::factory()->create([
            'user_id' => $user->id,
            'registration_status' => 'approved'
        ]);
        
        // Create a pending product
        $product = Product::factory()->create([
            'seller_profile_id' => $sellerProfile->id,
            'status' => 'pending'
        ]);

        // Make request to reject the product
        $response = $this->patchJson("/api/admin/product/{$product->id}/approve", [
            'status' => 'rejected',
            'rejection_reason' => 'Product does not meet quality standards.'
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
                'message' => 'Product rejected successfully',
                'data' => [
                    'status' => 'rejected',
                    'rejection_reason' => 'Product does not meet quality standards.'
                ]
            ]);
            
        // Verify the product is actually updated in the database
        $updatedProduct = Product::find($product->id);
        $this->assertEquals('rejected', $updatedProduct->status);
        $this->assertEquals('Product does not meet quality standards.', $updatedProduct->rejection_reason);
    }
    
    public function test_admin_can_update_product_status()
    {
        // Set up admin authentication
        $this->setUpAdmin();

        // Create test data
        $user = User::factory()->create();
        $sellerProfile = SellerProfile::factory()->create([
            'user_id' => $user->id,
            'registration_status' => 'approved'
        ]);
        
        // Create an active product
        $product = Product::factory()->create([
            'seller_profile_id' => $sellerProfile->id,
            'status' => 'approved'
        ]);

        // Make request to update the product status
        $response = $this->patchJson("/api/admin/market/product/{$product->id}/status", [
            'status' => 'rejected'
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
                'message' => 'Product status updated to rejected successfully',
                'data' => [
                    'status' => 'rejected'
                ]
            ]);
            
        // Verify the product status is actually updated in the database
        $this->assertEquals(
            'rejected',
            Product::find($product->id)->status
        );
    }
}
