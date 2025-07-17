<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
    }

    public function test_user_can_get_notifications()
    {
        Notification::factory()->count(3)->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/notifications');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'data' => [
                        '*' => [
                            'id',
                            'user_id',
                            'type',
                            'event',
                            'title',
                            'message',
                            'payload',
                            'read_at',
                            'created_at',
                            'updated_at'
                        ]
                    ]
                ]
            ])
            ->assertJsonCount(3, 'data.data');
    }

    public function test_user_can_get_unread_notifications_count()
    {
        // Create read and unread notifications
        Notification::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'read_at' => now()
        ]);
        
        Notification::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'read_at' => null
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/notifications/unread-count');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => ['unread_count' => 3]
            ]);
    }

    public function test_user_can_mark_notification_as_read()
    {
        $notification = Notification::factory()->create([
            'user_id' => $this->user->id,
            'read_at' => null
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->patchJson('/api/notifications/' . $notification->id . '/read');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Notification marked as read'
            ]);

        $this->assertDatabaseHas('notifications', [
            'id' => $notification->id,
        ]);
        
        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_user_can_mark_all_notifications_as_read()
    {
        Notification::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'read_at' => null
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->patchJson('/api/notifications/mark-all-read');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Marked 3 notifications as read'
            ]);

        $this->assertEquals(0, $this->user->notifications()->unread()->count());
    }

    public function test_user_can_delete_notification()
    {
        $notification = Notification::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson('/api/notifications/' . $notification->id);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Notification deleted'
            ]);

        $this->assertDatabaseMissing('notifications', ['id' => $notification->id]);
    }

    public function test_user_can_filter_notifications_by_type()
    {
        Notification::factory()->create([
            'user_id' => $this->user->id,
            'type' => 'push'
        ]);
        
        Notification::factory()->create([
            'user_id' => $this->user->id,
            'type' => 'email'
        ]);
        
        Notification::factory()->create([
            'user_id' => $this->user->id,
            'type' => 'push'
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/notifications/filter?type=push');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data.data');

        $notifications = $response->json('data.data');
        foreach ($notifications as $notification) {
            $this->assertEquals('push', $notification['type']);
        }
    }

    public function test_user_can_filter_notifications_by_event()
    {
        Notification::factory()->create([
            'user_id' => $this->user->id,
            'event' => 'order_placed'
        ]);
        
        Notification::factory()->create([
            'user_id' => $this->user->id,
            'event' => 'order_shipped'
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/notifications/filter?event=order_placed');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.data')
            ->assertJsonFragment(['event' => 'order_placed']);
    }

    public function test_user_can_filter_notifications_by_read_status()
    {
        Notification::factory()->create([
            'user_id' => $this->user->id,
            'read_at' => now()
        ]);
        
        Notification::factory()->create([
            'user_id' => $this->user->id,
            'read_at' => null
        ]);

        // Filter for read notifications
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/notifications/filter?read=1');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.data');

        // Filter for unread notifications
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/notifications/filter?read=0');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.data');
    }

    public function test_user_cannot_access_other_users_notifications()
    {
        $otherUser = User::factory()->create();
        $notification = Notification::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->patchJson('/api/notifications/' . $notification->id . '/read');

        $response->assertStatus(404)
            ->assertJson([
                'status' => 'error',
                'message' => 'Notification not found'
            ]);
    }

    public function test_notification_endpoints_require_authentication()
    {
        $response = $this->getJson('/api/notifications');
        $response->assertStatus(401);

        $response = $this->getJson('/api/notifications/unread-count');
        $response->assertStatus(401);

        $response = $this->patchJson('/api/notifications/1/read');
        $response->assertStatus(401);

        $response = $this->patchJson('/api/notifications/mark-all-read');
        $response->assertStatus(401);

        $response = $this->deleteJson('/api/notifications/1');
        $response->assertStatus(401);
    }

    public function test_filter_validation_rules()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/notifications/filter?type=invalid_type');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['type']);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/notifications/filter?read=not_boolean');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['read']);
    }

    public function test_notifications_are_ordered_by_latest()
    {
        $oldNotification = Notification::factory()->create([
            'user_id' => $this->user->id,
            'created_at' => now()->subDay()
        ]);
        
        $newNotification = Notification::factory()->create([
            'user_id' => $this->user->id,
            'created_at' => now()
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/notifications');

        $response->assertStatus(200);
        
        $notifications = $response->json('data.data');
        $this->assertEquals($newNotification->id, $notifications[0]['id']);
        $this->assertEquals($oldNotification->id, $notifications[1]['id']);
    }

    public function test_nonexistent_notification_returns_404()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->patchJson('/api/notifications/99999/read');

        $response->assertStatus(404)
            ->assertJson([
                'status' => 'error',
                'message' => 'Notification not found'
            ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson('/api/notifications/99999');

        $response->assertStatus(404)
            ->assertJson([
                'status' => 'error',
                'message' => 'Notification not found'
            ]);
    }
}
