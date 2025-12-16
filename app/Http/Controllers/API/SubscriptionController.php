<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\BusinessProfile;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
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

    /**
     * Get subscription features based on type
     */
    private function getSubscriptionFeatures(string $subscriptionType): array
    {
        return match ($subscriptionType) {
            'basic' => [
                'max_products' => 50,
                'max_photos_per_product' => 5,
                'analytics_access' => false,
                'priority_support' => false,
                'advanced_promotion' => false
            ],
            'premium' => [
                'max_products' => 200,
                'max_photos_per_product' => 10,
                'analytics_access' => true,
                'priority_support' => true,
                'advanced_promotion' => true
            ],
            'enterprise' => [
                'max_products' => 1000,
                'max_photos_per_product' => 20,
                'analytics_access' => true,
                'priority_support' => true,
                'advanced_promotion' => true,
                'custom_branding' => true,
                'api_access' => true
            ]
        };
    }
}