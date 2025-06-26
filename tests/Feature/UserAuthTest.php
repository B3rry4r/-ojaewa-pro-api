<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class UserAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_and_login(): void
    {
        $register = $this->postJson('/api/register', [
            'firstname' => 'John',
            'lastname'  => 'Doe',
            'email'     => 'john@example.com',
            'password'  => 'password',
            'phone'     => '08012345678',
        ])->assertCreated();

        $this->assertArrayHasKey('token', $register->json());

        $login = $this->postJson('/api/login', [
            'email'    => 'john@example.com',
            'password' => 'password',
        ])->assertOk();

        $this->assertArrayHasKey('token', $login->json());
    }

    public function test_user_can_request_password_reset(): void
    {
        Notification::fake();

        $user = User::factory()->create(['email' => 'reset@example.com']);

        $this->postJson('/api/password/forgot', [
            'email' => 'reset@example.com',
        ])->assertOk();

        Notification::assertSentTimes(\Illuminate\Auth\Notifications\ResetPassword::class, 1);
    }
}
