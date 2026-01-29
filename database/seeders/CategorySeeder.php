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

        $kidsFemale = Category::create([
            'name' => 'Female',
            'slug' => 'textiles-kids-female',
            'type' => 'textiles',
            'parent_id' => $kids->id,
            'order' => 1,
        ]);

        $kidsMale = Category::create([
            'name' => 'Male',
            'slug' => 'textiles-kids-male',
            'type' => 'textiles',
            'parent_id' => $kids->id,
            'order' => 2,
        ]);

        $this->createLeafChildren($kidsFemale, [
            'Kaba and Slit',
            'Iro and Buba',
            'Dashiki Dress',
            'Kente Frock/Dress',
            'Ankara Puffy Sleeve Dress',
            'Wrap Skirt and Top Set',
            'Gambian Boubou Dress',
            'Headwrap (Gele) and Dress Set',
            'Modern Romper/Jumpsuit (Ankara)',
            'Shawl and Dress Ensemble',
        ]);

        $this->createLeafChildren($kidsMale, [
            'Dashiki Shirt and Trouser Set',
            'Senegalese Kaftan (Grand Boubou)',
            'Kente Tunic and Hat Set',
            'Agbada/Sokoto Set',
            'Ankara Shirt and Shorts Set',
            'Isiagu (Lion Head) Top and Chokers',
            'Safari/Jumpsuit (Ankara)',
            'Embroidery Detailed Tunic',
            'Fila (Hat) and Matching Outfit',
            'Wrapper (Ipelé) and Top Set',
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

        $kidsMale = Category::create([
            'name' => 'Male',
            'slug' => 'shoes-bags-kids-male',
            'type' => 'shoes_bags',
            'parent_id' => $kids->id,
            'order' => 1,
        ]);

        $kidsFemale = Category::create([
            'name' => 'Female',
            'slug' => 'shoes-bags-kids-female',
            'type' => 'shoes_bags',
            'parent_id' => $kids->id,
            'order' => 2,
        ]);

        $kidsMaleShoes = Category::create([
            'name' => 'Shoes',
            'slug' => 'shoes-bags-kids-male-shoes',
            'type' => 'shoes_bags',
            'parent_id' => $kidsMale->id,
            'order' => 1,
        ]);

        $kidsMaleBags = Category::create([
            'name' => 'Bags',
            'slug' => 'shoes-bags-kids-male-bags',
            'type' => 'shoes_bags',
            'parent_id' => $kidsMale->id,
            'order' => 2,
        ]);

        $kidsFemaleShoes = Category::create([
            'name' => 'Shoes',
            'slug' => 'shoes-bags-kids-female-shoes',
            'type' => 'shoes_bags',
            'parent_id' => $kidsFemale->id,
            'order' => 1,
        ]);

        $kidsFemaleBags = Category::create([
            'name' => 'Bags',
            'slug' => 'shoes-bags-kids-female-bags',
            'type' => 'shoes_bags',
            'parent_id' => $kidsFemale->id,
            'order' => 2,
        ]);

        $this->createLeafChildren($kidsMaleShoes, [
            'Mojari (Embroidered Leather Shoes)',
            'Kobo Kobo (Woven Slippers)',
            'Beaded Leather Sandals',
            'Ankara Print Slip-on Sneakers',
            'Embroidered Velvet Slippers',
        ]);

        $this->createLeafChildren($kidsFemaleShoes, [
            'Beaded Gelesko Slippers',
            'Adinkra Symbol Sandals',
            'Ankara Print Ballerina Flats',
            'Embroidered Pom-pom Sliders',
            'Sequined Jelly Shoes',
        ]);

        $this->createLeafChildren($kidsMaleBags, [
            'Kente Print Backpack',
            'Leather Pouch (Tribal Motifs)',
            'Ankara Drawstring Bag',
            'Beaded Waist Pouch',
            'Mudcloth Pattern Messenger Bag',
        ]);

        $this->createLeafChildren($kidsFemaleBags, [
            'Beaded Handbag (Zulu/Maasai)',
            'Ankara Print Backpack with Pom-poms',
            'Embroidered Clutch',
            'Basket Bag (Mini)',
            'Shweshwe Pattern Crossbody Bag',
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
