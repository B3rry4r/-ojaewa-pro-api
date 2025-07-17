<?php

namespace Tests\Feature\Admin;

use App\Models\BusinessProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminBusinessTest extends AdminTestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_business_profiles_by_category()
    {
        // Set up admin authentication
        $this->setUpAdmin();

        // Create test data
        $user = User::factory()->create();
        
        // Create 2 business profiles in different categories
        $beautyBusiness = BusinessProfile::factory()->create([
            'user_id' => $user->id,
            'category' => 'beauty',
            'store_status' => 'approved'
        ]);
        
        $musicBusiness = BusinessProfile::factory()->create([
            'user_id' => $user->id,
            'category' => 'music',
            'store_status' => 'approved'
        ]);

        // Make request to list beauty businesses
        $response = $this->getJson('/api/admin/business/beauty');

        // Assert successful response
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data'
            ])
            ->assertJson([
                'status' => 'success',
                'message' => 'beauty businesses retrieved successfully',
            ]);
            
        // Verify only the beauty business is returned
        $this->assertCount(1, $response->json('data.data'));
        $this->assertEquals('beauty', $response->json('data.data.0.category'));
    }
    
    public function test_admin_can_view_business_profile_details()
    {
        // Set up admin authentication
        $this->setUpAdmin();

        // Create test data
        $user = User::factory()->create();
        
        // Create a business profile
        $business = BusinessProfile::factory()->create([
            'user_id' => $user->id,
            'category' => 'school',
            'store_status' => 'approved',
            'business_name' => 'Test School',
            'business_description' => 'A test school for API testing'
        ]);

        // Make request to view business details
        $response = $this->getJson("/api/admin/business/school/{$business->id}");

        // Assert successful response
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id',
                    'user_id',
                    'category',
                    'business_name',
                    'business_description',
                    'store_status',
                ]
            ])
            ->assertJson([
                'status' => 'success',
                'message' => 'Business profile retrieved successfully',
                'data' => [
                    'id' => $business->id,
                    'category' => 'school',
                    'business_name' => 'Test School',
                ]
            ]);
    }
    
    public function test_admin_can_update_business_status()
    {
        // Set up admin authentication
        $this->setUpAdmin();

        // Create test data
        $user = User::factory()->create();
        
        // Create a business profile with pending status
        $business = BusinessProfile::factory()->create([
            'user_id' => $user->id,
            'category' => 'beauty',
            'store_status' => 'pending',
            'business_name' => 'Beauty Salon'
        ]);

        // Make request to update business status
        $response = $this->patchJson("/api/admin/business/beauty/{$business->id}/status", [
            'store_status' => 'approved'
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
                'message' => 'Business profile status updated to approved successfully',
                'data' => [
                    'id' => $business->id,
                    'store_status' => 'approved'
                ]
            ]);
            
        // Verify the business status is actually updated in the database
        $this->assertEquals(
            'approved',
            BusinessProfile::find($business->id)->store_status
        );
    }
}
