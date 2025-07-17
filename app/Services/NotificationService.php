<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use App\Models\User;

class NotificationService
{
    /**
     * Send email via Resend API
     */
    public function sendEmail(User $user, string $subject, string $view, array $data = []): bool
    {
        try {
            $apiKey = config('services.resend.api_key');
            
            if (!$apiKey) {
                Log::error('Resend API key not configured');
                return false;
            }

            // Render the email template
            $htmlContent = View::make("emails.{$view}", $data)->render();

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'Content-Type' => 'application/json',
            ])->post('https://api.resend.com/emails', [
                'from' => config('services.resend.from_email', 'noreply@ojaewa.com'),
                'to' => [$user->email],
                'subject' => $subject,
                'html' => $htmlContent,
            ]);

            if ($response->successful()) {
                Log::info("Email notification sent successfully to {$user->email}", [
                    'subject' => $subject,
                    'view' => $view
                ]);
                return true;
            } else {
                Log::error("Failed to send email to {$user->email}", [
                    'response' => $response->body(),
                    'status' => $response->status()
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error("Email sending exception: {$e->getMessage()}", [
                'to' => $user->email,
                'subject' => $subject,
                'view' => $view
            ]);
            return false;
        }
    }

    /**
     * Send push notification via Pusher Beams
     */
    public function sendPush(User $user, string $title, string $body, array $payload = []): bool
    {
        try {
            $instanceId = config('services.pusher_beams.instance_id');
            $secretKey = config('services.pusher_beams.secret_key');
            
            if (!$instanceId || !$secretKey) {
                Log::error('Pusher Beams configuration not found');
                return false;
            }

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$secretKey}",
                'Content-Type' => 'application/json',
            ])->post("https://{$instanceId}.pushnotifications.pusher.com/publish_api/v1/instances/{$instanceId}/publishes", [
                'interests' => ["user-{$user->id}"],
                'web' => [
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                        'deep_link' => $payload['deep_link'] ?? null,
                        'icon' => 'https://example.com/logo.png',
                    ],
                    'data' => $payload,
                ],
                'fcm' => [
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                    'data' => $payload,
                ],
                'apns' => [
                    'aps' => [
                        'alert' => [
                            'title' => $title,
                            'body' => $body,
                        ],
                    ],
                    'data' => $payload,
                ],
            ]);

            if ($response->successful()) {
                Log::info("Push notification sent successfully to user {$user->id}", [
                    'title' => $title,
                    'body' => $body
                ]);
                return true;
            } else {
                Log::error("Failed to send push notification to user {$user->id}", [
                    'response' => $response->body(),
                    'status' => $response->status()
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error("Push notification exception: {$e->getMessage()}", [
                'user_id' => $user->id,
                'title' => $title,
                'body' => $body
            ]);
            return false;
        }
    }

    /**
     * Send both email and push notification
     */
    public function sendEmailAndPush(User $user, string $subject, string $emailView, string $pushTitle, string $pushBody, array $emailData = [], array $pushPayload = []): array
    {
        $emailSent = $this->sendEmail($user, $subject, $emailView, $emailData);
        $pushSent = $this->sendPush($user, $pushTitle, $pushBody, $pushPayload);

        return [
            'email_sent' => $emailSent,
            'push_sent' => $pushSent,
        ];
    }

    /**
     * Send push notification to all users (for blog posts)
     */
    public function sendPushToAllUsers(string $title, string $body, array $payload = []): bool
    {
        try {
            $instanceId = config('services.pusher_beams.instance_id');
            $secretKey = config('services.pusher_beams.secret_key');
            
            if (!$instanceId || !$secretKey) {
                Log::error('Pusher Beams configuration not found');
                return false;
            }

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$secretKey}",
                'Content-Type' => 'application/json',
            ])->post("https://{$instanceId}.pushnotifications.pusher.com/publish_api/v1/instances/{$instanceId}/publishes", [
                'interests' => ['all-users'],
                'web' => [
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                        'deep_link' => $payload['deep_link'] ?? null,
                        'icon' => 'https://example.com/logo.png',
                    ],
                    'data' => $payload,
                ],
                'fcm' => [
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                    'data' => $payload,
                ],
                'apns' => [
                    'aps' => [
                        'alert' => [
                            'title' => $title,
                            'body' => $body,
                        ],
                    ],
                    'data' => $payload,
                ],
            ]);

            if ($response->successful()) {
                Log::info("Push notification sent to all users", [
                    'title' => $title,
                    'body' => $body
                ]);
                return true;
            } else {
                Log::error("Failed to send push notification to all users", [
                    'response' => $response->body(),
                    'status' => $response->status()
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error("Push notification to all users exception: {$e->getMessage()}", [
                'title' => $title,
                'body' => $body
            ]);
            return false;
        }
    }
}
