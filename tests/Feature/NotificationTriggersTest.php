<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Blog;
use App\Models\BusinessProfile;
use App\Models\Order;
use App\Models\Product;
use App\Models\SellerProfile;
use App\Models\Subscription;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

class NotificationTriggersTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Mock HTTP requests for external APIs
        Http::fake([
            'https://api.resend.com/emails' => Http::response(['id' => 'test-email-id'], 200),
            'https://*/publishes' => Http::response(['publishId' => 'test-push-id'], 200)
        ]);
        
        // Mock the view facade to avoid template rendering issues
        View::shouldReceive('make')
            ->zeroOrMoreTimes()
            ->andReturnSelf();
        
        View::shouldReceive('render')
            ->zeroOrMoreTimes()
            ->andReturn('<html><body>Test Email Content</body></html>');
        
        // Create test users
        $this->user = User::factory()->create();
        $this->admin = Admin::factory()->create();
        
        // Set up environment variables for testing
        config([
            'services.resend.api_key' => 'test-key',
            'services.resend.from_email' => 'test@example.com',
            'services.pusher_beams.instance_id' => 'test-instance',
            'services.pusher_beams.secret_key' => 'test-secret'
        ]);
    }

    /** @test */
    public function it_sends_notification_when_order_is_created()
    {
        // Create seller and approved product
        $seller = SellerProfile::factory()->create(['user_id' => $this->user->id]);
        $product = Product::factory()->create([
            'seller_profile_id' => $seller->id,
            'status' => 'approved'
        ]);

        // Create order via API
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/orders', [
                'items' => [
                    [
                        'product_id' => $product->id,
                        'quantity' => 2
                    ]
                ]
            ]);

        $response->assertStatus(201);

        // Assert email API was called
        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.resend.com/emails' &&
                   $request['subject'] === 'Order Confirmation - Oja Ewa';
        });

        // Assert push API was called
        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'publishes') &&
                   $request->data()['web']['notification']['title'] === 'Order Confirmed!';
        });
    }

    /** @test */
    public function it_sends_notification_when_order_status_is_updated()
    {
        // Create order with pending status
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'pending'
        ]);

        // Update order status
        $response = $this->actingAs($this->admin, 'sanctum')
            ->patchJson("/api/admin/order/{$order->id}/status", [
                'status' => 'paid',
                'tracking_number' => 'TRACK123'
            ]);

        $response->assertStatus(200);

        // Assert email API was called
        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.resend.com/emails' &&
                   $request['subject'] === 'Order Status Update - Oja Ewa';
        });

        // Assert push API was called
        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'publishes') &&
                   $request->data()['web']['notification']['title'] === 'Order Status Updated';
        });
    }

    /** @test */
    public function it_sends_notification_when_business_is_approved()
    {
        // Create business profile
        $business = BusinessProfile::factory()->create([
            'user_id' => $this->user->id,
            'category' => 'beauty',
            'store_status' => 'pending'
        ]);

        // Approve business via admin API
        $response = $this->actingAs($this->admin, 'sanctum')
            ->patchJson("/api/admin/business/beauty/{$business->id}/status", [
                'store_status' => 'approved'
            ]);

        $response->assertStatus(200);

        // Assert email API was called
        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.resend.com/emails' &&
                   $request['subject'] === 'Business Profile Update - Oja Ewa';
        });

        // Assert push API was called
        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'publishes') &&
                   $request->data()['web']['notification']['title'] === 'Business Approved!';
        });
    }

    /** @test */
    public function it_sends_notification_when_business_is_rejected()
    {
        // Create business profile
        $business = BusinessProfile::factory()->create([
            'user_id' => $this->user->id,
            'category' => 'brand',
            'store_status' => 'pending'
        ]);

        // Reject business via admin API
        $response = $this->actingAs($this->admin, 'sanctum')
            ->patchJson("/api/admin/business/brand/{$business->id}/status", [
                'store_status' => 'deactivated',
                'rejection_reason' => 'Incomplete documentation'
            ]);

        $response->assertStatus(200);

        // Assert email API was called
        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.resend.com/emails' &&
                   $request['subject'] === 'Business Profile Update - Oja Ewa';
        });

        // Assert push API was called
        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'publishes') &&
                   $request->data()['web']['notification']['title'] === 'Business Profile Needs Update';
        });
    }

    /** @test */
    public function it_sends_notification_when_blog_is_published()
    {
        // Create and publish blog via admin API
        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/admin/blogs', [
                'title' => 'New Blog Post',
                'body' => 'This is a new blog post content.',
                'featured_image' => 'https://example.com/image.jpg',
                'published' => true
            ]);

        $response->assertStatus(201);

        // Assert push API was called for all users
        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'publishes') &&
                   $request->data()['interests'] === ['all-users'] &&
                   $request->data()['web']['notification']['title'] === 'New Blog Post';
        });
    }

    /** @test */
    public function it_sends_notification_when_blog_is_toggled_to_published()
    {
        // Create unpublished blog
        $blog = Blog::factory()->create([
            'admin_id' => $this->admin->id,
            'published_at' => null
        ]);

        // Toggle publish status
        $response = $this->actingAs($this->admin, 'sanctum')
            ->patchJson("/api/admin/blogs/{$blog->id}/toggle-publish");

        $response->assertStatus(200);

        // Assert push API was called for all users
        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'publishes') &&
                   $request->data()['interests'] === ['all-users'] &&
                   $request->data()['web']['notification']['title'] === 'New Blog Post';
        });
    }

    /** @test */
    public function it_does_not_send_notification_when_order_status_unchanged()
    {
        // Create order
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'pending'
        ]);

        // Update with same status
        $response = $this->actingAs($this->admin, 'sanctum')
            ->patchJson("/api/admin/order/{$order->id}/status", [
                'status' => 'pending'
            ]);

        $response->assertStatus(200);

        // Assert no notification APIs were called
        Http::assertNotSent(function ($request) {
            return $request->url() === 'https://api.resend.com/emails';
        });

        Http::assertNotSent(function ($request) {
            return str_contains($request->url(), 'publishes');
        });
    }

    /** @test */
    public function it_does_not_send_notification_when_business_status_unchanged()
    {
        // Create business profile
        $business = BusinessProfile::factory()->create([
            'user_id' => $this->user->id,
            'category' => 'music',
            'store_status' => 'approved'
        ]);

        // Update with same status
        $response = $this->actingAs($this->admin, 'sanctum')
            ->patchJson("/api/admin/business/music/{$business->id}/status", [
                'store_status' => 'approved'
            ]);

        $response->assertStatus(200);

        // Assert no notification APIs were called
        Http::assertNotSent(function ($request) {
            return $request->url() === 'https://api.resend.com/emails';
        });

        Http::assertNotSent(function ($request) {
            return str_contains($request->url(), 'publishes');
        });
    }

    /** @test */
    public function it_does_not_send_blog_notification_when_already_published()
    {
        // Create published blog
        $blog = Blog::factory()->create([
            'admin_id' => $this->admin->id,
            'published_at' => now()->subDay()
        ]);

        // Update blog content
        $response = $this->actingAs($this->admin, 'sanctum')
            ->putJson("/api/admin/blogs/{$blog->id}", [
                'title' => 'Updated Blog Post',
                'body' => 'Updated content.',
                'published' => true
            ]);

        $response->assertStatus(200);

        // Assert no push notification was sent (already published)
        Http::assertNotSent(function ($request) {
            return str_contains($request->url(), 'publishes');
        });
    }

    /** @test */
    public function it_handles_notification_failures_gracefully()
    {
        // Mock API failure
        Http::fake([
            'https://api.resend.com/emails' => Http::response(['error' => 'API Error'], 500),
            'https://*/publishes' => Http::response(['error' => 'Push Error'], 500)
        ]);

        // Create seller and approved product
        $seller = SellerProfile::factory()->create(['user_id' => $this->user->id]);
        $product = Product::factory()->create([
            'seller_profile_id' => $seller->id,
            'status' => 'approved'
        ]);

        // Create order (should not fail even if notifications fail)
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/orders', [
                'items' => [
                    [
                        'product_id' => $product->id,
                        'quantity' => 1
                    ]
                ]
            ]);

        // Order creation should still succeed
        $response->assertStatus(201);
        
        // Verify order was created
        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->id
        ]);
    }
}
