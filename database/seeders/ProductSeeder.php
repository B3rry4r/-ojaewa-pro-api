<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\SellerProfile;
use App\Models\Category;
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

        // Deterministic category assignment so app navigation shows different products
        $targetSlugs = [
            // TEXTILES
            'textiles-women-dresses-gowns',
            'textiles-women-tops',
            'textiles-men-shirts-tops',
            'textiles-men-trousers',
            'textiles-unisex-modern-casual-wear',

            // SHOES & BAGS
            'shoes-bags-women-slides-mules',
            'shoes-bags-women-evening-wedding-shoes',
            'shoes-bags-men-leather-sandals',
            'shoes-bags-men-brogues-derbies',

            // AFRO BEAUTY (Products)
            'afro-beauty-products-hair-care',
            'afro-beauty-products-skin-care',

            // ART (Products)
            'art-products-sculpture',
            'art-products-painting',
        ];

        $categoriesBySlug = Category::whereIn('slug', $targetSlugs)
            ->get()
            ->keyBy('slug');

        // Fallback to any product-catalog leaf categories if some slugs are missing
        $fallbackLeafIds = Category::whereIn('type', ['textiles', 'shoes_bags', 'afro_beauty_products', 'art'])
            ->whereDoesntHave('children')
            ->pluck('id')
            ->toArray();

        if ($categoriesBySlug->isEmpty() && empty($fallbackLeafIds)) {
            $this->command->warn('No market categories found. Ensure CategorySeeder runs before ProductSeeder.');
            return;
        }

        // For each seller, create products across the specific categories
        foreach ($sellerProfiles as $profile) {
            $created = 0;

            foreach ($targetSlugs as $slug) {
                $categoryId = $categoriesBySlug[$slug]->id ?? ($fallbackLeafIds[array_rand($fallbackLeafIds)] ?? null);
                if (!$categoryId) {
                    continue;
                }

                // Create 1 product per target category per seller (keeps categories distinct)
                Product::factory()->create([
                    'seller_profile_id' => $profile->id,
                    'category_id' => $categoryId,
                    'status' => 'approved',
                ]);
                $created++;
            }

            $this->command->info("Created {$created} categorized products for seller {$profile->business_name}");
        }
    }
}
