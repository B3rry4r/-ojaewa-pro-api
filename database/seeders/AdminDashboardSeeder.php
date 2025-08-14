<?php

namespace Database\Seeders;

use App\Models\BusinessProfile;
use App\Models\Product;
use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminDashboardSeeder extends Seeder
{
    /**
     * Run the database seeds to create pending data for admin dashboard testing.
     */
    public function run(): void
    {
        // Create users for testing
        $users = User::factory(3)->create();
        
        // Create pending seller profiles (for admin approval)
        foreach ($users as $index => $user) {
            // Skip if user already has a seller profile
            if (!$user->sellerProfile()->exists()) {
                $sellerProfile = SellerProfile::factory()->create([
                    'user_id' => $user->id,
                    'registration_status' => 'pending',
                    'business_name' => "Test Pending Seller {$index}",
                ]);
                
                // Create pending products for this seller
                if ($index === 0) {
                    $products = Product::factory(3)->create([
                        'seller_profile_id' => $sellerProfile->id,
                        'status' => 'pending'
                    ]);
                    
                    foreach ($products as $pIndex => $product) {
                        $product->update([
                            'name' => "Pending Product {$pIndex}",
                            'description' => "Product awaiting admin approval."
                        ]);
                    }
                }
            }
        }
        
        // Create pending business profiles for each category
        $categories = ['beauty', 'brand', 'school', 'music'];
        
        foreach ($categories as $index => $category) {
            // Get or create a user for this business
            $user = User::inRandomOrder()->first() ?? User::factory()->create();
            
            // Create a pending business profile
            BusinessProfile::factory()->create([
                'user_id' => $user->id,
                'category' => $category,
                'store_status' => 'pending',
                'business_name' => "Pending {$category} Business",
                'business_description' => "A {$category} business awaiting approval by admin."
            ]);
        }
        
        $this->command->info('Created testing data for admin dashboard:');
        $this->command->info('- ' . SellerProfile::where('registration_status', 'pending')->count() . ' pending seller profiles');
        $this->command->info('- ' . Product::where('status', 'pending')->count() . ' pending products');
        $this->command->info('- ' . BusinessProfile::where('store_status', 'pending')->count() . ' pending business profiles');
    }
}
