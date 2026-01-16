<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // TEXTILES (Products) -> Men/Women/Unisex/Fabrics -> Leaf
        $this->createTextilesCategories();

        // AFRO BEAUTY PRODUCTS (Products) -> Leaf
        $this->createAfroBeautyProductsCategories();

        // AFRO BEAUTY SERVICES (Businesses) -> Leaf
        $this->createAfroBeautyServicesCategories();

        // SHOES & BAGS (Products) -> Men/Women -> Leaf
        $this->createShoesAndBagsCategories();

        // ART (Businesses) -> Leaf
        $this->createArtCategories();

        // SCHOOL (Businesses) -> Leaf
        $this->createSchoolCategories();

        // SUSTAINABILITY (Initiatives) -> Leaf
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

        $fabrics = Category::create([
            'name' => 'Fabrics',
            'slug' => 'textiles-fabrics',
            'type' => 'textiles',
            'order' => 4,
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

        $this->createLeafChildren($fabrics, [
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
            'Raffia Cloth',
            'Shweshwe',
            'Lishu / Letishu',
            'IsiShweshwe',
            'Cotton Voile',
            'Woolen Fabrics',
            'Melhfa',
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
        $this->createLeafType('art', [
            'Sculpture',
            'Painting',
            'Mask',
            'Mixed Media',
            'Installation',
        ], 'art');
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
