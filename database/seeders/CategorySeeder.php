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

        $kids = Category::create([
            'name' => 'Kids',
            'slug' => 'textiles-kids',
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

        $this->createLeafChildren($kids, [
            'Female - Kaba and Slit',
            'Female - Iro and Buba',
            'Female - Dashiki Dress',
            'Female - Kente Frock/Dress',
            'Female - Ankara Puffy Sleeve Dress',
            'Female - Wrap Skirt and Top Set',
            'Female - Gambian Boubou Dress',
            'Female - Headwrap (Gele) and Dress Set',
            'Female - Modern Romper/Jumpsuit (Ankara)',
            'Female - Shawl and Dress Ensemble',
            'Male - Dashiki Shirt and Trouser Set',
            'Male - Senegalese Kaftan (Grand Boubou)',
            'Male - Kente Tunic and Hat Set',
            'Male - Agbada/Sokoto Set',
            'Male - Ankara Shirt and Shorts Set',
            'Male - Isiagu (Lion Head) Top and Chokers',
            'Male - Safari/Jumpsuit (Ankara)',
            'Male - Embroidery Detailed Tunic',
            'Male - Fila (Hat) and Matching Outfit',
            'Male - Wrapper (Ipelé) and Top Set',
        ]);
    }

    private function createAfroBeautyProductsCategories(): void
    {
        // 3-level structure: Afro Beauty Products -> Group -> Leaf
        $kids = Category::create([
            'name' => 'Kids',
            'slug' => 'afro-beauty-products-kids',
            'type' => 'afro_beauty_products',
            'order' => 1,
        ]);

        $women = Category::create([
            'name' => 'Women',
            'slug' => 'afro-beauty-products-women',
            'type' => 'afro_beauty_products',
            'order' => 2,
        ]);

        $men = Category::create([
            'name' => 'Men',
            'slug' => 'afro-beauty-products-men',
            'type' => 'afro_beauty_products',
            'order' => 3,
        ]);

        // Kids
        $this->createLeafChildren($kids, [
            'Shampoos & Conditioners',
            'Hair Moisturizers & Creams',
            'Hair Butters & Pomades',
            'Styling Gels & Custards',
            'Scalp Care Oils & Treatments',
            'Detangling Sprays & Milks',
            'Beard & Mustache Care (Older Teens)',
            'Body Moisturizers & Butters',
            'Cleansing Bars & Body Washes',
        ]);

        // Women
        $this->createLeafChildren($women, [
            'Shampoos & Conditioners',
            'Hair Moisturizers & Lotions',
            'Deep Conditioners & Hair Masks',
            'Leave-In Conditioners & Detanglers',
            'Hair Butters & Cream',
            'Styling Gels, Custards & Edge Control',
            'Scalp Care Oils & Treatments',
            'Braid & Twist Sprays',
            'Hair Accessories Care Products',
            'Body Moisturizers & Butters',
            'Cleansing Bars & Body Washes',
        ]);

        // Men
        $this->createLeafChildren($men, [
            'Shampoos & Conditioners',
            'Hair Moisturizers & Creams',
            'Hair Butters & Pomades',
            'Styling Gels & Custards',
            'Scalp Care Oils & Treatments',
            'Detangling Sprays & Milks',
            'Beard & Mustache Care',
            'Body Moisturizers & Butters',
            'Cleansing Bars & Body Washes',
        ]);
    }

    // Afro beauty services removed for now (no services)
    private function createAfroBeautyServicesCategories(): void
    {
        return;
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

        $kids = Category::create([
            'name' => 'Kids',
            'slug' => 'shoes-bags-kids',
            'type' => 'shoes_bags',
            'order' => 3,
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

        $this->createLeafChildren($kids, [
            'Kids Male Shoes - Mojari (Embroidered Leather Shoes)',
            'Kids Male Shoes - Kobo Kobo (Woven Slippers)',
            'Kids Male Shoes - Beaded Leather Sandals',
            'Kids Male Shoes - Ankara Print Slip-on Sneakers',
            'Kids Male Shoes - Embroidered Velvet Slippers',
            'Kids Female Shoes - Beaded Gelesko Slippers',
            'Kids Female Shoes - Adinkra Symbol Sandals',
            'Kids Female Shoes - Ankara Print Ballerina Flats',
            'Kids Female Shoes - Embroidered Pom-pom Sliders',
            'Kids Female Shoes - Sequined Jelly Shoes',
            'Kids Male Bags - Kente Print Backpack',
            'Kids Male Bags - Leather Pouch (Tribal Motifs)',
            'Kids Male Bags - Ankara Drawstring Bag',
            'Kids Male Bags - Beaded Waist Pouch',
            'Kids Male Bags - Mudcloth Pattern Messenger Bag',
            'Kids Female Bags - Beaded Handbag (Zulu/Maasai)',
            'Kids Female Bags - Ankara Backpack with Pom-poms',
            'Kids Female Bags - Embroidered Clutch',
            'Kids Female Bags - Basket Bag (Mini)',
            'Kids Female Bags - Shweshwe Pattern Crossbody Bag',
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
