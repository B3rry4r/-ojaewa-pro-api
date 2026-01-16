<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('');
        $this->command->info('🌱 Starting OjaEwa Database Seeding...');
        $this->command->info('=====================================');

        // 1. Create FIXED test user with known credentials (always first)
        $testUser = User::create([
            'firstname' => 'Test',
            'lastname' => 'User',
            'email' => 'test@ojaewa.com',
            'password' => Hash::make('password123'),
            'phone' => '+2348012345678',
            'email_verified_at' => now(),
        ]);
        $this->command->info('✓ Created test user: test@ojaewa.com / password123');
        
        // 2. Create additional buyer users (these will place orders)
        $buyerNames = [
            ['Adaeze', 'Okonkwo', 'Lagos State'],
            ['Chidi', 'Eze', 'Abuja'],
            ['Folake', 'Adeyemi', 'Oyo State'],
            ['Emeka', 'Nnamdi', 'Rivers State'],
            ['Aisha', 'Mohammed', 'Kano State'],
            ['Ngozi', 'Obi', 'Enugu State'],
            ['Tunde', 'Bakare', 'Lagos State'],
            ['Zainab', 'Ibrahim', 'Kaduna State'],
        ];

        foreach ($buyerNames as $index => $name) {
            User::create([
                'firstname' => $name[0],
                'lastname' => $name[1],
                'email' => strtolower($name[0]) . '.' . strtolower($name[1]) . '@example.com',
                'password' => Hash::make('password123'),
                'phone' => '+234' . rand(8010000000, 9099999999),
                'email_verified_at' => now(),
            ]);
        }
        $this->command->info('✓ Created 8 buyer accounts');

        // 3. Create seller users (these will have seller profiles and products)
        $sellerNames = [
            ['Olumide', 'Fashions', 'Lagos State'],
            ['Amara', 'Designs', 'Abuja'],
            ['Kwame', 'Artistry', 'Rivers State'],
        ];

        foreach ($sellerNames as $name) {
            User::create([
                'firstname' => $name[0],
                'lastname' => $name[1],
                'email' => strtolower($name[0]) . '@seller.com',
                'password' => Hash::make('password123'),
                'phone' => '+234' . rand(8010000000, 9099999999),
                'email_verified_at' => now(),
            ]);
        }
        $this->command->info('✓ Created 3 seller accounts');

        $this->command->info('');
        $this->command->info('📦 Seeding Core Data...');
        $this->command->info('-----------------------');

        // Run seeders in correct dependency order
        $this->call([
            // Foundation data
            AdminSeeder::class,
            CategorySeeder::class,
            
            // Seller infrastructure (must come before products)
            SellerProfileSeeder::class,
            
            // Products (depends on sellers and categories)
            ProductSeeder::class,
            
            // Orders (depends on users and products)
            OrderSeeder::class,
            
            // Reviews (depends on orders and products)
            ReviewSeeder::class,
            
            // Business profiles (service providers)
            BusinessProfileSeeder::class,
            
            // Content
            BlogSeeder::class,
            FaqSeeder::class,
            
            // User engagement
            WishlistSeeder::class,
            
            // Notifications (depends on orders, products, businesses - must be last)
            NotificationSeeder::class,
            
            // Admin dashboard data
            AdminDashboardSeeder::class,
            
            // Sustainability
            SustainabilityInitiativeSeeder::class,
        ]);
        
        $this->command->info('');
        $this->command->info('🎁 Adding Extra Test Data...');
        $this->command->info('----------------------------');
        
        // Add extra linked data
        $this->createCartsAndAddresses();
        $this->createAdvertsAndSubscriptions();
        
        $this->command->info('');
        $this->command->info('=====================================');
        $this->command->info('✅ Database seeding complete!');
        $this->command->info('');
        $this->printTestCredentials();
    }
    
    /**
     * Create carts and addresses for users
     */
    private function createCartsAndAddresses(): void
    {
        $users = User::all();
        $products = \App\Models\Product::where('status', 'approved')->get();
        
        // Nigerian addresses for realism
        $addresses = [
            ['state' => 'Lagos State', 'city' => 'Lagos', 'address' => '15 Admiralty Way, Lekki Phase 1'],
            ['state' => 'Lagos State', 'city' => 'Lagos', 'address' => '42 Allen Avenue, Ikeja'],
            ['state' => 'FCT', 'city' => 'Abuja', 'address' => '8 Aminu Kano Crescent, Wuse 2'],
            ['state' => 'Rivers State', 'city' => 'Port Harcourt', 'address' => '23 Trans Amadi Road'],
            ['state' => 'Oyo State', 'city' => 'Ibadan', 'address' => '56 Ring Road, Ibadan'],
        ];
        
        // Create addresses for all users
        foreach ($users as $index => $user) {
            $addr = $addresses[$index % count($addresses)];
            \App\Models\Address::create([
                'user_id' => $user->id,
                'country' => 'Nigeria',
                'full_name' => $user->firstname . ' ' . $user->lastname,
                'phone_number' => $user->phone ?? '+234' . rand(8010000000, 9099999999),
                'state' => $addr['state'],
                'city' => $addr['city'],
                'zip_code' => (string)rand(100000, 999999),
                'address' => $addr['address'],
                'is_default' => true,
            ]);
        }
        $this->command->info('✓ Created addresses for all users');
        
        // Create carts with items for some users
        if ($products->isNotEmpty()) {
            foreach ($users->take(6) as $user) {
                $cart = \App\Models\Cart::create(['user_id' => $user->id]);
                
                // Add 2-4 random products to cart
                $cartProducts = $products->random(min(rand(2, 4), $products->count()));
                foreach ($cartProducts as $product) {
                    \App\Models\CartItem::create([
                        'cart_id' => $cart->id,
                        'product_id' => $product->id,
                        'quantity' => rand(1, 2),
                        'unit_price' => $product->price,
                    ]);
                }
            }
            $this->command->info('✓ Created carts with items for 6 users');
        }
    }
    
    /**
     * Create adverts and subscriptions
     */
    private function createAdvertsAndSubscriptions(): void
    {
        $admin = \App\Models\Admin::first();
        
        if ($admin) {
            // Create multiple adverts
            $adverts = [
                [
                    'title' => 'New Year Fashion Sale',
                    'description' => 'Start the year in style! Up to 40% off on all African textiles.',
                    'image_url' => 'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=800&h=400&fit=crop',
                    'action_url' => '/products/browse?category=textiles',
                    'position' => 'banner',
                    'priority' => 1,
                ],
                [
                    'title' => 'Support Local Artisans',
                    'description' => 'Every purchase supports African craftspeople and their families.',
                    'image_url' => 'https://images.unsplash.com/photo-1509631179647-0177331693ae?w=800&h=400&fit=crop',
                    'action_url' => '/about/artisans',
                    'position' => 'sidebar',
                    'priority' => 2,
                ],
                [
                    'title' => 'Free Delivery Weekend',
                    'description' => 'Free delivery on orders over ₦50,000 this weekend only!',
                    'image_url' => 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=800&h=400&fit=crop',
                    'action_url' => '/promotions/free-delivery',
                    'position' => 'popup',
                    'priority' => 3,
                ],
            ];

            foreach ($adverts as $advert) {
                \App\Models\Advert::create([
                    'created_by' => $admin->id,
                    'title' => $advert['title'],
                    'description' => $advert['description'],
                    'image_url' => $advert['image_url'],
                    'action_url' => $advert['action_url'],
                    'position' => $advert['position'],
                    'status' => 'active',
                    'priority' => $advert['priority'],
                    'start_date' => now(),
                    'end_date' => now()->addMonths(2),
                ]);
            }
            $this->command->info('✓ Created 3 adverts');
        }
        
        // Create subscriptions for some users
        $users = User::take(5)->get();
        $plans = [
            ['name' => 'basic', 'price' => 5000],
            ['name' => 'standard', 'price' => 10000],
            ['name' => 'premium', 'price' => 25000],
        ];
        
        foreach ($users->take(3) as $index => $user) {
            $plan = $plans[$index % count($plans)];
            \App\Models\Subscription::create([
                'user_id' => $user->id,
                'plan_name' => $plan['name'],
                'price' => $plan['price'],
                'status' => 'active',
                'starts_at' => now()->subMonth(),
                'expires_at' => now()->addMonths(11),
                'next_billing_date' => now()->addMonths(11),
                'payment_method' => 'paystack',
                'features' => ['unlimited_listings', 'priority_support', 'analytics'],
            ]);
        }
        $this->command->info('✓ Created 3 subscriptions');
    }

    /**
     * Print test credentials for easy reference
     */
    private function printTestCredentials(): void
    {
        $this->command->info('📋 TEST CREDENTIALS');
        $this->command->info('-------------------');
        $this->command->info('Main Test User (Buyer + Seller):');
        $this->command->info('  Email: test@ojaewa.com');
        $this->command->info('  Password: password123');
        $this->command->info('');
        $this->command->info('Additional Sellers:');
        $this->command->info('  olumide@seller.com / password123');
        $this->command->info('  amara@seller.com / password123');
        $this->command->info('  kwame@seller.com / password123');
        $this->command->info('');
        $this->command->info('Admin Panel:');
        $this->command->info('  Check AdminSeeder for admin credentials');
        $this->command->info('');
    }
}
