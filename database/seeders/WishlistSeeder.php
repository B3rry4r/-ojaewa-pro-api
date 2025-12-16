<?php

namespace Database\Seeders;

use App\Models\Wishlist;
use App\Models\User;
use App\Models\Product;
use App\Models\BusinessProfile;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WishlistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::take(5)->get();
        $products = Product::where('status', 'approved')->take(8)->get();
        $businessProfiles = BusinessProfile::where('store_status', 'approved')->take(4)->get();

        if ($users->isEmpty() || ($products->isEmpty() && $businessProfiles->isEmpty())) {
            echo "No users, products, or business profiles found. Skipping wishlist seeding.\n";
            return;
        }

        $wishlistItems = [];

        foreach ($users as $user) {
            // Add 2-3 products to each user's wishlist
            $userProducts = $products->random(min(3, $products->count()));
            foreach ($userProducts as $product) {
                $wishlistItems[] = [
                    'user_id' => $user->id,
                    'wishlistable_id' => $product->id,
                    'wishlistable_type' => 'App\Models\Product',
                    'created_at' => now()->subDays(rand(1, 30)),
                    'updated_at' => now()->subDays(rand(1, 30)),
                ];
            }

            // Add 1-2 business services to each user's wishlist
            if ($businessProfiles->isNotEmpty()) {
                $userBusinesses = $businessProfiles->random(min(2, $businessProfiles->count()));
                foreach ($userBusinesses as $business) {
                    $wishlistItems[] = [
                        'user_id' => $user->id,
                        'wishlistable_id' => $business->id,
                        'wishlistable_type' => 'App\Models\BusinessProfile',
                        'created_at' => now()->subDays(rand(1, 20)),
                        'updated_at' => now()->subDays(rand(1, 20)),
                    ];
                }
            }
        }

        // Remove duplicates (same user + wishlistable combination)
        $uniqueWishlistItems = [];
        $seen = [];
        
        foreach ($wishlistItems as $item) {
            $key = $item['user_id'] . '_' . $item['wishlistable_id'] . '_' . $item['wishlistable_type'];
            if (!isset($seen[$key])) {
                $uniqueWishlistItems[] = $item;
                $seen[$key] = true;
            }
        }

        // Insert wishlist items
        foreach ($uniqueWishlistItems as $item) {
            try {
                Wishlist::create($item);
            } catch (\Exception $e) {
                // Skip duplicates that might exist due to unique constraint
                continue;
            }
        }

        echo "Created " . count($uniqueWishlistItems) . " wishlist items for " . $users->count() . " users.\n";
    }
}