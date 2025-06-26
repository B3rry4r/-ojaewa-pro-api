<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_login(): void
    {
        $admin = Admin::create([
            'firstname' => 'Super',
            'lastname'  => 'Admin',
            'email'     => 'admin@ojaewa.com',
            'password'  => Hash::make('Admin@1234'),
        ]);

        $response = $this->postJson('/api/admin/login', [
            'email'    => 'admin@ojaewa.com',
            'password' => 'Admin@1234',
        ])->assertOk();

        $response->assertJsonStructure(['token', 'admin' => ['id', 'email']]);
    }
}
