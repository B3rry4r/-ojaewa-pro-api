<?php

namespace Tests\Feature\Admin;

use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Models\BusinessProfile;
use App\Models\SellerProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminDashboardTest extends AdminTestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_dashboard_overview()
    {
        // Set up admin authentication
        $this->setUpAdmin();

        // Create test data
        User::factory(5)->create();
        SellerProfile::factory(3)->create();
        BusinessProfile::factory(2)->create();
        $product = Product::factory()->create();
        Order::factory(2)->create([
            'status' => 'paid',
            'total_price' => 100.00
        ]);

        // Make request to dashboard overview endpoint
        $response = $this->getJson('/api/admin/dashboard/overview');

        // Assert successful response
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'total_users',
                    'total_revenue',
                    'total_businesses',
                    'total_sellers',
                    'market_revenue'
                ]
            ])
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'total_users' => User::count(),
                    'total_businesses' => BusinessProfile::count(),
                    'total_sellers' => SellerProfile::count(),
                ]
            ]);
    }

    public function test_unauthorized_user_cannot_access_dashboard_overview()
    {
        // Do not authenticate as admin
        
        // Make request to dashboard overview endpoint
        $response = $this->getJson('/api/admin/dashboard/overview');

        // Assert unauthorized response
        $response->assertStatus(401);
    }
}
