<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\SellerProfile;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all seller profiles
        $sellerProfiles = SellerProfile::all();
        
        if ($sellerProfiles->isEmpty()) {
            $this->command->warn('No seller profiles found. Skipping product creation.');
            return;
        }

        // Create 10 products distributed across sellers
        foreach ($sellerProfiles as $profile) {
            // Create 3-4 products per seller until we reach 10
            $count = min(rand(3, 4), 10 - Product::count());
            
            if ($count <= 0) break; // Stop if we've reached 10 products
            
            Product::factory()->count($count)->create([
                'seller_profile_id' => $profile->id,
                'status' => 'approved', // Set all to approved for easy testing
            ]);
            
            $this->command->info("Created {$count} products for seller {$profile->business_name}");
        }
    }
}
