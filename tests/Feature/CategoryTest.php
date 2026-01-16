<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\BusinessProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * CategoryTest
 * 
 * FINAL LOCKED MODEL - Category Types:
 * =====================================
 * 
 * PRODUCT CATALOGS (return Products):
 * - textiles (3 levels: Group → Leaf)
 * - shoes_bags (3 levels: Group → Leaf)
 * - afro_beauty_products (2 levels: Leaf only)
 * 
 * BUSINESS DIRECTORIES (return BusinessProfiles) - 2 levels:
 * - art (2 levels: Leaf only)
 * - school (2 levels: Leaf only)
 * - afro_beauty_services (2 levels: Leaf only)
 * 
 * INITIATIVES (return SustainabilityInitiatives) - 2 levels:
 * - sustainability (2 levels: Leaf only)
 */
class CategoryTest extends TestCase
{
    use RefreshDatabase;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // Create some test categories
        $this->createTestCategories();
    }
    
    /** @test */
    public function can_get_textiles_categories()
    {
        $response = $this->getJson('/api/categories?type=textiles');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'type',
                        'order',
                        'children'
                    ]
                ],
                'meta' => [
                    'type',
                    'returns',
                    'total_count'
                ]
            ]);
            
        $data = $response->json('data');
        $this->assertNotEmpty($data);
        $this->assertEquals('textiles', $data[0]['type']);
        $this->assertEquals('products', $response->json('meta.returns'));
    }
    
    /** @test */
    public function can_get_shoes_bags_categories()
    {
        $response = $this->getJson('/api/categories?type=shoes_bags');
        
        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertNotEmpty($data);
        $this->assertEquals('shoes_bags', $data[0]['type']);
        $this->assertEquals('products', $response->json('meta.returns'));
    }
    
    /** @test */
    public function can_get_afro_beauty_products_categories()
    {
        $response = $this->getJson('/api/categories?type=afro_beauty_products');
        
        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertNotEmpty($data);
        $this->assertEquals('afro_beauty_products', $data[0]['type']);
        $this->assertEquals('products', $response->json('meta.returns'));
    }
    
    /** @test */
    public function can_get_afro_beauty_services_categories()
    {
        $response = $this->getJson('/api/categories?type=afro_beauty_services');
        
        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertNotEmpty($data);
        $this->assertEquals('afro_beauty_services', $data[0]['type']);
        $this->assertEquals('businesses', $response->json('meta.returns'));
    }
    
    /** @test */
    public function can_get_art_categories()
    {
        $response = $this->getJson('/api/categories?type=art');
        
        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertNotEmpty($data);
        $this->assertEquals('art', $data[0]['type']);
        $this->assertEquals('businesses', $response->json('meta.returns'));
    }
    
    /** @test */
    public function can_get_school_categories()
    {
        $response = $this->getJson('/api/categories?type=school');
        
        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertNotEmpty($data);
        $this->assertEquals('school', $data[0]['type']);
        $this->assertEquals('businesses', $response->json('meta.returns'));
    }
    
    /** @test */
    public function can_get_sustainability_categories()
    {
        $response = $this->getJson('/api/categories?type=sustainability');
        
        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertNotEmpty($data);
        $this->assertEquals('sustainability', $data[0]['type']);
        $this->assertEquals('initiatives', $response->json('meta.returns'));
    }
    
    /** @test */
    public function requires_valid_type_parameter()
    {
        $response = $this->getJson('/api/categories?type=invalid');
        
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['type']);
    }
    
    /** @test */
    public function requires_type_parameter()
    {
        $response = $this->getJson('/api/categories');
        
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['type']);
    }
    
    /** @test */
    public function can_get_category_children()
    {
        $parent = Category::factory()->textiles()->topLevel()->create();
        $child1 = Category::factory()->textiles()->withParent($parent)->create();
        $child2 = Category::factory()->textiles()->withParent($parent)->create();
        
        $response = $this->getJson("/api/categories/{$parent->id}/children");
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'parent' => [
                        'id',
                        'name',
                        'slug',
                        'type'
                    ],
                    'children' => [
                        '*' => [
                            'id',
                            'name',
                            'slug',
                            'parent_id',
                            'type',
                            'order'
                        ]
                    ],
                    'entity_type'
                ]
            ]);
            
        $data = $response->json('data');
        $this->assertEquals($parent->id, $data['parent']['id']);
        $this->assertCount(2, $data['children']);
        $this->assertEquals('products', $data['entity_type']);
    }
    
    /** @test */
    public function returns_404_for_nonexistent_category_children()
    {
        $response = $this->getJson('/api/categories/999/children');
        
        $response->assertStatus(404);
    }
    
    /** @test */
    public function can_get_category_items_by_type_and_slug()
    {
        $category = Category::factory()->textiles()->create(['slug' => 'test-textiles-category']);
        
        $response = $this->getJson('/api/categories/textiles/test-textiles-category/items');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'category' => [
                        'id',
                        'name',
                        'slug',
                        'type',
                        'entity_type'
                    ],
                    'items'
                ]
            ]);
            
        $data = $response->json('data');
        $this->assertEquals($category->id, $data['category']['id']);
        $this->assertEquals('products', $data['category']['entity_type']);
    }
    
    /** @test */
    public function returns_400_for_invalid_category_type_in_items()
    {
        $response = $this->getJson('/api/categories/invalid/test-slug/items');
        
        $response->assertStatus(400)
            ->assertJsonPath('status', 'error');
    }
    
    /** @test */
    public function returns_404_for_nonexistent_category_slug_in_items()
    {
        $response = $this->getJson('/api/categories/textiles/nonexistent-slug/items');
        
        $response->assertStatus(404);
    }
    
    /** @test */
    public function categories_are_ordered_correctly()
    {
        Category::truncate();
        
        $category1 = Category::factory()->textiles()->create(['order' => 3]);
        $category2 = Category::factory()->textiles()->create(['order' => 1]);
        $category3 = Category::factory()->textiles()->create(['order' => 2]);
        
        $response = $this->getJson('/api/categories?type=textiles');
        
        $response->assertStatus(200);
        $data = $response->json('data');
        
        $this->assertEquals($category2->id, $data[0]['id']); // order 1
        $this->assertEquals($category3->id, $data[1]['id']); // order 2
        $this->assertEquals($category1->id, $data[2]['id']); // order 3
    }
    
    /** @test */
    public function only_returns_top_level_categories()
    {
        Category::truncate();
        
        $parent = Category::factory()->textiles()->topLevel()->create();
        $child = Category::factory()->textiles()->withParent($parent)->create();
        
        $response = $this->getJson('/api/categories?type=textiles');
        
        $response->assertStatus(200);
        $data = $response->json('data');
        
        $this->assertCount(1, $data);
        $this->assertEquals($parent->id, $data[0]['id']);
        $this->assertNull($data[0]['parent_id']);
    }
    
    /** @test */
    public function includes_children_in_category_response()
    {
        Category::truncate();
        
        $parent = Category::factory()->textiles()->topLevel()->create();
        $child1 = Category::factory()->textiles()->withParent($parent)->create(['order' => 2]);
        $child2 = Category::factory()->textiles()->withParent($parent)->create(['order' => 1]);
        
        $response = $this->getJson('/api/categories?type=textiles');
        
        $response->assertStatus(200);
        $data = $response->json('data');
        
        $this->assertCount(2, $data[0]['children']);
        $this->assertEquals($child2->id, $data[0]['children'][0]['id']); // order 1
        $this->assertEquals($child1->id, $data[0]['children'][1]['id']); // order 2
    }
    
    /** @test */
    public function can_get_all_categories_tree()
    {
        $response = $this->getJson('/api/categories/all');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'textiles',
                    'shoes_bags',
                    'afro_beauty_products',
                    'afro_beauty_services',
                    'art',
                    'school',
                    'sustainability'
                ],
                'meta' => [
                    'type_mapping',
                    'afro_beauty_tabs',
                    'depth_rules',
                    'types'
                ]
            ]);
            
        $meta = $response->json('meta');
        $this->assertEquals('afro_beauty_products', $meta['afro_beauty_tabs']['tab_1_products']);
        $this->assertEquals('afro_beauty_services', $meta['afro_beauty_tabs']['tab_2_services']);
    }
    
    private function createTestCategories()
    {
        // PRODUCT CATALOG TYPES
        // Textiles (3 levels)
        Category::factory()->textiles()->topLevel()->create(['name' => 'Women', 'slug' => 'textiles-women']);
        Category::factory()->textiles()->topLevel()->create(['name' => 'Men', 'slug' => 'textiles-men']);
        
        // Shoes & Bags (3 levels)
        Category::factory()->shoesBags()->topLevel()->create(['name' => 'Women Shoes', 'slug' => 'shoes-bags-women']);
        Category::factory()->shoesBags()->topLevel()->create(['name' => 'Men Shoes', 'slug' => 'shoes-bags-men']);
        
        // Afro Beauty Products (2 levels - leaf only)
        Category::factory()->afroBeautyProducts()->topLevel()->create(['name' => 'Hair Care', 'slug' => 'afro-beauty-products-hair-care']);
        Category::factory()->afroBeautyProducts()->topLevel()->create(['name' => 'Skin Care', 'slug' => 'afro-beauty-products-skin-care']);
        
        // BUSINESS DIRECTORY TYPES
        // Afro Beauty Services (2 levels - leaf only)
        Category::factory()->afroBeautyServices()->topLevel()->create(['name' => 'Hair Styling', 'slug' => 'afro-beauty-services-hair-styling']);
        Category::factory()->afroBeautyServices()->topLevel()->create(['name' => 'Makeup', 'slug' => 'afro-beauty-services-makeup']);
        
        // Art (2 levels - leaf only)
        Category::factory()->art()->topLevel()->create(['name' => 'Sculpture', 'slug' => 'art-sculpture']);
        Category::factory()->art()->topLevel()->create(['name' => 'Painting', 'slug' => 'art-painting']);
        
        // School (2 levels - leaf only)
        Category::factory()->school()->topLevel()->create(['name' => 'Undergraduate', 'slug' => 'school-undergraduate']);
        Category::factory()->school()->topLevel()->create(['name' => 'Graduate', 'slug' => 'school-graduate']);
        
        // INITIATIVE TYPES
        // Sustainability (2 levels - leaf only)
        Category::factory()->sustainability()->topLevel()->create(['name' => 'Recycling', 'slug' => 'sustainability-recycling']);
        Category::factory()->sustainability()->topLevel()->create(['name' => 'Zero Waste', 'slug' => 'sustainability-zero-waste']);
    }
}
