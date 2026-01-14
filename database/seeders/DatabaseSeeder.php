<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create additional test users beyond what factories create
        User::factory(10)->create();
        
        // Create admin and other records with comprehensive data
        $this->call([
            AdminSeeder::class,
            CategorySeeder::class,
            SellerProfileSeeder::class, 
            ProductSeeder::class,
            OrderSeeder::class,
            ReviewSeeder::class,
            BusinessProfileSeeder::class,
            BlogSeeder::class,
            FaqSeeder::class,
            WishlistSeeder::class,
            NotificationSeeder::class,
            AdminDashboardSeeder::class,
        ]);
        
        // Add extra data
        $this->createExtraTestData();
    }
    
    private function createExtraTestData()
    {
        $this->command->info('Adding extra test data...');
        
        // Create carts for users
        $users = \App\Models\User::all();
        $products = \App\Models\Product::where('status', 'approved')->get();
        
        if ($products->isNotEmpty()) {
            foreach ($users->take(5) as $user) {
                $cart = \App\Models\Cart::create(['user_id' => $user->id]);
                
                // Add random products to cart
                $cartProducts = $products->random(min(3, $products->count()));
                foreach ($cartProducts as $product) {
                    \App\Models\CartItem::create([
                        'cart_id' => $cart->id,
                        'product_id' => $product->id,
                        'quantity' => rand(1, 3),
                        'unit_price' => $product->price,
                    ]);
                }
            }
            $this->command->info('✓ Created carts with items');
        }
        
        // Create addresses
        foreach ($users->take(5) as $user) {
            \App\Models\Address::create([
                'user_id' => $user->id,
                'country' => 'Nigeria',
                'full_name' => $user->firstname . ' ' . $user->lastname,
                'phone_number' => $user->phone ?? '+2348012345678',
                'state' => 'Lagos',
                'city' => 'Lagos',
                'zip_code' => (string)rand(100000, 999999),
                'address' => rand(1, 999) . ' Test Street, Ikeja',
                'is_default' => true,
            ]);
        }
        $this->command->info('✓ Created addresses');
        
        // Create sustainability initiatives
        $admin = \App\Models\Admin::first();
        if ($admin) {
            \App\Models\SustainabilityInitiative::create([
                'created_by' => $admin->id,
                'title' => 'Zero Waste Fashion Initiative',
                'description' => 'Promoting sustainable fashion practices and reducing textile waste in the fashion industry.',
                'image_url' => 'https://via.placeholder.com/600x400',
                'category' => 'environmental',
                'status' => 'active',
                'target_amount' => 1000000,
                'current_amount' => 350000,
                'impact_metrics' => '200 artisans trained, 5000kg waste reduced',
                'start_date' => now()->subMonths(3),
                'end_date' => now()->addMonths(9),
                'partners' => json_encode(['NGO Partner', 'Government', 'Fashion Alliance']),
                'participant_count' => 150,
                'progress_notes' => 'Great progress towards our sustainability goals.',
            ]);
            $this->command->info('✓ Created sustainability initiatives');
        }
        
        // Create adverts
        if ($admin) {
            \App\Models\Advert::create([
                'created_by' => $admin->id,
                'title' => 'Summer Sale',
                'description' => 'Get 30% off on all summer collections! Limited time offer.',
                'image_url' => 'https://via.placeholder.com/800x400',
                'action_url' => 'https://ojaewa.com/sale',
                'position' => 'banner',
                'status' => 'active',
                'priority' => 1,
                'start_date' => now(),
                'end_date' => now()->addMonths(2),
            ]);
            $this->command->info('✓ Created adverts');
        }
        
        // Create subscriptions
        foreach ($users->take(3) as $index => $user) {
            $plans = ['basic', 'standard', 'premium'];
            $prices = [5000, 10000, 20000];
            
            \App\Models\Subscription::create([
                'user_id' => $user->id,
                'plan_name' => $plans[$index],
                'price' => $prices[$index],
                'status' => 'active',
                'starts_at' => now()->subMonth(),
                'expires_at' => now()->addMonths(11),
                'next_billing_date' => now()->addMonths(11),
                'payment_method' => 'paystack',
                'features' => json_encode(['feature1', 'feature2']),
            ]);
        }
        $this->command->info('✓ Created subscriptions');
        
        $this->command->info('✅ Extra test data completed!');
    }
}
