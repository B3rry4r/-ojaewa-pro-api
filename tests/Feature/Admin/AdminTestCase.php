<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminTestCase extends TestCase
{
    /**
     * Set up admin authentication for testing
     *
     * @return void
     */
    protected function setUpAdmin()
    {
        // Create and authenticate as admin
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
            'is_super_admin' => true
        ]);
        
        // Authenticate as this admin
        Sanctum::actingAs($admin, ['admin']);
        
        return $admin;
    }
}
