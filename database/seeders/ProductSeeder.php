<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\SellerProfile;
use App\Models\Category;
use Database\Factories\ProductFactory;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sellerProfiles = SellerProfile::all();
        
        if ($sellerProfiles->isEmpty()) {
            $this->command->warn('No seller profiles found. Skipping product creation.');
            return;
        }

        // All leaf categories for products - organized by type
        $productCategories = [
            // TEXTILES - Women (3 products each)
            'textiles-women-dresses-gowns' => ['gender' => 'female', 'count' => 3],
            'textiles-women-two-piece-sets' => ['gender' => 'female', 'count' => 2],
            'textiles-women-wrappers-skirts' => ['gender' => 'female', 'count' => 2],
            'textiles-women-tops' => ['gender' => 'female', 'count' => 2],
            'textiles-women-headwear-accessories' => ['gender' => 'female', 'count' => 2],
            'textiles-women-outerwear' => ['gender' => 'female', 'count' => 1],
            'textiles-women-special-occasion' => ['gender' => 'female', 'count' => 2],
            
            // TEXTILES - Men
            'textiles-men-full-suits-gowns' => ['gender' => 'male', 'count' => 3],
            'textiles-men-two-piece-sets' => ['gender' => 'male', 'count' => 2],
            'textiles-men-shirts-tops' => ['gender' => 'male', 'count' => 3],
            'textiles-men-trousers' => ['gender' => 'male', 'count' => 2],
            'textiles-men-wrap-garments' => ['gender' => 'male', 'count' => 1],
            'textiles-men-outerwear' => ['gender' => 'male', 'count' => 1],
            'textiles-men-accessories' => ['gender' => 'male', 'count' => 2],
            
            // TEXTILES - Unisex
            'textiles-unisex-modern-casual-wear' => ['gender' => 'unisex', 'count' => 3],
            'textiles-unisex-capes-stoles' => ['gender' => 'unisex', 'count' => 1],
            'textiles-unisex-home-lounge-wear' => ['gender' => 'unisex', 'count' => 2],
            'textiles-unisex-accessories' => ['gender' => 'unisex', 'count' => 2],
            
            // SHOES & BAGS - Women
            'shoes-bags-women-slides-mules' => ['gender' => 'female', 'count' => 2, 'type' => 'shoes_bags'],
            'shoes-bags-women-block-heel-sandals-pumps' => ['gender' => 'female', 'count' => 2, 'type' => 'shoes_bags'],
            'shoes-bags-women-wedges' => ['gender' => 'female', 'count' => 1, 'type' => 'shoes_bags'],
            'shoes-bags-women-ballet-flats-loafers' => ['gender' => 'female', 'count' => 2, 'type' => 'shoes_bags'],
            'shoes-bags-women-evening-wedding-shoes' => ['gender' => 'female', 'count' => 2, 'type' => 'shoes_bags'],
            
            // SHOES & BAGS - Men
            'shoes-bags-men-african-print-slip-ons-loafers' => ['gender' => 'male', 'count' => 2, 'type' => 'shoes_bags'],
            'shoes-bags-men-leather-sandals' => ['gender' => 'male', 'count' => 2, 'type' => 'shoes_bags'],
            'shoes-bags-men-modern-masr' => ['gender' => 'male', 'count' => 1, 'type' => 'shoes_bags'],
            'shoes-bags-men-brogues-derbies' => ['gender' => 'male', 'count' => 2, 'type' => 'shoes_bags'],
            
            // AFRO BEAUTY PRODUCTS
            'afro-beauty-products-hair-care' => ['gender' => 'unisex', 'count' => 3, 'type' => 'afro_beauty_products'],
            'afro-beauty-products-skin-care' => ['gender' => 'unisex', 'count' => 3, 'type' => 'afro_beauty_products'],
            'afro-beauty-products-makeup-color-cosmetics' => ['gender' => 'female', 'count' => 2, 'type' => 'afro_beauty_products'],
            'afro-beauty-products-fragrance' => ['gender' => 'unisex', 'count' => 2, 'type' => 'afro_beauty_products'],
            'afro-beauty-products-mens-grooming' => ['gender' => 'male', 'count' => 2, 'type' => 'afro_beauty_products'],
            'afro-beauty-products-wellness-bathbody' => ['gender' => 'unisex', 'count' => 2, 'type' => 'afro_beauty_products'],
            'afro-beauty-products-childrens-afro-beauty' => ['gender' => 'unisex', 'count' => 1, 'type' => 'afro_beauty_products'],
            'afro-beauty-products-tools-accessories' => ['gender' => 'unisex', 'count' => 2, 'type' => 'afro_beauty_products'],
            
            // ART PRODUCTS
            'art-products-sculpture' => ['gender' => null, 'count' => 3, 'type' => 'art'],
            'art-products-painting' => ['gender' => null, 'count' => 3, 'type' => 'art'],
            'art-products-mask' => ['gender' => null, 'count' => 2, 'type' => 'art'],
            'art-products-mixed-media' => ['gender' => null, 'count' => 2, 'type' => 'art'],
            'art-products-installation' => ['gender' => null, 'count' => 1, 'type' => 'art'],
        ];

        // Get all categories by slug
        $categoriesBySlug = Category::whereIn('slug', array_keys($productCategories))
            ->get()
            ->keyBy('slug');

        $this->command->info("Found " . $categoriesBySlug->count() . " product categories");

        // Styles and tribes for textiles
        $africanStyles = ['Ankara', 'Kente', 'Aso Oke', 'Agbada', 'Dashiki', 'Kaftan', 'Gele', 'Adire', 'Boubou'];
        $africanTribes = ['Yoruba', 'Igbo', 'Hausa', 'Ashanti', 'Zulu', 'Masai', 'Xhosa', 'Fulani', 'Tuareg'];
        $fabrics = ['Ankara', 'Kente', 'Adinkra', 'Aso Oke', 'Akwa Ocha', 'George', 'Kitenge', 'Shweshwe', 'Raffia'];
        $sizes = ['S', 'M', 'L', 'XL', 'XXL'];
        $shoeSizes = ['36', '37', '38', '39', '40', '41', '42', '43', '44', '45'];

        $totalCreated = 0;
        $sellerIndex = 0;

        foreach ($productCategories as $slug => $config) {
            $category = $categoriesBySlug[$slug] ?? null;
            if (!$category) {
                $this->command->warn("Category not found: {$slug}");
                continue;
            }

            $count = $config['count'];
            $gender = $config['gender'];
            $categoryType = $config['type'] ?? $category->type;

            for ($i = 0; $i < $count; $i++) {
                // Distribute products across sellers
                $seller = $sellerProfiles[$sellerIndex % $sellerProfiles->count()];
                $sellerIndex++;

                // Get appropriate image and name for category
                $image = ProductFactory::getImageForCategory($slug);
                $name = ProductFactory::getNameForCategory($slug);
                
                // Add variation to prevent exact duplicates
                if ($i > 0) {
                    $name .= ' - Style ' . ($i + 1);
                }

                $productData = [
                    'seller_profile_id' => $seller->id,
                    'category_id' => $category->id,
                    'name' => $name,
                    'image' => $image,
                    'gender' => $gender,
                    'status' => 'approved',
                    'price' => fake()->randomFloat(2, 5000, 150000),
                    'description' => $this->getDescriptionForCategory($slug),
                    'processing_time_type' => fake()->randomElement(['normal', 'quick_quick']),
                    'processing_days' => fake()->numberBetween(2, 14),
                ];

                // Add textile-specific fields
                if (str_starts_with($slug, 'textiles-')) {
                    $productData['style'] = fake()->randomElement($africanStyles);
                    $productData['tribe'] = fake()->randomElement($africanTribes);
                    $productData['fabric_type'] = fake()->randomElement($fabrics);
                    $productData['size'] = fake()->randomElement($sizes);
                }
                // Shoes & bags - only size required
                elseif (str_starts_with($slug, 'shoes-bags-')) {
                    $productData['size'] = fake()->randomElement($shoeSizes);
                    $productData['fabric_type'] = fake()->optional(0.3)->randomElement($fabrics);
                    $productData['style'] = null;
                    $productData['tribe'] = null;
                }
                // Afro beauty & art - no apparel fields
                else {
                    $productData['size'] = null;
                    $productData['style'] = null;
                    $productData['tribe'] = null;
                    $productData['fabric_type'] = null;
                }

                Product::create($productData);
                $totalCreated++;
            }
        }

        // Create products for new Kids + Afro Beauty group leaves
        // Kids leaf categories based on new structure
        $kidsTextilesLeaves = Category::where('type', 'textiles')
            ->whereHas('parent', fn($q) => $q->whereIn('name', ['Female', 'Male'])
                ->whereHas('parent', fn($qq) => $qq->where('name', 'Kids')))
            ->get();

        $kidsShoesLeaves = Category::where('type', 'shoes_bags')
            ->whereHas('parent', fn($q) => $q->whereIn('name', ['Shoes', 'Bags'])
                ->whereHas('parent', fn($qq) => $qq->whereIn('name', ['Male', 'Female'])
                    ->whereHas('parent', fn($qqq) => $qqq->where('name', 'Kids'))))
            ->get();

        $kidsBeautyLeaves = Category::where('type', 'afro_beauty_products')
            ->whereHas('parent', fn($q) => $q->where('name', 'Kids'))
            ->get();

        $kidsLeafCategories = $kidsTextilesLeaves->merge($kidsShoesLeaves)->merge($kidsBeautyLeaves);

        foreach ($kidsLeafCategories as $category) {
            $seller = $sellerProfiles[$sellerIndex % $sellerProfiles->count()];
            $sellerIndex++;

            $leafName = strtolower($category->name);
            $gender = str_contains($leafName, 'female') ? 'female' : (str_contains($leafName, 'male') ? 'male' : 'unisex');

            $productData = [
                'seller_profile_id' => $seller->id,
                'category_id' => $category->id,
                'name' => ProductFactory::getNameForCategory($category->slug),
                'image' => ProductFactory::getImageForCategory($category->slug),
                'gender' => $gender,
                'status' => 'approved',
                'price' => fake()->randomFloat(2, 3000, 60000),
                'description' => $this->getDescriptionForCategory($category->slug),
                'processing_time_type' => fake()->randomElement(['normal', 'quick_quick']),
                'processing_days' => fake()->numberBetween(2, 14),
            ];

            if ($category->type === 'textiles') {
                $productData['style'] = fake()->randomElement($africanStyles);
                $productData['tribe'] = fake()->randomElement($africanTribes);
                $productData['fabric_type'] = fake()->randomElement($fabrics);
                $productData['size'] = fake()->randomElement($sizes);
            } elseif ($category->type === 'shoes_bags') {
                $productData['size'] = fake()->randomElement($shoeSizes);
                $productData['fabric_type'] = fake()->optional(0.3)->randomElement($fabrics);
                $productData['style'] = null;
                $productData['tribe'] = null;
            } else {
                $productData['size'] = null;
                $productData['style'] = null;
                $productData['tribe'] = null;
                $productData['fabric_type'] = null;
            }

            Product::create($productData);
            $totalCreated++;
        }

        $afroBeautyGroupLeaves = Category::where('type', 'afro_beauty_products')
            ->whereHas('parent', fn($q) => $q->whereIn('name', ['Women', 'Men']))
            ->get();

        foreach ($afroBeautyGroupLeaves as $category) {
            $seller = $sellerProfiles[$sellerIndex % $sellerProfiles->count()];
            $sellerIndex++;

            Product::create([
                'seller_profile_id' => $seller->id,
                'category_id' => $category->id,
                'name' => ProductFactory::getNameForCategory($category->slug),
                'image' => ProductFactory::getImageForCategory($category->slug),
                'gender' => strtolower($category->parent?->name) === 'men' ? 'male' : 'female',
                'status' => 'approved',
                'price' => fake()->randomFloat(2, 3000, 60000),
                'description' => $this->getDescriptionForCategory($category->slug),
                'processing_time_type' => fake()->randomElement(['normal', 'quick_quick']),
                'processing_days' => fake()->numberBetween(2, 14),
                'size' => null,
                'style' => null,
                'tribe' => null,
                'fabric_type' => null,
            ]);
            $totalCreated++;
        }

        // Also create some pending and rejected products for testing
        $testSeller = $sellerProfiles->first();
        $firstCategory = $categoriesBySlug->first();
        
        if ($testSeller && $firstCategory) {
            // Create pending products
            for ($i = 0; $i < 3; $i++) {
                Product::factory()->pending()->create([
                    'seller_profile_id' => $testSeller->id,
                    'category_id' => $firstCategory->id,
                    'name' => 'Pending Product ' . ($i + 1),
                ]);
            }
            
            // Create rejected products
            for ($i = 0; $i < 2; $i++) {
                Product::factory()->rejected()->create([
                    'seller_profile_id' => $testSeller->id,
                    'category_id' => $firstCategory->id,
                    'name' => 'Rejected Product ' . ($i + 1),
                ]);
            }
            $totalCreated += 5;
        }

        $this->command->info("✓ Created {$totalCreated} products across all categories");
    }

    /**
     * Get a description for a category
     */
    private function getDescriptionForCategory(string $slug): string
    {
        $descriptions = [
            'textiles' => "Beautifully handcrafted African textile featuring traditional patterns and vibrant colors. Made by skilled artisans using time-honored techniques passed down through generations. Perfect for special occasions and everyday elegance.",
            'shoes-bags' => "Expertly crafted African footwear combining traditional design with modern comfort. Made from high-quality materials by skilled craftspeople. A perfect blend of style and heritage.",
            'afro-beauty' => "Premium African beauty product made with natural ingredients sourced from across the continent. Formulated to nourish and enhance your natural beauty. Free from harsh chemicals.",
            'art' => "Authentic African art piece created by talented local artists. Each piece tells a story of African heritage, culture, and creativity. A unique addition to any collection.",
        ];

        if (str_starts_with($slug, 'textiles-')) {
            return $descriptions['textiles'];
        } elseif (str_starts_with($slug, 'shoes-bags-')) {
            return $descriptions['shoes-bags'];
        } elseif (str_starts_with($slug, 'afro-beauty-')) {
            return $descriptions['afro-beauty'];
        } elseif (str_starts_with($slug, 'art-')) {
            return $descriptions['art'];
        }
        
        return fake()->paragraphs(2, true);
    }
}
