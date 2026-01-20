<?php

namespace Database\Seeders;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Seeder;

class SubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'test@ojaewa.com')->first();
        if (!$user) {
            return;
        }

        Subscription::updateOrCreate(
            ['user_id' => $user->id, 'product_id' => 'ojaewa_pro'],
            [
                'plan_name' => 'ojaewa_pro',
                'price' => 0,
                'status' => 'active',
                'starts_at' => now(),
                'expires_at' => now()->addYear(),
                'next_billing_date' => now()->addYear(),
                'payment_method' => 'ios',
                'features' => null,
                'tier' => 'ojaewa_pro',
                'platform' => 'ios',
                'store_transaction_id' => 'seeded-test-transaction',
                'store_product_id' => 'ojaewa_pro',
                'receipt_data' => 'SEED_DATA',
                'is_auto_renewing' => true,
                'will_renew' => true,
                'environment' => 'production',
            ]
        );
    }
}
