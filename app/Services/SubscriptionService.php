<?php

namespace App\Services;

use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SubscriptionService
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Create a new subscription for a user.
     */
    public function createSubscription(User $user, array $data): Subscription
    {
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'plan_name' => $data['plan_name'],
            'price' => $data['price'],
            'status' => 'active',
            'starts_at' => now(),
            'expires_at' => now()->addMonth(),
            'next_billing_date' => now()->addMonth(),
            'payment_method' => $data['payment_method'] ?? null,
            'features' => $data['features'] ?? [],
        ]);

        // Send subscription confirmation notification
        $this->notificationService->sendEmailAndPush(
            $user,
            'Subscription Activated - Oja Ewa',
            'subscription_status',
            'Subscription Activated!',
            "Your {$subscription->plan_name} subscription has been activated successfully.",
            ['subscription' => $subscription, 'status' => 'renewed'],
            [
                'subscription_id' => $subscription->id,
                'status' => 'active',
                'deep_link' => '/subscription/manage'
            ]
        );

        return $subscription;
    }

    /**
     * Renew a subscription.
     */
    public function renewSubscription(Subscription $subscription): bool
    {
        try {
            $subscription->update([
                'status' => 'active',
                'expires_at' => $subscription->expires_at->addMonth(),
                'next_billing_date' => $subscription->next_billing_date->addMonth(),
            ]);

            // Send renewal success notification
            $this->notificationService->sendEmailAndPush(
                $subscription->user,
                'Subscription Renewed - Oja Ewa',
                'subscription_status',
                'Subscription Renewed!',
                "Your {$subscription->plan_name} subscription has been renewed successfully.",
                ['subscription' => $subscription, 'status' => 'renewed'],
                [
                    'subscription_id' => $subscription->id,
                    'status' => 'renewed',
                    'deep_link' => '/subscription/manage'
                ]
            );

            return true;
        } catch (\Exception $e) {
            \Log::error('Subscription renewal failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Handle failed subscription payment.
     */
    public function handlePaymentFailure(Subscription $subscription, string $reason = null): void
    {
        $subscription->update([
            'status' => 'payment_failed',
        ]);

        // Send payment failure notification
        $this->notificationService->sendEmailAndPush(
            $subscription->user,
            'Subscription Payment Failed - Oja Ewa',
            'subscription_status',
            'Payment Failed',
            "We couldn't process your payment for the {$subscription->plan_name} subscription.",
            ['subscription' => $subscription, 'status' => 'payment_failed', 'reason' => $reason],
            [
                'subscription_id' => $subscription->id,
                'status' => 'payment_failed',
                'deep_link' => '/subscription/payment'
            ]
        );
    }

    /**
     * Cancel a subscription.
     */
    public function cancelSubscription(Subscription $subscription): bool
    {
        try {
            $subscription->update([
                'status' => 'cancelled',
            ]);

            // Send cancellation notification
            $this->notificationService->sendEmailAndPush(
                $subscription->user,
                'Subscription Cancelled - Oja Ewa',
                'subscription_status',
                'Subscription Cancelled',
                "Your {$subscription->plan_name} subscription has been cancelled.",
                ['subscription' => $subscription, 'status' => 'cancelled'],
                [
                    'subscription_id' => $subscription->id,
                    'status' => 'cancelled',
                    'deep_link' => '/subscription/manage'
                ]
            );

            return true;
        } catch (\Exception $e) {
            \Log::error('Subscription cancellation failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Expire subscriptions that have passed their expiration date.
     */
    public function expireSubscriptions(): int
    {
        $expiredSubscriptions = Subscription::where('status', 'active')
            ->where('expires_at', '<', now())
            ->get();

        $count = 0;
        foreach ($expiredSubscriptions as $subscription) {
            $subscription->update(['status' => 'expired']);
            
            // Send expiration notification
            $this->notificationService->sendEmailAndPush(
                $subscription->user,
                'Subscription Expired - Oja Ewa',
                'subscription_status',
                'Subscription Expired',
                "Your {$subscription->plan_name} subscription has expired.",
                ['subscription' => $subscription, 'status' => 'expired'],
                [
                    'subscription_id' => $subscription->id,
                    'status' => 'expired',
                    'deep_link' => '/subscription/renew'
                ]
            );
            
            $count++;
        }

        return $count;
    }
}
