<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\SellerProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Category-specific product images from Unsplash
     */
    public static array $categoryImages = [
        // Textiles - Women
        'textiles-women-dresses-gowns' => [
            'https://images.unsplash.com/photo-1590735213920-68192a487bc2?w=500&h=500&fit=crop',
            'https://images.unsplash.com/photo-1614252235316-8c857d38b5f4?w=500&h=500&fit=crop',
            'https://images.unsplash.com/photo-1618354691373-d851c5c3a990?w=500&h=500&fit=crop',
        ],
        'textiles-women-two-piece-sets' => [
            'https://images.unsplash.com/photo-1594938298603-c8148c4dae35?w=500&h=500&fit=crop',
            'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?w=500&h=500&fit=crop',
        ],
        'textiles-women-wrappers-skirts' => [
            'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?w=500&h=500&fit=crop',
            'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?w=500&h=500&fit=crop',
        ],
        'textiles-women-tops' => [
            'https://images.unsplash.com/photo-1564257631407-4deb1f99d992?w=500&h=500&fit=crop',
            'https://images.unsplash.com/photo-1551163943-3f6a855d1153?w=500&h=500&fit=crop',
        ],
        'textiles-women-headwear-accessories' => [
            'https://images.unsplash.com/photo-1590735213408-9d0bd91578a1?w=500&h=500&fit=crop',
            'https://images.unsplash.com/photo-1611923134239-b9be5816e23c?w=500&h=500&fit=crop',
        ],
        // Textiles - Men
        'textiles-men-full-suits-gowns' => [
            'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?w=500&h=500&fit=crop',
            'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=500&h=500&fit=crop',
        ],
        'textiles-men-two-piece-sets' => [
            'https://images.unsplash.com/photo-1617137968427-85924c800a22?w=500&h=500&fit=crop',
            'https://images.unsplash.com/photo-1594938298603-c8148c4dae35?w=500&h=500&fit=crop',
        ],
        'textiles-men-shirts-tops' => [
            'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?w=500&h=500&fit=crop',
            'https://images.unsplash.com/photo-1589310243389-96a5483213a8?w=500&h=500&fit=crop',
        ],
        'textiles-men-trousers' => [
            'https://images.unsplash.com/photo-1473966968600-fa801b869a1a?w=500&h=500&fit=crop',
            'https://images.unsplash.com/photo-1624378439575-d8705ad7ae80?w=500&h=500&fit=crop',
        ],
        // Textiles - Unisex
        'textiles-unisex-modern-casual-wear' => [
            'https://images.unsplash.com/photo-1620799140408-edc6dcb6d633?w=500&h=500&fit=crop',
            'https://images.unsplash.com/photo-1556905055-8f358a7a47b2?w=500&h=500&fit=crop',
        ],
        // Shoes & Bags - Women
        'shoes-bags-women-slides-mules' => [
            'https://images.unsplash.com/photo-1543163521-1bf539c55dd2?w=500&h=500&fit=crop',
            'https://images.unsplash.com/photo-1603808033192-082d6919d3e1?w=500&h=500&fit=crop',
        ],
        'shoes-bags-women-block-heel-sandals-pumps' => [
            'https://images.unsplash.com/photo-1596703263926-eb0762ee17e4?w=500&h=500&fit=crop',
            'https://images.unsplash.com/photo-1515347619252-60a4bf4fff4f?w=500&h=500&fit=crop',
        ],
        'shoes-bags-women-evening-wedding-shoes' => [
            'https://images.unsplash.com/photo-1519415943484-9fa1873496d4?w=500&h=500&fit=crop',
            'https://images.unsplash.com/photo-1543163521-1bf539c55dd2?w=500&h=500&fit=crop',
        ],
        // Shoes & Bags - Men
        'shoes-bags-men-leather-sandals' => [
            'https://images.unsplash.com/photo-1603808033192-082d6919d3e1?w=500&h=500&fit=crop',
            'https://images.unsplash.com/photo-1531310197839-ccf54634509e?w=500&h=500&fit=crop',
        ],
        'shoes-bags-men-brogues-derbies' => [
            'https://images.unsplash.com/photo-1614252235316-8c857d38b5f4?w=500&h=500&fit=crop',
            'https://images.unsplash.com/photo-1533867617858-e7b97e060509?w=500&h=500&fit=crop',
        ],
        // Afro Beauty Products
        'afro-beauty-products-hair-care' => [
            'https://images.unsplash.com/photo-1526947425960-945c6e72858f?w=500&h=500&fit=crop',
            'https://images.unsplash.com/photo-1608248597279-f99d160bfcbc?w=500&h=500&fit=crop',
        ],
        'afro-beauty-products-skin-care' => [
            'https://images.unsplash.com/photo-1570194065650-d99fb4b38b15?w=500&h=500&fit=crop',
            'https://images.unsplash.com/photo-1556228720-195a672e8a03?w=500&h=500&fit=crop',
        ],
        'afro-beauty-products-makeup-color-cosmetics' => [
            'https://images.unsplash.com/photo-1512496015851-a90fb38ba796?w=500&h=500&fit=crop',
            'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?w=500&h=500&fit=crop',
        ],
        'afro-beauty-products-fragrance' => [
            'https://images.unsplash.com/photo-1541643600914-78b084683601?w=500&h=500&fit=crop',
            'https://images.unsplash.com/photo-1595425964071-2c1ecb10b52d?w=500&h=500&fit=crop',
        ],
        // Art Products
        'art-products-sculpture' => [
            'https://images.unsplash.com/photo-1544967082-d9d25d867d66?w=500&h=500&fit=crop',
            'https://images.unsplash.com/photo-1578926288207-a90a5366759d?w=500&h=500&fit=crop',
        ],
        'art-products-painting' => [
            'https://images.unsplash.com/photo-1579783902614-a3fb3927b6a5?w=500&h=500&fit=crop',
            'https://images.unsplash.com/photo-1547826039-bfc35e0f1ea8?w=500&h=500&fit=crop',
        ],
        'art-products-mask' => [
            'https://images.unsplash.com/photo-1582555172866-f73bb12a2ab3?w=500&h=500&fit=crop',
            'https://images.unsplash.com/photo-1594736797933-d0501ba2fe65?w=500&h=500&fit=crop',
        ],
        'art-products-mixed-media' => [
            'https://images.unsplash.com/photo-1561214115-f2f134cc4912?w=500&h=500&fit=crop',
            'https://images.unsplash.com/photo-1513364776144-60967b0f800f?w=500&h=500&fit=crop',
        ],
        // Default fallback
        'default' => [
            'https://images.unsplash.com/photo-1485968579580-b6d095142e6e?w=500&h=500&fit=crop',
            'https://images.unsplash.com/photo-1558171813-4c088753af8f?w=500&h=500&fit=crop',
            'https://images.unsplash.com/photo-1590735213920-68192a487bc2?w=500&h=500&fit=crop',
        ],
    ];

    /**
     * Category-specific product names
     */
    public static array $categoryProductNames = [
        'textiles-women-dresses-gowns' => ['Elegant Ankara Gown', 'Traditional Iro & Buba Set', 'Modern African Print Maxi Dress', 'Kente Evening Dress', 'Adire Boho Dress'],
        'textiles-women-two-piece-sets' => ['Ankara Skirt & Top Set', 'Kente Blouse & Wrapper', 'Modern African Co-ord Set', 'Traditional Two-Piece Ensemble'],
        'textiles-women-tops' => ['Ankara Peplum Top', 'Off-Shoulder African Print Blouse', 'Dashiki Crop Top', 'Kente Wrap Top'],
        'textiles-men-full-suits-gowns' => ['Traditional Agbada Set', 'Senator Style Kaftan', 'Grand Boubou', 'Aso Oke Agbada'],
        'textiles-men-shirts-tops' => ['African Print Shirt', 'Dashiki Top', 'Ankara Short Sleeve Shirt', 'Embroidered Kaftan Top'],
        'textiles-men-trousers' => ['Ankara Print Trousers', 'African Pattern Pants', 'Traditional Sokoto', 'Modern African Chinos'],
        'textiles-unisex-modern-casual-wear' => ['African Print T-Shirt', 'Casual Dashiki', 'Modern Ankara Jacket', 'African Print Hoodie'],
        'shoes-bags-women-slides-mules' => ['African Print Slides', 'Ankara Mules', 'Beaded Flat Sandals', 'Kente Pattern Slippers'],
        'shoes-bags-women-evening-wedding-shoes' => ['Gold African Wedding Heels', 'Ankara Stilettos', 'Traditional Bridal Shoes', 'Embellished Evening Pumps'],
        'shoes-bags-men-leather-sandals' => ['Handcrafted Leather Sandals', 'Traditional African Sandals', 'Beaded Men Slides', 'Artisan Leather Slippers'],
        'shoes-bags-men-brogues-derbies' => ['African Print Brogues', 'Ankara Derby Shoes', 'Traditional Leather Brogues', 'Modern African Oxfords'],
        'afro-beauty-products-hair-care' => ['Shea Butter Hair Cream', 'African Black Soap Shampoo', 'Natural Hair Growth Oil', 'Chebe Hair Treatment'],
        'afro-beauty-products-skin-care' => ['Raw African Shea Butter', 'Moringa Body Oil', 'Baobab Face Serum', 'Black Soap Facial Cleanser'],
        'art-products-sculpture' => ['Wooden African Sculpture', 'Bronze Tribal Figure', 'Ebony Carving', 'Traditional Mask Sculpture'],
        'art-products-painting' => ['African Village Scene', 'Tribal Portrait Painting', 'Modern African Art', 'Traditional Dance Painting'],
        'art-products-mask' => ['Ceremonial African Mask', 'Decorative Tribal Mask', 'Hand-Carved Wooden Mask', 'Traditional Festival Mask'],
        'default' => ['African Handcrafted Item', 'Traditional Artisan Product', 'Authentic African Creation'],
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $africanStyles = ['Ankara', 'Kente', 'Aso Oke', 'Agbada', 'Dashiki', 'Kaftan', 'Gele', 'Adire', 'Boubou'];
        $africanTribes = ['Yoruba', 'Igbo', 'Hausa', 'Ashanti', 'Zulu', 'Masai', 'Xhosa', 'Fulani', 'Tuareg'];
        $fabrics = ['Ankara','Kente','Adinkra','Aso Oke','Akwa Ocha','George','Kente Prestige','Faso Dan Fani','Korhogo','Kitenge','Leso','Shuka','Liputa','Raffia','Shweshwe','Lishu','IsiShweshwe','Cotton Voile','Woolen','Melhfa'];
        $sizes = ['S', 'M', 'L', 'XL', 'XXL', '36', '38', '40', '42', '44'];
        $genders = ['male', 'female', 'unisex'];
        $status = ['pending', 'approved', 'rejected'];
        
        return [
            'seller_profile_id' => SellerProfile::factory(),
            'name' => fake()->words(3, true) . ' ' . $this->faker->randomElement($africanStyles),
            'gender' => $this->faker->randomElement($genders),
            'style' => $this->faker->randomElement($africanStyles),
            'tribe' => $this->faker->randomElement($africanTribes),
            'fabric_type' => $this->faker->randomElement($fabrics),
            'description' => fake()->paragraphs(2, true),
            'image' => $this->faker->randomElement(self::$categoryImages['default']),
            'size' => $this->faker->randomElement($sizes),
            'processing_time_type' => $this->faker->randomElement(['normal', 'quick_quick']),
            'processing_days' => $this->faker->numberBetween(2, 14),
            'price' => $this->faker->randomFloat(2, 5000, 150000),
            'status' => $this->faker->randomElement($status),
        ];
    }
    
    /**
     * Get image for a specific category slug
     */
    public static function getImageForCategory(string $categorySlug): string
    {
        $images = self::$categoryImages[$categorySlug] ?? self::$categoryImages['default'];
        return $images[array_rand($images)];
    }

    /**
     * Get product name for a specific category slug
     */
    public static function getNameForCategory(string $categorySlug): string
    {
        $names = self::$categoryProductNames[$categorySlug] ?? self::$categoryProductNames['default'];
        return $names[array_rand($names)];
    }
    
    /**
     * Indicate that the product is approved.
     */
    public function approved(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'approved',
            ];
        });
    }

    /**
     * Indicate that the product is pending.
     */
    public function pending(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'pending',
            ];
        });
    }

    /**
     * Indicate that the product is rejected.
     */
    public function rejected(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'rejected',
            ];
        });
    }
}
