<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\BusinessProfile;
use App\Models\SellerProfile;
use App\Models\Review;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates REAL notifications linked to actual database records.
     */
    public function run(): void
    {
        $users = User::all();
        
        if ($users->isEmpty()) {
            $this->command->warn('No users found. Skipping notification seeding.');
            return;
        }

        $totalCreated = 0;

        // Get the test user for comprehensive notifications
        $testUser = User::where('email', 'test@ojaewa.com')->first();
        
        if ($testUser) {
            $totalCreated += $this->createTestUserNotifications($testUser);
        }

        // Create notifications for all users based on their actual activities
        foreach ($users as $user) {
            $totalCreated += $this->createOrderNotifications($user);
            $totalCreated += $this->createSellerNotifications($user);
            $totalCreated += $this->createBusinessNotifications($user);
        }

        // Create some general promotional notifications for random users
        $totalCreated += $this->createPromotionalNotifications($users);

        $this->command->info("✓ Created {$totalCreated} notifications linked to real activities");
    }

    /**
     * Create comprehensive test notifications for the main test user
     */
    private function createTestUserNotifications(User $user): int
    {
        $count = 0;

        // Get user's actual orders
        $orders = Order::where('user_id', $user->id)->get();
        
        foreach ($orders as $order) {
            // Order placed notification
            Notification::create([
                'user_id' => $user->id,
                'type' => 'push',
                'event' => 'order_placed',
                'title' => 'Order Confirmed!',
                'message' => "Your order {$order->order_number} has been confirmed. Total: ₦" . number_format($order->total_price, 2),
                'payload' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'total' => $order->total_price,
                    'status' => 'pending',
                    'deep_link' => "/orders/{$order->id}"
                ],
                'read_at' => $order->status !== 'pending' ? $order->created_at->addHours(1) : null,
                'created_at' => $order->created_at,
                'updated_at' => $order->created_at,
            ]);
            $count++;

            // Status update notifications based on current status
            if (in_array($order->status, ['processing', 'shipped', 'delivered', 'cancelled'])) {
                $statusMessages = [
                    'processing' => 'is being processed by the seller',
                    'shipped' => 'has been shipped! Track: ' . ($order->tracking_number ?? 'N/A'),
                    'delivered' => 'has been delivered. Enjoy your purchase!',
                    'cancelled' => 'has been cancelled.',
                ];

                Notification::create([
                    'user_id' => $user->id,
                    'type' => 'push',
                    'event' => 'order_status_updated',
                    'title' => 'Order ' . ucfirst($order->status),
                    'message' => "Your order {$order->order_number} {$statusMessages[$order->status]}",
                    'payload' => [
                        'order_id' => $order->id,
                        'order_number' => $order->order_number,
                        'status' => $order->status,
                        'tracking_number' => $order->tracking_number,
                        'deep_link' => "/orders/{$order->id}"
                    ],
                    'read_at' => $order->status === 'delivered' ? $order->updated_at->addHours(2) : null,
                    'created_at' => $order->updated_at,
                    'updated_at' => $order->updated_at,
                ]);
                $count++;
            }
        }

        // Get user's seller profile notifications
        $sellerProfile = SellerProfile::where('user_id', $user->id)->first();
        if ($sellerProfile) {
            Notification::create([
                'user_id' => $user->id,
                'type' => 'email',
                'event' => 'seller_approved',
                'title' => 'Seller Profile Approved!',
                'message' => "Congratulations! Your seller profile \"{$sellerProfile->business_name}\" has been approved. Start listing your products now!",
                'payload' => [
                    'seller_id' => $sellerProfile->id,
                    'business_name' => $sellerProfile->business_name,
                    'status' => $sellerProfile->registration_status,
                    'deep_link' => '/seller/dashboard'
                ],
                'read_at' => Carbon::now()->subDays(5),
                'created_at' => Carbon::now()->subDays(7),
                'updated_at' => Carbon::now()->subDays(5),
            ]);
            $count++;

            // Notifications for seller's products
            $products = Product::where('seller_profile_id', $sellerProfile->id)->get();
            foreach ($products->take(5) as $product) {
                if ($product->status === 'approved') {
                    Notification::create([
                        'user_id' => $user->id,
                        'type' => 'push',
                        'event' => 'product_approved',
                        'title' => 'Product Approved!',
                        'message' => "Your product \"{$product->name}\" has been approved and is now live on the marketplace!",
                        'payload' => [
                            'product_id' => $product->id,
                            'product_name' => $product->name,
                            'status' => 'approved',
                            'deep_link' => "/products/{$product->id}"
                        ],
                        'read_at' => Carbon::now()->subDays(rand(1, 3)),
                        'created_at' => $product->created_at->addHours(rand(1, 24)),
                        'updated_at' => $product->created_at->addHours(rand(1, 24)),
                    ]);
                    $count++;
                } elseif ($product->status === 'rejected') {
                    Notification::create([
                        'user_id' => $user->id,
                        'type' => 'push',
                        'event' => 'product_rejected',
                        'title' => 'Product Needs Updates',
                        'message' => "Your product \"{$product->name}\" needs some updates before it can be approved. Please review and resubmit.",
                        'payload' => [
                            'product_id' => $product->id,
                            'product_name' => $product->name,
                            'status' => 'rejected',
                            'deep_link' => "/seller/products/{$product->id}/edit"
                        ],
                        'read_at' => null,
                        'created_at' => $product->created_at->addHours(rand(1, 24)),
                        'updated_at' => $product->created_at->addHours(rand(1, 24)),
                    ]);
                    $count++;
                } elseif ($product->status === 'pending') {
                    Notification::create([
                        'user_id' => $user->id,
                        'type' => 'push',
                        'event' => 'product_submitted',
                        'title' => 'Product Submitted for Review',
                        'message' => "Your product \"{$product->name}\" has been submitted and is pending admin review.",
                        'payload' => [
                            'product_id' => $product->id,
                            'product_name' => $product->name,
                            'status' => 'pending',
                            'deep_link' => "/seller/products/{$product->id}"
                        ],
                        'read_at' => null,
                        'created_at' => $product->created_at,
                        'updated_at' => $product->created_at,
                    ]);
                    $count++;
                }
            }

            // New order notifications for seller
            $sellerOrders = Order::whereHas('orderItems.product', function ($q) use ($sellerProfile) {
                $q->where('seller_profile_id', $sellerProfile->id);
            })->get();

            foreach ($sellerOrders->take(5) as $order) {
                Notification::create([
                    'user_id' => $user->id,
                    'type' => 'push',
                    'event' => 'new_order_received',
                    'title' => 'New Order Received!',
                    'message' => "You have a new order {$order->order_number}! Check your seller dashboard to process it.",
                    'payload' => [
                        'order_id' => $order->id,
                        'order_number' => $order->order_number,
                        'customer_name' => $order->shipping_name,
                        'total' => $order->total_price,
                        'deep_link' => "/seller/orders/{$order->id}"
                    ],
                    'read_at' => in_array($order->status, ['processing', 'shipped', 'delivered']) 
                        ? $order->created_at->addHours(rand(1, 4)) 
                        : null,
                    'created_at' => $order->created_at,
                    'updated_at' => $order->created_at,
                ]);
                $count++;
            }
        }

        // Business profile notifications
        $businessProfile = BusinessProfile::where('user_id', $user->id)->first();
        if ($businessProfile) {
            Notification::create([
                'user_id' => $user->id,
                'type' => 'email',
                'event' => 'business_approved',
                'title' => 'Business Profile Approved!',
                'message' => "Your business \"{$businessProfile->business_name}\" is now live! Customers can find you in the {$businessProfile->category} directory.",
                'payload' => [
                    'business_id' => $businessProfile->id,
                    'business_name' => $businessProfile->business_name,
                    'category' => $businessProfile->category,
                    'status' => $businessProfile->store_status,
                    'deep_link' => "/business/{$businessProfile->id}"
                ],
                'read_at' => Carbon::now()->subDays(3),
                'created_at' => Carbon::now()->subDays(6),
                'updated_at' => Carbon::now()->subDays(3),
            ]);
            $count++;
        }

        // Welcome notification
        Notification::create([
            'user_id' => $user->id,
            'type' => 'push',
            'event' => 'welcome',
            'title' => 'Welcome to OjaEwa!',
            'message' => 'Welcome to the marketplace for authentic African products and services. Start exploring now!',
            'payload' => [
                'deep_link' => '/home'
            ],
            'read_at' => Carbon::now()->subDays(10),
            'created_at' => $user->created_at,
            'updated_at' => $user->created_at,
        ]);
        $count++;

        $this->command->info("✓ Created {$count} notifications for test@ojaewa.com");
        return $count;
    }

    /**
     * Create order-related notifications for a user
     */
    private function createOrderNotifications(User $user): int
    {
        $count = 0;
        $orders = Order::where('user_id', $user->id)->get();

        foreach ($orders as $order) {
            // Only create if not already handled (for test user)
            if ($user->email === 'test@ojaewa.com') {
                continue;
            }

            // Order confirmation
            Notification::create([
                'user_id' => $user->id,
                'type' => 'push',
                'event' => 'order_placed',
                'title' => 'Order Confirmed',
                'message' => "Order {$order->order_number} confirmed. Total: ₦" . number_format($order->total_price, 2),
                'payload' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'deep_link' => "/orders/{$order->id}"
                ],
                'read_at' => rand(0, 1) ? Carbon::now()->subHours(rand(1, 48)) : null,
                'created_at' => $order->created_at,
                'updated_at' => $order->created_at,
            ]);
            $count++;

            // Delivery notification for delivered orders
            if ($order->status === 'delivered') {
                Notification::create([
                    'user_id' => $user->id,
                    'type' => 'push',
                    'event' => 'order_delivered',
                    'title' => 'Order Delivered!',
                    'message' => "Your order {$order->order_number} has been delivered. Leave a review!",
                    'payload' => [
                        'order_id' => $order->id,
                        'order_number' => $order->order_number,
                        'deep_link' => "/orders/{$order->id}/review"
                    ],
                    'read_at' => rand(0, 1) ? $order->delivered_at?->addHours(rand(1, 24)) : null,
                    'created_at' => $order->delivered_at ?? $order->updated_at,
                    'updated_at' => $order->delivered_at ?? $order->updated_at,
                ]);
                $count++;
            }
        }

        return $count;
    }

    /**
     * Create seller-related notifications
     */
    private function createSellerNotifications(User $user): int
    {
        $count = 0;
        
        if ($user->email === 'test@ojaewa.com') {
            return 0; // Already handled
        }

        $sellerProfile = SellerProfile::where('user_id', $user->id)->first();
        
        if ($sellerProfile && $sellerProfile->registration_status === 'approved') {
            Notification::create([
                'user_id' => $user->id,
                'type' => 'email',
                'event' => 'seller_approved',
                'title' => 'Seller Application Approved',
                'message' => "Your seller profile \"{$sellerProfile->business_name}\" is approved!",
                'payload' => [
                    'seller_id' => $sellerProfile->id,
                    'business_name' => $sellerProfile->business_name,
                    'deep_link' => '/seller/dashboard'
                ],
                'read_at' => Carbon::now()->subDays(rand(1, 7)),
                'created_at' => Carbon::now()->subDays(rand(7, 14)),
                'updated_at' => Carbon::now()->subDays(rand(1, 7)),
            ]);
            $count++;
        }

        return $count;
    }

    /**
     * Create business-related notifications
     */
    private function createBusinessNotifications(User $user): int
    {
        $count = 0;
        
        if ($user->email === 'test@ojaewa.com') {
            return 0; // Already handled
        }

        $businessProfile = BusinessProfile::where('user_id', $user->id)->first();
        
        if ($businessProfile && $businessProfile->store_status === 'approved') {
            Notification::create([
                'user_id' => $user->id,
                'type' => 'email',
                'event' => 'business_approved',
                'title' => 'Business Profile Live!',
                'message' => "Your business \"{$businessProfile->business_name}\" is now visible to customers.",
                'payload' => [
                    'business_id' => $businessProfile->id,
                    'business_name' => $businessProfile->business_name,
                    'category' => $businessProfile->category,
                    'deep_link' => "/business/{$businessProfile->id}"
                ],
                'read_at' => Carbon::now()->subDays(rand(1, 5)),
                'created_at' => Carbon::now()->subDays(rand(5, 10)),
                'updated_at' => Carbon::now()->subDays(rand(1, 5)),
            ]);
            $count++;
        }

        return $count;
    }

    /**
     * Create promotional notifications for random users
     */
    private function createPromotionalNotifications($users): int
    {
        $count = 0;
        $promos = [
            [
                'event' => 'seasonal_sale',
                'title' => 'Weekend Flash Sale!',
                'message' => 'Get up to 30% off on selected African textiles this weekend only!',
            ],
            [
                'event' => 'new_arrivals',
                'title' => 'New Arrivals Alert',
                'message' => 'Check out the latest African fashion pieces just added to our marketplace.',
            ],
            [
                'event' => 'featured_seller',
                'title' => 'Featured Sellers This Week',
                'message' => 'Discover amazing products from our top-rated African artisans.',
            ],
        ];

        // Give promotional notifications to random users
        foreach ($users->random(min(5, $users->count())) as $user) {
            $promo = $promos[array_rand($promos)];
            
            Notification::create([
                'user_id' => $user->id,
                'type' => 'push',
                'event' => $promo['event'],
                'title' => $promo['title'],
                'message' => $promo['message'],
                'payload' => [
                    'deep_link' => '/promotions'
                ],
                'read_at' => rand(0, 1) ? Carbon::now()->subHours(rand(1, 72)) : null,
                'created_at' => Carbon::now()->subDays(rand(1, 7)),
                'updated_at' => Carbon::now()->subDays(rand(1, 7)),
            ]);
            $count++;
        }

        return $count;
    }
}
