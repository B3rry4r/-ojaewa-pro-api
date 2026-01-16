<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * CategorySeeder - Final Locked Model
 * 
 * LANDING BOX → ENTITY MAPPING:
 * =====================================
 * 
 * PRODUCT CATALOGS (return Products):
 * - textiles → Products (3 levels: Group → Leaf)
 * - shoes_bags → Products (3 levels: Group → Leaf)
 * - afro_beauty_products → Products (2 levels: Leaf only)
 * 
 * BUSINESS DIRECTORIES (return BusinessProfiles) - 2 levels only:
 * - art → Businesses (2 levels: Leaf only)
 * - school → Businesses (2 levels: Leaf only)
 * - afro_beauty_services → Businesses (2 levels: Leaf only)
 * 
 * INITIATIVES (return SustainabilityInitiatives) - 2 levels only:
 * - sustainability → Initiatives (2 levels: Leaf only)
 * 
 * AFRO BEAUTY: Split into two tabs (Products + Services)
 * - Tab 1: afro_beauty_products → Products
 * - Tab 2: afro_beauty_services → Businesses
 */
class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // PRODUCT CATALOGS (return Products)
        $this->createTextilesCategories();      // 3 levels
        $this->createShoesAndBagsCategories();  // 3 levels
        $this->createAfroBeautyProductCategories();  // 2 levels

        // BUSINESS DIRECTORIES (return Businesses) - 2 levels
        $this->createArtCategories();           // 2 levels - returns Businesses
        $this->createSchoolCategories();        // 2 levels - returns Businesses
        $this->createAfroBeautyServiceCategories();  // 2 levels - returns Businesses

        // INITIATIVES (return SustainabilityInitiatives) - 2 levels
        $this->createSustainabilityCategories();
    }
    
    /**
     * TEXTILES - Product Catalog (3 levels)
     * Structure: Group (Women/Men/Unisex/Fabrics) → Leaf categories
     * Returns: Products
     */
    private function createTextilesCategories(): void
    {
        $women = Category::create([
            'name' => 'Women',
            'slug' => 'textiles-women',
            'type' => 'textiles',
            'order' => 1,
        ]);

        $men = Category::create([
            'name' => 'Men',
            'slug' => 'textiles-men',
            'type' => 'textiles',
            'order' => 2,
        ]);

        $unisex = Category::create([
            'name' => 'Unisex',
            'slug' => 'textiles-unisex',
            'type' => 'textiles',
            'order' => 3,
        ]);

        $fabrics = Category::create([
            'name' => 'Fabrics',
            'slug' => 'textiles-fabrics',
            'type' => 'textiles',
            'order' => 4,
        ]);

        // Leaf categories under Women
        $womenLeaf = [
            'Dresses & Gowns',
            'Two-Piece Sets',
            'Wrappers & Skirts',
            'Tops',
            'Headwear & Accessories',
            'Outerwear',
            'Special Occasion',
        ];
        $this->createLeafCategories($women, $womenLeaf, 'textiles');

        // Leaf categories under Men
        $menLeaf = [
            'Full Suits & Gowns',
            'Two-Piece Sets',
            'Shirts & Tops',
            'Trousers',
            'Wrap Garments',
            'Outerwear',
            'Accessories',
        ];
        $this->createLeafCategories($men, $menLeaf, 'textiles');

        // Leaf categories under Unisex
        $unisexLeaf = [
            'Modern Casual Wear',
            'Capes & Stoles',
            'Home & Lounge Wear',
            'Accessories',
        ];
        $this->createLeafCategories($unisex, $unisexLeaf, 'textiles');

        // Leaf categories under Fabrics
        $fabricList = [
            'Ankara',
            'Kente',
            'Adinkra',
            'Bògòlanfini (Bogolan/Mud Cloth)',
            'Aso Oke',
            'Akwa Ocha',
            'George & Super Wax',
            'Kente Prestige',
            'Faso Dan Fani',
            'Korhogo Cloth',
            'Kitenge & Kanga',
            'Leso',
            'Shúkà',
            'Liputa',
            'Raffia cloth',
            'Shweshwe',
            'Lishu / Letishu',
            'IsiShweshwe',
            'Cotton voile',
            'Woolen fabrics',
            'Melhfa',
        ];
        $this->createLeafCategories($fabrics, $fabricList, 'textiles');
    }

    /**
     * SHOES & BAGS - Product Catalog (3 levels)
     * Structure: Group (Women/Men) → Leaf categories
     * Returns: Products
     */
    private function createShoesAndBagsCategories(): void
    {
        $women = Category::create([
            'name' => 'Women',
            'slug' => 'shoes-bags-women',
            'type' => 'shoes_bags',
            'order' => 1,
        ]);

        $womenLeaf = [
            'Slides & Mules',
            'Block Heel Sandals & Pumps',
            'Wedges',
            'Ballet Flats & Loafers',
            'Evening & Wedding Shoes',
        ];
        $this->createLeafCategories($women, $womenLeaf, 'shoes_bags');

        $men = Category::create([
            'name' => 'Men',
            'slug' => 'shoes-bags-men',
            'type' => 'shoes_bags',
            'order' => 2,
        ]);

        $menLeaf = [
            'African Print Slip-Ons & Loafers',
            'Leather Sandals',
            'Modern Māṣǝr',
            'Brogues & Derbies',
        ];
        $this->createLeafCategories($men, $menLeaf, 'shoes_bags');
    }

    /**
     * AFRO BEAUTY PRODUCTS - Product Catalog (2 levels)
     * Structure: Leaf categories only (no intermediate groups)
     * Returns: Products
     * 
     * This is Tab 1 of Afro Beauty landing box
     */
    private function createAfroBeautyProductCategories(): void
    {
        $productCats = [
            'Hair Care',
            'Skin Care',
            'Makeup & Color Cosmetics',
            'Fragrance',
            "Men's Grooming",
            'Wellness & Bath/Body',
            "Children's Afro-Beauty",
            'Tools & Accessories',
        ];

        $order = 1;
        foreach ($productCats as $name) {
            Category::create([
                'name' => $name,
                'slug' => Str::slug('afro-beauty-products-' . $name),
                'type' => 'afro_beauty_products',
                'order' => $order++,
            ]);
        }
    }

    /**
     * AFRO BEAUTY SERVICES - Business Directory (2 levels)
     * Structure: Leaf categories only (no intermediate groups)
     * Returns: BusinessProfiles
     * 
     * This is Tab 2 of Afro Beauty landing box
     */
    private function createAfroBeautyServiceCategories(): void
    {
        $serviceCats = [
            'Hair Care & Styling Services',
            'Skin Care & Aesthetics Services',
            'Makeup Artistry Services',
            'Barbering Services',
            'Education & Consulting Services',
            'Wellness & Therapeutic Services',
        ];

        $order = 1;
        foreach ($serviceCats as $name) {
            Category::create([
                'name' => $name,
                'slug' => Str::slug('afro-beauty-services-' . $name),
                'type' => 'afro_beauty_services',
                'order' => $order++,
            ]);
        }
    }

    /**
     * ART - Business Directory (2 levels)
     * Structure: Leaf categories only
     * Returns: BusinessProfiles (artists, galleries, studios)
     */
    private function createArtCategories(): void
    {
        // ART -> Leaf (business directory)
        $artCategories = [
            'Sculpture',
            'Painting',
            'Mask',
            'Mixed Media',
            'Installation',
        ];

        $order = 1;
        foreach ($artCategories as $name) {
            Category::create([
                'name' => $name,
                'slug' => Str::slug('art-' . $name),
                'type' => 'art',
                'order' => $order++,
            ]);
        }
    }
    
    /**
     * SCHOOL - Business Directory (2 levels)
     * Structure: Leaf categories only
     * Returns: BusinessProfiles (schools, educational institutions)
     */
    private function createSchoolCategories(): void
    {
        $schoolCategories = [
            'Undergraduate',
            'Graduate',
            'Professional Courses',
            'Certifications',
            'Technical Skills',
            'Soft Skills',
            'Digital Literacy',
            'Entrepreneurship',
            'E-Learning Platforms',
            'Virtual Classrooms',
            'Webinars',
            'Tutorials',
        ];

        $order = 1;
        foreach ($schoolCategories as $name) {
            Category::create([
                'name' => $name,
                'slug' => Str::slug('school-' . $name),
                'type' => 'school',
                'order' => $order++,
            ]);
        }
    }
    
    /**
     * SUSTAINABILITY - Initiatives (2 levels)
     * Structure: Leaf categories only
     * Returns: SustainabilityInitiatives
     */
    private function createSustainabilityCategories(): void
    {
        $sustainabilityCategories = [
            'Biodegradable Items',
            'Recycled Materials',
            'Sustainable Fashion',
            'Green Beauty',
            'Solar Products',
            'Wind Energy',
            'Energy Storage',
            'Efficiency Solutions',
            'Recycling Solutions',
            'Composting',
            'Waste Reduction',
            'Upcycling',
            'Zero Waste',
            'Minimalism',
            'Organic Products',
            'Sustainable Transport',
        ];

        $order = 1;
        foreach ($sustainabilityCategories as $name) {
            Category::create([
                'name' => $name,
                'slug' => Str::slug('sustainability-' . $name),
                'type' => 'sustainability',
                'order' => $order++,
            ]);
        }
    }
    
    /**
     * Helper: Create leaf categories under a parent
     */
    private function createLeafCategories(Category $parent, array $names, string $type): void
    {
        $order = 1;
        foreach ($names as $name) {
            Category::create([
                'name' => $name,
                'slug' => Str::slug($parent->slug . '-' . $name),
                'parent_id' => $parent->id,
                'type' => $type,
                'order' => $order++,
            ]);
        }
    }
}
