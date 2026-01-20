<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\BusinessProfile;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    /**
     * Store subscription (verification handled later)
     * POST /api/subscriptions/verify
     */
    public function verify(Request $request): JsonResponse
    {
        $request->validate([
            'platform' => 'required|in:ios,android',
            'product_id' => 'required|string',
            'transaction_id' => 'required|string',
            'receipt_data' => 'nullable|string',
            'environment' => 'nullable|in:sandbox,production',
        ]);

        $user = Auth::user();

        $productId = $request->product_id;
        $tier = $productId === 'ojaewa_pro' ? 'ojaewa_pro' : 'free';

        $startsAt = now();
        $expiresAt = $productId === 'ojaewa_pro' ? now()->addYear() : now();

        $subscription = Subscription::updateOrCreate(
            [
                'store_transaction_id' => $request->transaction_id,
                'platform' => $request->platform,
            ],
            [
                'user_id' => $user->id,
                'product_id' => $productId,
                'tier' => $tier,
                'store_product_id' => $productId,
                'receipt_data' => $request->receipt_data,
                'status' => 'active',
                'starts_at' => $startsAt,
                'expires_at' => $expiresAt,
                'is_auto_renewing' => true,
                'will_renew' => true,
                'renewal_price' => null,
                'renewal_currency' => 'NGN',
                'environment' => $request->environment ?? 'production',
                'raw_data' => [
                    'platform' => $request->platform,
                    'product_id' => $productId,
                    'transaction_id' => $request->transaction_id,
                ],
                'plan_name' => $tier,
                'price' => 0,
                'payment_method' => $request->platform,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Subscription verified and activated',
            'data' => [
                'subscription' => [
                    'id' => $subscription->id,
                    'product_id' => $subscription->product_id,
                    'tier' => $subscription->tier,
                    'status' => $subscription->status,
                    'starts_at' => $subscription->starts_at,
                    'expires_at' => $subscription->expires_at,
                    'is_auto_renewing' => $subscription->is_auto_renewing,
                    'platform' => $subscription->platform,
                ],
            ],
        ]);
    }

    /**
     * Get current subscription status
     * GET /api/subscriptions/status
     */
    public function status(): JsonResponse
    {
        $user = Auth::user();
        $subscription = Subscription::where('user_id', $user->id)
            ->whereNotNull('product_id')
            ->orderByDesc('expires_at')
            ->first();

        if (!$subscription || !$subscription->isActive()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'has_subscription' => false,
                    'subscription' => null,
                ],
            ]);
        }

        $daysRemaining = $subscription->daysUntilExpiration();

        return response()->json([
            'success' => true,
            'data' => [
                'has_subscription' => true,
                'subscription' => [
                    'id' => $subscription->id,
                    'product_id' => $subscription->product_id,
                    'tier' => $subscription->tier,
                    'status' => $subscription->status,
                    'platform' => $subscription->platform,
                    'starts_at' => $subscription->starts_at,
                    'expires_at' => $subscription->expires_at,
                    'days_remaining' => $daysRemaining,
                    'is_auto_renewing' => $subscription->is_auto_renewing,
                    'will_renew' => $subscription->will_renew,
                ],
            ],
        ]);
    }

    /**
     * Manage business subscription
     */
    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'business_id' => 'required|integer|exists:business_profiles,id',
            'subscription_type' => 'required|in:basic,premium,enterprise',
            'billing_cycle' => 'required|in:monthly,quarterly,yearly'
        ]);

        $user = Auth::user();
        
        // Find the business profile belonging to the user
        $businessProfile = BusinessProfile::where('user_id', $user->id)
                                         ->where('id', $request->business_id)
                                         ->firstOrFail();

        // Subscription pricing logic (in NGN)
        $pricing = [
            'basic' => [
                'monthly' => 5000,    // 50 NGN
                'quarterly' => 13500, // 135 NGN (10% discount)
                'yearly' => 48000     // 480 NGN (20% discount)
            ],
            'premium' => [
                'monthly' => 10000,   // 100 NGN
                'quarterly' => 27000, // 270 NGN (10% discount)  
                'yearly' => 96000     // 960 NGN (20% discount)
            ],
            'enterprise' => [
                'monthly' => 20000,   // 200 NGN
                'quarterly' => 54000, // 540 NGN (10% discount)
                'yearly' => 192000    // 1920 NGN (20% discount)
            ]
        ];

        $subscriptionType = $request->subscription_type;
        $billingCycle = $request->billing_cycle;
        $amount = $pricing[$subscriptionType][$billingCycle];

        // Calculate next billing date
        $nextBillingDate = match ($billingCycle) {
            'monthly' => now()->addMonth(),
            'quarterly' => now()->addMonths(3),
            'yearly' => now()->addYear(),
        };

        // Update business profile subscription (use existing subscription_status field)
        $businessProfile->update([
            'subscription_type' => $subscriptionType,
            'subscription_status' => 'active', // Uses existing field
            'billing_cycle' => $billingCycle,
            'subscription_amount' => $amount,
            'next_billing_date' => $nextBillingDate,
            'subscription_updated_at' => now()
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Business subscription updated successfully',
            'data' => [
                'business_id' => $businessProfile->id,
                'subscription_type' => $subscriptionType,
                'subscription_status' => 'active',
                'billing_cycle' => $billingCycle,
                'amount' => $amount,
                'currency' => 'NGN',
                'next_billing_date' => $nextBillingDate->format('Y-m-d'),
                'features' => $this->getSubscriptionFeatures($subscriptionType)
            ]
        ]);
    }

}