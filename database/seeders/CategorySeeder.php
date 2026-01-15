<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // LANDING PAGE BOX 1: TEXTILES (replaces Market)
        $this->createTextilesCategories();

        // LANDING PAGE BOX 2: AFRO BEAUTY (replaces Beauty)
        $this->createAfroBeautyCategories();

        // LANDING PAGE BOX 3: SHOES AND BAGS (replaces Brands)
        $this->createShoesAndBagsCategories();

        // LANDING PAGE BOX 4: ART (replaces Music)
        $this->createArtCategories();

        // Keep existing School categories (until you provide new school taxonomy)
        $this->createSchoolCategories();

        // Keep existing Sustainability categories (until you provide new sustainability taxonomy)
        $this->createSustainabilityCategories();
    }
    
    private function createTextilesCategories(): void
    {
        // Top-level: For Women / For Men / Unisex
        $women = Category::create([
            'name' => 'For Women',
            'slug' => 'textiles-women',
            'type' => 'textiles',
            'order' => 1,
        ]);

        $men = Category::create([
            'name' => 'For Men',
            'slug' => 'textiles-men',
            'type' => 'textiles',
            'order' => 2,
        ]);

        $unisex = Category::create([
            'name' => 'Unisex / For Both',
            'slug' => 'textiles-unisex',
            'type' => 'textiles',
            'order' => 3,
        ]);

        // Women subcategories
        $womenCategories = [
            'Categories' => [
                'Dresses & Gowns',
                'Two-Piece Sets',
                'Wrappers & Skirts',
                'Tops',
                'Headwear & Accessories',
                'Outerwear',
                'Special Occasion',
            ],
        ];
        $this->createSubcategories($women, $womenCategories, 'textiles');

        // Men subcategories
        $menCategories = [
            'Categories' => [
                'Full Suits & Gowns',
                'Two-Piece Sets',
                'Shirts & Tops',
                'Trousers',
                'Wrap Garments',
                'Outerwear',
                'Accessories',
            ],
        ];
        $this->createSubcategories($men, $menCategories, 'textiles');

        // Unisex subcategories
        $unisexCategories = [
            'Categories' => [
                'Modern Casual Wear',
                'Capes & Stoles',
                'Home & Lounge Wear',
                'Accessories',
            ],
        ];
        $this->createSubcategories($unisex, $unisexCategories, 'textiles');

        // Fabric filters (as categories under Textiles root)
        $fabrics = Category::create([
            'name' => 'Filter by Fabrics',
            'slug' => 'textiles-fabrics',
            'type' => 'textiles',
            'order' => 4,
        ]);

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

        $fabricOrder = 1;
        foreach ($fabricList as $name) {
            Category::create([
                'name' => $name,
                'slug' => Str::slug($fabrics->slug . '-' . $name),
                'parent_id' => $fabrics->id,
                'type' => 'textiles',
                'order' => $fabricOrder++,
            ]);
        }
    }

    private function createAfroBeautyCategories(): void
    {
        // Afro Beauty has products + services sections
        $products = Category::create([
            'name' => 'Categories Under Products',
            'slug' => 'afro-beauty-products',
            'type' => 'afro_beauty',
            'order' => 1,
        ]);

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
                'slug' => Str::slug($products->slug . '-' . $name),
                'parent_id' => $products->id,
                'type' => 'afro_beauty',
                'order' => $order++,
            ]);
        }

        $services = Category::create([
            'name' => 'Categories Under Services',
            'slug' => 'afro-beauty-services',
            'type' => 'afro_beauty',
            'order' => 2,
        ]);

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
                'slug' => Str::slug($services->slug . '-' . $name),
                'parent_id' => $services->id,
                'type' => 'afro_beauty',
                'order' => $order++,
            ]);
        }

        // Additional filters are not categories in DB (brand, price range, ingredients, etc.)
        // Those should be implemented as product attributes/filters later.
    }

    private function createShoesAndBagsCategories(): void
    {
        $women = Category::create([
            'name' => 'For Women',
            'slug' => 'shoes-bags-women',
            'type' => 'shoes_bags',
            'order' => 1,
        ]);

        $womenCats = [
            'Categories' => [
                'Slides & Mules',
                'Block Heel Sandals & Pumps',
                'Wedges',
                'Ballet Flats & Loafers',
                'Evening & Wedding Shoes',
            ],
        ];
        $this->createSubcategories($women, $womenCats, 'shoes_bags');

        $men = Category::create([
            'name' => 'For Men',
            'slug' => 'shoes-bags-men',
            'type' => 'shoes_bags',
            'order' => 2,
        ]);

        $menCats = [
            'Categories' => [
                'African Print Slip-Ons & Loafers',
                'Leather Sandals',
                'Modern Māṣǝr',
                'Brogues & Derbies',
            ],
        ];
        $this->createSubcategories($men, $menCats, 'shoes_bags');
    }

    private function createArtCategories(): void
    {
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
    
    private function createSchoolCategories(): void
    {
        $schoolCategories = [
            'Academic Programs' => ['Undergraduate', 'Graduate', 'Professional Courses', 'Certifications'],
            'Skills Training' => ['Technical Skills', 'Soft Skills', 'Digital Literacy', 'Entrepreneurship'],
            'Online Learning' => ['E-Learning Platforms', 'Virtual Classrooms', 'Webinars', 'Tutorials'],
            'Educational Resources' => ['Books', 'Study Materials', 'Research Tools', 'Learning Apps'],
        ];
        
        $this->createTopLevelWithSubcategories($schoolCategories, 'school');
    }
    
    private function createSustainabilityCategories(): void
    {
        $sustainabilityCategories = [
            'Eco-Friendly Products' => ['Biodegradable Items', 'Recycled Materials', 'Sustainable Fashion', 'Green Beauty'],
            'Renewable Energy' => ['Solar Products', 'Wind Energy', 'Energy Storage', 'Efficiency Solutions'],
            'Waste Management' => ['Recycling Solutions', 'Composting', 'Waste Reduction', 'Upcycling'],
            'Sustainable Living' => ['Zero Waste', 'Minimalism', 'Organic Products', 'Sustainable Transport'],
        ];
        
        $this->createTopLevelWithSubcategories($sustainabilityCategories, 'sustainability');
    }
    
    private function createTopLevelWithSubcategories(array $categories, string $type): void
    {
        $order = 1;
        
        foreach ($categories as $parentName => $subcategories) {
            $parent = Category::create([
                'name' => $parentName,
                'slug' => Str::slug($parentName),
                'type' => $type,
                'order' => $order++,
            ]);
            
            $subOrder = 1;
            foreach ($subcategories as $subName) {
                Category::create([
                    'name' => $subName,
                    'slug' => Str::slug($parent->slug . '-' . $subName),
                    'parent_id' => $parent->id,
                    'type' => $type,
                    'order' => $subOrder++,
                ]);
            }
        }
    }
    
    private function createSubcategories(Category $parent, array $categories, string $type): void
    {
        $order = 1;
        
        foreach ($categories as $categoryName => $subcategories) {
            $category = Category::create([
                'name' => $categoryName,
                'slug' => Str::slug($parent->slug . '-' . $categoryName),
                'parent_id' => $parent->id,
                'type' => $type,
                'order' => $order++,
            ]);
            
            $subOrder = 1;
            foreach ($subcategories as $subName) {
                Category::create([
                    'name' => $subName,
                    'slug' => Str::slug($category->slug . '-' . $subName),
                    'parent_id' => $category->id,
                    'type' => $type,
                    'order' => $subOrder++,
                ]);
            }
        }
    }
}
