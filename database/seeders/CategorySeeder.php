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
        // Market categories
        $this->createMarketCategories();
        
        // Beauty categories
        $this->createBeautyCategories();
        
        // Brand categories
        $this->createBrandCategories();
        
        // School categories
        $this->createSchoolCategories();
        
        // Sustainability categories
        $this->createSustainabilityCategories();
        
        // Music categories
        $this->createMusicCategories();

    }
    
    private function createMarketCategories(): void
    {
        // Top-level market categories
        $men = Category::create([
            'name' => 'Men',
            'slug' => 'men',
            'type' => 'market',
            'order' => 1,
        ]);
        
        $women = Category::create([
            'name' => 'Women',
            'slug' => 'women',
            'type' => 'market',
            'order' => 2,
        ]);
        
        // Men's subcategories
        $menCategories = [
            'Clothing' => ['Shirts', 'Pants', 'Suits', 'Casual Wear'],
            'Shoes' => ['Sneakers', 'Formal Shoes', 'Boots', 'Sandals'],
            'Accessories' => ['Watches', 'Bags', 'Belts', 'Sunglasses'],
        ];
        
        $this->createSubcategories($men, $menCategories, 'market');
        
        // Women's subcategories
        $womenCategories = [
            'Clothing' => ['Dresses', 'Tops', 'Bottoms', 'Outerwear'],
            'Shoes' => ['Heels', 'Flats', 'Sneakers', 'Boots'],
            'Accessories' => ['Jewelry', 'Handbags', 'Scarves', 'Sunglasses'],
        ];
        
        $this->createSubcategories($women, $womenCategories, 'market');
    }
    
    private function createBeautyCategories(): void
    {
        $beautyCategories = [
            'Skincare' => ['Cleansers', 'Moisturizers', 'Serums', 'Sunscreen'],
            'Makeup' => ['Foundation', 'Lipstick', 'Eyeshadow', 'Mascara'],
            'Hair Care' => ['Shampoo', 'Conditioner', 'Styling Products', 'Treatments'],
            'Fragrance' => ['Perfumes', 'Body Sprays', 'Essential Oils'],
            'Tools & Accessories' => ['Brushes', 'Sponges', 'Mirrors', 'Storage'],
        ];
        
        $this->createTopLevelWithSubcategories($beautyCategories, 'beauty');
    }
    
    private function createBrandCategories(): void
    {
        $brandCategories = [
            'Luxury Brands' => ['Designer Fashion', 'Premium Accessories', 'High-End Beauty'],
            'Local Brands' => ['Nigerian Designers', 'Artisan Products', 'Handmade Items'],
            'International Brands' => ['Global Fashion', 'Tech Brands', 'Sports Brands'],
            'Emerging Brands' => ['Startups', 'New Designers', 'Innovative Products'],
        ];
        
        $this->createTopLevelWithSubcategories($brandCategories, 'brand');
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
    
    private function createMusicCategories(): void
    {
        $musicCategories = [
            'Genres' => ['Afrobeats', 'Hip Hop', 'R&B', 'Gospel', 'Traditional'],
            'Instruments' => ['Drums', 'Guitars', 'Keyboards', 'Traditional Instruments'],
            'Music Production' => ['Recording Equipment', 'Software', 'Mixing Tools', 'Studio Services'],
            'Events & Performances' => ['Concerts', 'Festivals', 'Live Performances', 'Music Venues'],
        ];
        
        $this->createTopLevelWithSubcategories($musicCategories, 'music');
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
