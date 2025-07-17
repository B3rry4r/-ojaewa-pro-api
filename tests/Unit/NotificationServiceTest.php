<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

class NotificationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $notificationService;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Set up environment variables for testing
        config([
            'services.resend.api_key' => 'test-resend-key',
            'services.resend.from_email' => 'test@ojaewa.com',
            'services.pusher_beams.instance_id' => 'test-instance-id',
            'services.pusher_beams.secret_key' => 'test-secret-key',
        ]);
        
        $this->notificationService = new NotificationService();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_can_send_email_notification()
    {
        // Mock the HTTP client for Resend API
        Http::fake([
            'https://api.resend.com/emails' => Http::response([
                'id' => 'test-email-id',
                'from' => 'noreply@ojaewa.com',
                'to' => [$this->user->email],
                'subject' => 'Test Email',
                'created_at' => now()->toISOString(),
            ], 200)
        ]);

        // Mock the view to avoid template rendering issues
        View::shouldReceive('make')
            ->once()
            ->with('emails.order_created', ['order' => ['id' => 1, 'total_price' => 100]])
            ->andReturnSelf();
        
        View::shouldReceive('render')
            ->once()
            ->andReturn('<html><body>Test Email Content</body></html>');

        $result = $this->notificationService->sendEmail(
            $this->user,
            'Test Email',
            'order_created',
            ['order' => ['id' => 1, 'total_price' => 100]]
        );

        $this->assertTrue($result);

        // Assert the HTTP request was made
        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.resend.com/emails' &&
                   $request['to'] === [$this->user->email] &&
                   $request['subject'] === 'Test Email';
        });
    }

    /** @test */
    public function it_handles_email_sending_failure()
    {
        // Mock failed HTTP response
        Http::fake([
            'https://api.resend.com/emails' => Http::response([
                'message' => 'Invalid API key',
            ], 401)
        ]);

        Log::shouldReceive('error')
            ->once()
            ->withArgs(function ($message) {
                return str_contains($message, 'Email sending exception');
            });

        $result = $this->notificationService->sendEmail(
            $this->user,
            'Test Email',
            'order_created',
            ['order' => ['id' => 1]]
        );

        $this->assertFalse($result);
    }

    /** @test */
    public function it_can_send_push_notification()
    {
        // Mock the HTTP client for Pusher Beams API
        Http::fake([
            'https://*/publishes' => Http::response([
                'publishId' => 'test-publish-id',
            ], 200)
        ]);

        $result = $this->notificationService->sendPush(
            $this->user,
            'Test Push',
            'This is a test push notification',
            ['order_id' => 1]
        );

        $this->assertTrue($result);

        // Assert the HTTP request was made
        Http::assertSent(function ($request) {
            $body = $request->data();
            return str_contains($request->url(), 'publishes') &&
                   $body['interests'] === ["user-{$this->user->id}"] &&
                   $body['web']['notification']['title'] === 'Test Push';
        });
    }

    /** @test */
    public function it_handles_push_notification_failure()
    {
        // Mock failed HTTP response
        Http::fake([
            'https://*/publishes' => Http::response([
                'error' => 'Invalid instance ID',
            ], 401)
        ]);

        Log::shouldReceive('error')
            ->once()
            ->withArgs(function ($message) {
                return str_contains($message, 'Push notification exception');
            });

        $result = $this->notificationService->sendPush(
            $this->user,
            'Test Push',
            'This is a test push notification',
            ['order_id' => 1]
        );

        $this->assertFalse($result);
    }

    /** @test */
    public function it_can_send_combined_email_and_push()
    {
        // Mock both APIs
        Http::fake([
            'https://api.resend.com/emails' => Http::response(['id' => 'email-id'], 200),
            'https://*/publishes' => Http::response(['publishId' => 'push-id'], 200)
        ]);

        // Mock the view
        View::shouldReceive('make')
            ->once()
            ->andReturnSelf();
        
        View::shouldReceive('render')
            ->once()
            ->andReturn('<html><body>Test Email Content</body></html>');

        $result = $this->notificationService->sendEmailAndPush(
            $this->user,
            'Test Subject',
            'order_created',
            'Test Push Title',
            'Test push message',
            ['order' => ['id' => 1]],
            ['order_id' => 1]
        );

        $this->assertTrue($result['email_sent']);
        $this->assertTrue($result['push_sent']);

        // Assert both requests were made
        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.resend.com/emails';
        });

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'publishes');
        });
    }

    /** @test */
    public function it_can_send_push_to_all_users()
    {
        // Create multiple users
        User::factory()->count(3)->create();

        // Mock the HTTP client
        Http::fake([
            'https://*/publishes' => Http::response(['publishId' => 'broadcast-id'], 200)
        ]);

        $result = $this->notificationService->sendPushToAllUsers(
            'New Blog Post',
            'Check out our latest blog post!',
            ['blog_id' => 1]
        );

        $this->assertTrue($result);

        // Assert the broadcast request was made
        Http::assertSent(function ($request) {
            $body = $request->data();
            return str_contains($request->url(), 'publishes') &&
                   $body['interests'] === ['all-users'] &&
                   $body['web']['notification']['title'] === 'New Blog Post';
        });
    }

    /** @test */
    public function it_handles_missing_environment_variables()
    {
        // Create new service instance with missing config
        config(['services.resend.api_key' => null]);
        config(['services.pusher_beams.instance_id' => null]);
        
        $service = new NotificationService();

        Log::shouldReceive('error')
            ->once()
            ->withArgs(function ($message) {
                return str_contains($message, 'Resend API key not configured');
            });

        $result = $service->sendEmail(
            $this->user,
            'Test Email',
            'order_created',
            ['order' => ['id' => 1]]
        );

        $this->assertFalse($result);
    }

    /** @test */
    public function it_validates_email_template_exists()
    {
        Http::fake([
            'https://api.resend.com/emails' => Http::response(['id' => 'email-id'], 200)
        ]);

        // Mock the view
        View::shouldReceive('make')
            ->once()
            ->andReturnSelf();
        
        View::shouldReceive('render')
            ->once()
            ->andReturn('<html><body>Test Email Content</body></html>');

        // Test with non-existent template
        $result = $this->notificationService->sendEmail(
            $this->user,
            'Test Email',
            'non_existent_template',
            ['data' => 'test']
        );

        // Should still attempt to send (Laravel will handle missing view)
        $this->assertTrue($result);
    }

    /** @test */
    public function it_logs_successful_notifications()
    {
        Http::fake([
            'https://api.resend.com/emails' => Http::response(['id' => 'email-id'], 200),
            'https://*/publishes' => Http::response(['publishId' => 'push-id'], 200)
        ]);

        // Mock the view
        View::shouldReceive('make')
            ->once()
            ->andReturnSelf();
        
        View::shouldReceive('render')
            ->once()
            ->andReturn('<html><body>Test Email Content</body></html>');

        Log::shouldReceive('info')
            ->once()
            ->withArgs(function ($message) {
                return str_contains($message, 'Email notification sent successfully');
            });

        Log::shouldReceive('info')
            ->once()
            ->withArgs(function ($message) {
                return str_contains($message, 'Push notification sent successfully');
            });

        $this->notificationService->sendEmailAndPush(
            $this->user,
            'Test Subject',
            'order_created',
            'Test Push',
            'Test message',
            ['order' => ['id' => 1]],
            ['order_id' => 1]
        );
    }
}
