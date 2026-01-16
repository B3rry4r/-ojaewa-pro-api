<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First, create specific notifications for the test user (test@ojaewa.com)
        $testUser = User::where('email', 'test@ojaewa.com')->first();
        
        if ($testUser) {
            $this->createTestUserNotifications($testUser);
        }
        
        $users = User::take(5)->get();
        
        if ($users->isEmpty()) {
            echo "No users found. Skipping notification seeding.\n";
            return;
        }

        $notificationTemplates = [
            // Order-related notifications
            [
                'type' => 'push',
                'event' => 'order_placed',
                'title' => 'Order Confirmed',
                'message' => 'Your order #{order_id} has been confirmed and is being processed.',
                'payload' => ['order_id' => 'ORD-001', 'amount' => 150.00],
            ],
            [
                'type' => 'email',
                'event' => 'order_shipped',
                'title' => 'Order Shipped',
                'message' => 'Great news! Your order #{order_id} has been shipped and is on its way.',
                'payload' => ['order_id' => 'ORD-002', 'tracking_number' => 'TRK123456'],
            ],
            [
                'type' => 'push',
                'event' => 'order_delivered',
                'title' => 'Order Delivered',
                'message' => 'Your order #{order_id} has been delivered. Don\'t forget to rate your purchase!',
                'payload' => ['order_id' => 'ORD-003'],
            ],
            
            // Product-related notifications
            [
                'type' => 'push',
                'event' => 'new_product',
                'title' => 'New Products Available',
                'message' => 'Check out the latest products from your favorite sellers.',
                'payload' => ['category' => 'market', 'count' => 5],
            ],
            [
                'type' => 'push',
                'event' => 'product_back_in_stock',
                'title' => 'Item Back in Stock',
                'message' => 'The {product_name} you wishlisted is now back in stock!',
                'payload' => ['product_name' => 'Traditional Kente Dress', 'product_id' => 123],
            ],
            [
                'type' => 'email',
                'event' => 'price_drop',
                'title' => 'Price Drop Alert',
                'message' => 'The price of {product_name} has dropped by 20%! Get it now before it\'s gone.',
                'payload' => ['product_name' => 'Ankara Print Blazer', 'old_price' => 100, 'new_price' => 80],
            ],

            // Seller/Business notifications
            [
                'type' => 'email',
                'event' => 'seller_approved',
                'title' => 'Seller Application Approved',
                'message' => 'Congratulations! Your seller application has been approved. You can now start listing products.',
                'payload' => ['approval_date' => now()->toDateString()],
            ],
            [
                'type' => 'push',
                'event' => 'business_approved',
                'title' => 'Business Profile Approved',
                'message' => 'Your {category} business profile has been approved and is now live.',
                'payload' => ['category' => 'afro_beauty_services', 'business_name' => 'Glam Afro Beauty Studio'],
            ],

            // Promotional notifications
            [
                'type' => 'push',
                'event' => 'discount_sale',
                'title' => 'Special Discount Available',
                'message' => 'Enjoy up to 30% off on selected items this weekend!',
                'payload' => ['discount_percent' => 30, 'valid_until' => Carbon::now()->addDays(3)->toDateString()],
            ],
            [
                'type' => 'email',
                'event' => 'seasonal_sale',
                'title' => 'End of Season Sale',
                'message' => 'Don\'t miss our biggest sale of the year! Up to 50% off on all categories.',
                'payload' => ['max_discount' => 50],
            ],

            // Blog and content notifications
            [
                'type' => 'push',
                'event' => 'new_blog_post',
                'title' => 'New Blog Post',
                'message' => 'Read our latest blog post: "Celebrating African Fashion: A Guide to Traditional and Modern Styles"',
                'payload' => ['blog_title' => 'Celebrating African Fashion', 'blog_id' => 1],
            ],

            // Review and feedback notifications
            [
                'type' => 'push',
                'event' => 'review_reminder',
                'title' => 'Review Your Purchase',
                'message' => 'How was your recent purchase? Your review helps other customers and sellers.',
                'payload' => ['order_id' => 'ORD-004'],
            ],
            [
                'type' => 'email',
                'event' => 'review_received',
                'title' => 'New Review on Your Product',
                'message' => 'You received a new review on {product_name}. Check it out!',
                'payload' => ['product_name' => 'Handwoven Basket', 'rating' => 5],
            ],

            // Account and security notifications
            [
                'type' => 'email',
                'event' => 'password_changed',
                'title' => 'Password Changed',
                'message' => 'Your password has been successfully changed. If you didn\'t make this change, please contact support.',
                'payload' => ['change_date' => now()->toDateTimeString()],
            ],
            [
                'type' => 'push',
                'event' => 'login_new_device',
                'title' => 'New Device Login',
                'message' => 'Your account was accessed from a new device. Was this you?',
                'payload' => ['device' => 'iPhone 12', 'location' => 'Lagos, Nigeria'],
            ],

            // Community and engagement
            [
                'type' => 'push',
                'event' => 'welcome',
                'title' => 'Welcome to Oja Ewa Pro!',
                'message' => 'Welcome to our community! Explore authentic African products and services.',
                'payload' => ['welcome_bonus' => 10],
            ],
            [
                'type' => 'push',
                'event' => 'milestone',
                'title' => 'Milestone Achieved',
                'message' => 'Congratulations! You\'ve completed your 5th purchase. Enjoy exclusive member benefits.',
                'payload' => ['milestone_type' => 'purchase_count', 'count' => 5],
            ],
        ];

        $notifications = [];
        
        foreach ($users as $user) {
            // Create 3-8 notifications per user
            $userNotificationCount = rand(3, 8);
            $userTemplates = array_slice($notificationTemplates, 0, $userNotificationCount);
            
            foreach ($userTemplates as $template) {
                $isRead = rand(1, 100) <= 30; // 30% chance of being read
                
                $notifications[] = [
                    'user_id' => $user->id,
                    'type' => $template['type'],
                    'event' => $template['event'],
                    'title' => $template['title'],
                    'message' => $template['message'],
                    'payload' => json_encode($template['payload']),
                    'read_at' => $isRead ? Carbon::now()->subHours(rand(1, 72)) : null,
                    'created_at' => Carbon::now()->subDays(rand(1, 30))->subHours(rand(1, 23)),
                    'updated_at' => Carbon::now()->subDays(rand(1, 30))->subHours(rand(1, 23)),
                ];
            }
        }

        // Insert notifications in chunks for better performance
        $chunks = array_chunk($notifications, 50);
        $totalCreated = 0;
        
        foreach ($chunks as $chunk) {
            foreach ($chunk as $notification) {
                Notification::create($notification);
                $totalCreated++;
            }
        }

        echo "Created {$totalCreated} notifications for " . $users->count() . " users.\n";
        
        // Show some statistics
        $readCount = collect($notifications)->where('read_at', '!=', null)->count();
        $unreadCount = $totalCreated - $readCount;
        
        echo "Notifications breakdown: {$readCount} read, {$unreadCount} unread.\n";
        
        $eventTypes = collect($notifications)->groupBy('event')->map->count();
        echo "Event types distribution: " . $eventTypes->toJson() . "\n";
    }

    /**
     * Create specific notifications for the test user to use for client-side testing
     */
    private function createTestUserNotifications(User $user): void
    {
        $testNotifications = [
            // Business Approved - UNREAD
            [
                'user_id' => $user->id,
                'type' => 'push',
                'event' => 'business_approved',
                'title' => 'Business Approved!',
                'message' => 'Congratulations! Your business profile "African Fashion Hub" has been approved.',
                'payload' => json_encode([
                    'business_id' => 1,
                    'status' => 'approved',
                    'deep_link' => '/business/1'
                ]),
                'read_at' => null,
                'created_at' => Carbon::now()->subMinutes(5),
                'updated_at' => Carbon::now()->subMinutes(5),
            ],
            // Seller Approved - UNREAD
            [
                'user_id' => $user->id,
                'type' => 'push',
                'event' => 'seller_approved',
                'title' => 'Seller Profile Approved!',
                'message' => 'Congratulations! Your seller profile has been approved. You can now start listing products!',
                'payload' => json_encode([
                    'seller_id' => 1,
                    'status' => 'approved',
                    'deep_link' => '/seller/profile'
                ]),
                'read_at' => null,
                'created_at' => Carbon::now()->subMinutes(10),
                'updated_at' => Carbon::now()->subMinutes(10),
            ],
            // Product Approved - UNREAD
            [
                'user_id' => $user->id,
                'type' => 'push',
                'event' => 'product_approved',
                'title' => 'Product Approved!',
                'message' => 'Great news! Your product "Traditional Agbada" has been approved and is now live!',
                'payload' => json_encode([
                    'product_id' => 1,
                    'status' => 'approved',
                    'deep_link' => '/products/1'
                ]),
                'read_at' => null,
                'created_at' => Carbon::now()->subMinutes(15),
                'updated_at' => Carbon::now()->subMinutes(15),
            ],
            // Product Rejected - UNREAD
            [
                'user_id' => $user->id,
                'type' => 'push',
                'event' => 'product_approved',
                'title' => 'Product Needs Update',
                'message' => 'Your product "Modern Kaftan" needs some updates before approval. Please review the feedback.',
                'payload' => json_encode([
                    'product_id' => 2,
                    'status' => 'rejected',
                    'deep_link' => '/products/2'
                ]),
                'read_at' => null,
                'created_at' => Carbon::now()->subMinutes(20),
                'updated_at' => Carbon::now()->subMinutes(20),
            ],
            // Order Status Updated - UNREAD
            [
                'user_id' => $user->id,
                'type' => 'push',
                'event' => 'order_status_updated',
                'title' => 'Order Shipped!',
                'message' => 'Your order #ORD-12345 has been shipped and is on its way!',
                'payload' => json_encode([
                    'order_id' => 1,
                    'status' => 'shipped',
                    'deep_link' => '/orders/1'
                ]),
                'read_at' => null,
                'created_at' => Carbon::now()->subMinutes(30),
                'updated_at' => Carbon::now()->subMinutes(30),
            ],
            // Order Delivered - READ (older notification)
            [
                'user_id' => $user->id,
                'type' => 'push',
                'event' => 'order_status_updated',
                'title' => 'Order Delivered!',
                'message' => 'Your order #ORD-12344 has been delivered. Enjoy your purchase!',
                'payload' => json_encode([
                    'order_id' => 2,
                    'status' => 'delivered',
                    'deep_link' => '/orders/2'
                ]),
                'read_at' => Carbon::now()->subHours(2),
                'created_at' => Carbon::now()->subHours(3),
                'updated_at' => Carbon::now()->subHours(2),
            ],
            // Business Deactivated - UNREAD
            [
                'user_id' => $user->id,
                'type' => 'push',
                'event' => 'business_approved',
                'title' => 'Business Profile Needs Update',
                'message' => 'Your business profile "Quick Fashion Store" needs some updates before approval.',
                'payload' => json_encode([
                    'business_id' => 2,
                    'status' => 'deactivated',
                    'deep_link' => '/business/2'
                ]),
                'read_at' => null,
                'created_at' => Carbon::now()->subMinutes(45),
                'updated_at' => Carbon::now()->subMinutes(45),
            ],
            // Seller Rejected - UNREAD
            [
                'user_id' => $user->id,
                'type' => 'push',
                'event' => 'seller_approved',
                'title' => 'Seller Profile Needs Update',
                'message' => 'Your seller profile needs some updates. Please provide valid business registration documents.',
                'payload' => json_encode([
                    'seller_id' => 2,
                    'status' => 'rejected',
                    'deep_link' => '/seller/profile'
                ]),
                'read_at' => null,
                'created_at' => Carbon::now()->subHours(1),
                'updated_at' => Carbon::now()->subHours(1),
            ],
        ];

        foreach ($testNotifications as $notification) {
            Notification::create($notification);
        }

        echo "✓ Created " . count($testNotifications) . " test notifications for test@ojaewa.com\n";
    }
}