<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $this->createTextilesCategories();
        $this->createAfroBeautyProductsCategories();
        $this->createAfroBeautyServicesCategories();
        $this->createShoesAndBagsCategories();
        $this->createArtCategories();
        $this->createSchoolCategories();
        $this->createSustainabilityCategories();
    }

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

        $this->createLeafChildren($women, [
            'Dresses & Gowns',
            'Two-Piece Sets',
            'Wrappers & Skirts',
            'Tops',
            'Headwear & Accessories',
            'Outerwear',
            'Special Occasion',
        ]);

        $this->createLeafChildren($men, [
            'Full Suits & Gowns',
            'Two-Piece Sets',
            'Shirts & Tops',
            'Trousers',
            'Wrap Garments',
            'Outerwear',
            'Accessories',
        ]);

        $this->createLeafChildren($unisex, [
            'Modern Casual Wear',
            'Capes & Stoles',
            'Home & Lounge Wear',
            'Accessories',
        ]);
    }

    private function createAfroBeautyProductsCategories(): void
    {
        $this->createLeafType('afro_beauty_products', [
            'Hair Care',
            'Skin Care',
            'Makeup & Color Cosmetics',
            'Fragrance',
            "Men's Grooming",
            'Wellness & Bath/Body',
            "Children's Afro-Beauty",
            'Tools & Accessories',
        ], 'afro-beauty-products');
    }

    private function createAfroBeautyServicesCategories(): void
    {
        $this->createLeafType('afro_beauty_services', [
            'Hair Care & Styling Services',
            'Skin Care & Aesthetics Services',
            'Makeup Artistry Services',
            'Barbering Services',
            'Education & Consulting Services',
            'Wellness & Therapeutic Services',
        ], 'afro-beauty-services');
    }

    private function createShoesAndBagsCategories(): void
    {
        $women = Category::create([
            'name' => 'Women',
            'slug' => 'shoes-bags-women',
            'type' => 'shoes_bags',
            'order' => 1,
        ]);

        $men = Category::create([
            'name' => 'Men',
            'slug' => 'shoes-bags-men',
            'type' => 'shoes_bags',
            'order' => 2,
        ]);

        $this->createLeafChildren($women, [
            'Slides & Mules',
            'Block Heel Sandals & Pumps',
            'Wedges',
            'Ballet Flats & Loafers',
            'Evening & Wedding Shoes',
        ]);

        $this->createLeafChildren($men, [
            'African Print Slip-Ons & Loafers',
            'Leather Sandals',
            'Modern Māṣǝr',
            'Brogues & Derbies',
        ]);
    }

    private function createArtCategories(): void
    {
        $products = Category::create([
            'name' => 'Products',
            'slug' => 'art-products',
            'type' => 'art',
            'order' => 1,
        ]);

        $this->createLeafChildren($products, [
            'Sculpture',
            'Painting',
            'Mask',
            'Mixed Media',
            'Installation',
        ]);
    }

    private function createSchoolCategories(): void
    {
        $this->createLeafType('school', [
            'Fashion',
            'Music',
            'Catering',
            'Beauty',
        ], 'school');
    }

    private function createSustainabilityCategories(): void
    {
        $this->createLeafType('sustainability', [
            'Eco-Friendly Products',
            'Renewable Energy',
            'Waste Management',
            'Sustainable Living',
        ], 'sustainability');
    }

    private function createLeafChildren(Category $parent, array $names): void
    {
        $order = 1;
        foreach ($names as $name) {
            Category::create([
                'name' => $name,
                'slug' => Str::slug($parent->slug . '-' . $name),
                'parent_id' => $parent->id,
                'type' => $parent->type,
                'order' => $order++,
            ]);
        }
    }

    private function createLeafType(string $type, array $names, string $slugPrefix): void
    {
        $order = 1;
        foreach ($names as $name) {
            Category::create([
                'name' => $name,
                'slug' => Str::slug($slugPrefix . '-' . $name),
                'type' => $type,
                'order' => $order++,
            ]);
        }
    }
}
