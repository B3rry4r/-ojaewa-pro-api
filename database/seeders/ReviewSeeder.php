<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all approved products
        $products = Product::where('status', 'approved')->get();
        
        if ($products->isEmpty()) {
            $this->command->warn('No approved products found. Skipping review creation.');
            return;
        }

        // Get all users
        $users = User::all();
        
        if ($users->isEmpty()) {
            $this->command->warn('No users found. Skipping review creation.');
            return;
        }
        
        // Get completed orders
        $orders = Order::where('status', 'paid')->take(3)->get();
        
        // Create reviews for about 70% of products
        $reviewProducts = $products->random(max(1, intval($products->count() * 0.7)));
        
        foreach ($reviewProducts as $product) {
            // Create 1-3 reviews per product
            $reviewCount = rand(1, 3);
            
            for ($i = 0; $i < $reviewCount; $i++) {
                $user = $users->random();
                
                Review::factory()->create([
                    'user_id' => $user->id,
                    'reviewable_id' => $product->id,
                    'reviewable_type' => Product::class,
                ]);
            }
            
            $this->command->info("Created {$reviewCount} reviews for product {$product->name}");
        }
        
        // Also create reviews for some orders
        foreach ($orders as $order) {
            // 50% chance of order having a review
            if (rand(0, 1) === 1) {
                Review::factory()->create([
                    'user_id' => $order->user_id,
                    'reviewable_id' => $order->id,
                    'reviewable_type' => Order::class,
                ]);
                
                $this->command->info("Created review for order #{$order->id}");
            }
        }
    }
}
