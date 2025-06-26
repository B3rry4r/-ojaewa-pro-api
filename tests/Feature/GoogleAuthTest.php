<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Google_Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Tests\TestCase;

class GoogleAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_google_login_creates_user(): void
    {
        // Fake Google Client payload
        $payload = [
            'email'       => 'google@example.com',
            'given_name'  => 'Google',
            'family_name' => 'User',
        ];

        // Bind a mocked Google_Client that returns the payload
        $mock = Mockery::mock(Google_Client::class);
        $mock->shouldReceive('setClientId');
        $mock->shouldReceive('verifyIdToken')->andReturn($payload);
        $this->app->instance(Google_Client::class, $mock);

        $response = $this->postJson('/api/oauth/google', ['token' => 'fake-token'])->assertOk();

        $response->assertJsonStructure(['token', 'user' => ['id', 'email'], 'need_phone']);
        $this->assertDatabaseHas('users', ['email' => 'google@example.com']);
    }
}
