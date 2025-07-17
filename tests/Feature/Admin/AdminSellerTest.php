<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\SellerProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminSellerTest extends AdminTestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_all_sellers()
    {
        // Set up admin authentication
        $this->setUpAdmin();

        // Create test data
        $users = User::factory(3)->create();
        $sellerProfiles = [];
        
        foreach ($users as $user) {
            $sellerProfiles[] = SellerProfile::factory()->create([
                'user_id' => $user->id,
                'registration_status' => 'approved'
            ]);
        }

        // Make request to list all sellers
        $response = $this->getJson('/api/admin/market/sellers');

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
                'message' => 'Seller profiles retrieved successfully',
            ]);
            
        // Verify the number of sellers returned
        $this->assertCount(count($sellerProfiles), $response->json('data.data'));
    }
    
    public function test_admin_can_view_pending_sellers()
    {
        // Set up admin authentication
        $this->setUpAdmin();

        // Create test data with both pending and approved sellers
        $users = User::factory(4)->create();
        
        // Create 2 pending sellers
        SellerProfile::factory()->create([
            'user_id' => $users[0]->id,
            'registration_status' => 'pending'
        ]);
        
        SellerProfile::factory()->create([
            'user_id' => $users[1]->id,
            'registration_status' => 'pending'
        ]);
        
        // Create 2 approved sellers
        SellerProfile::factory()->create([
            'user_id' => $users[2]->id,
            'registration_status' => 'approved'
        ]);
        
        SellerProfile::factory()->create([
            'user_id' => $users[3]->id,
            'registration_status' => 'approved'
        ]);

        // Make request to pending sellers endpoint
        $response = $this->getJson('/api/admin/pending/sellers');

        // Assert successful response
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data'
            ])
            ->assertJson([
                'status' => 'success',
                'message' => 'Pending seller profiles retrieved successfully',
            ]);
            
        // Verify only pending sellers are returned (should be 2)
        $this->assertCount(2, $response->json('data.data'));
        
        // Verify all returned sellers have pending status
        foreach ($response->json('data.data') as $seller) {
            $this->assertEquals('pending', $seller['registration_status']);
        }
    }
    
    public function test_admin_can_approve_seller()
    {
        // Set up admin authentication
        $this->setUpAdmin();

        // Create a user with a pending seller profile
        $user = User::factory()->create();
        $sellerProfile = SellerProfile::factory()->create([
            'user_id' => $user->id,
            'registration_status' => 'pending'
        ]);

        // Make request to approve the seller
        $response = $this->patchJson("/api/admin/seller/{$sellerProfile->id}/approve", [
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
                'message' => 'Seller profile approved successfully',
                'data' => [
                    'registration_status' => 'approved'
                ]
            ]);
            
        // Verify the seller is actually updated in the database
        $this->assertEquals(
            'approved',
            SellerProfile::find($sellerProfile->id)->registration_status
        );
    }
    
    public function test_admin_can_reject_seller()
    {
        // Set up admin authentication
        $this->setUpAdmin();

        // Create a user with a pending seller profile
        $user = User::factory()->create();
        $sellerProfile = SellerProfile::factory()->create([
            'user_id' => $user->id,
            'registration_status' => 'pending'
        ]);

        // Make request to reject the seller
        $response = $this->patchJson("/api/admin/seller/{$sellerProfile->id}/approve", [
            'status' => 'rejected',
            'rejection_reason' => 'Incomplete information provided.'
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
                'message' => 'Seller profile rejected successfully',
                'data' => [
                    'registration_status' => 'rejected',
                    'rejection_reason' => 'Incomplete information provided.'
                ]
            ]);
            
        // Verify the seller is actually updated in the database
        $updatedSeller = SellerProfile::find($sellerProfile->id);
        $this->assertEquals('rejected', $updatedSeller->registration_status);
        $this->assertEquals('Incomplete information provided.', $updatedSeller->rejection_reason);
    }
    
    public function test_admin_can_update_seller_status()
    {
        // Set up admin authentication
        $this->setUpAdmin();

        // Create an approved seller
        $user = User::factory()->create();
        $sellerProfile = SellerProfile::factory()->create([
            'user_id' => $user->id,
            'registration_status' => 'approved',
            'active' => true
        ]);

        // Make request to deactivate the seller
        $response = $this->patchJson("/api/admin/market/seller/{$sellerProfile->id}/status", [
            'active' => false
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
                'message' => 'Seller profile deactivated successfully',
                'data' => [
                    'active' => false
                ]
            ]);
            
        // Verify the seller is actually deactivated in the database
        $this->assertFalse(
            (bool) SellerProfile::find($sellerProfile->id)->active
        );
    }
}
