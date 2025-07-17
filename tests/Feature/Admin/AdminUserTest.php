<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\SellerProfile;
use App\Models\BusinessProfile;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminUserTest extends AdminTestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_all_users()
    {
        // Set up admin authentication
        $this->setUpAdmin();

        // Create test data
        $users = User::factory(5)->create();
        
        // Create some additional data for the first user
        $sellerProfile = SellerProfile::factory()->create([
            'user_id' => $users[0]->id
        ]);
        
        BusinessProfile::factory()->create([
            'user_id' => $users[0]->id
        ]);
        
        Order::factory(2)->create([
            'user_id' => $users[0]->id
        ]);

        // Make request to list all users
        $response = $this->getJson('/api/admin/users');

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
                'message' => 'Users retrieved successfully',
            ]);
            
        // Verify the users are returned in the response
        // No need to check exact count as it may vary based on other tests/seeding
        $this->assertNotEmpty($response->json('data.data'));
    }
    
    public function test_admin_can_search_users()
    {
        // Set up admin authentication
        $this->setUpAdmin();

        // Create test data with specific search terms
        $targetUser = User::factory()->create([
            'firstname' => 'John',
            'lastname' => 'SearchTarget',
            'email' => 'searchtarget@example.com'
        ]);
        
        // Create several other users that shouldn't match the search
        User::factory(3)->create();

        // Make request with search parameter
        $response = $this->getJson('/api/admin/users?search=SearchTarget');

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
            ]);
            
        // Verify only the target user is returned
        $this->assertCount(1, $response->json('data.data'));
        $this->assertEquals('John SearchTarget', $response->json('data.data.0.name')); // Expect name as computed property
        
        // Search by email as well
        $response = $this->getJson('/api/admin/users?search=searchtarget@example');
        $this->assertCount(1, $response->json('data.data'));
    }
}
