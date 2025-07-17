<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\BusinessProfile;
use App\Models\Blog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

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
    public function can_get_categories_by_type()
    {
        $response = $this->getJson('/api/categories?type=market');
        
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
                        'children' => [
                            '*' => [
                                'id',
                                'name',
                                'slug',
                                'parent_id',
                                'type',
                                'order'
                            ]
                        ]
                    ]
                ]
            ]);
            
        $data = $response->json('data');
        $this->assertNotEmpty($data);
        $this->assertEquals('market', $data[0]['type']);
    }
    
    /** @test */
    public function can_get_beauty_categories()
    {
        $response = $this->getJson('/api/categories?type=beauty');
        
        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertNotEmpty($data);
        $this->assertEquals('beauty', $data[0]['type']);
    }
    
    /** @test */
    public function can_get_brand_categories()
    {
        $response = $this->getJson('/api/categories?type=brand');
        
        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertNotEmpty($data);
        $this->assertEquals('brand', $data[0]['type']);
    }
    
    /** @test */
    public function can_get_school_categories()
    {
        $response = $this->getJson('/api/categories?type=school');
        
        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertNotEmpty($data);
        $this->assertEquals('school', $data[0]['type']);
    }
    
    /** @test */
    public function can_get_sustainability_categories()
    {
        $response = $this->getJson('/api/categories?type=sustainability');
        
        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertNotEmpty($data);
        $this->assertEquals('sustainability', $data[0]['type']);
    }
    
    /** @test */
    public function can_get_music_categories()
    {
        $response = $this->getJson('/api/categories?type=music');
        
        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertNotEmpty($data);
        $this->assertEquals('music', $data[0]['type']);
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
        $parent = Category::factory()->market()->topLevel()->create();
        $child1 = Category::factory()->market()->withParent($parent)->create();
        $child2 = Category::factory()->market()->withParent($parent)->create();
        
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
                    ]
                ]
            ]);
            
        $data = $response->json('data');
        $this->assertEquals($parent->id, $data['parent']['id']);
        $this->assertCount(2, $data['children']);
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
        $category = Category::factory()->market()->create(['slug' => 'test-category']);
        
        $response = $this->getJson('/api/categories/market/test-category/items');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'category' => [
                        'id',
                        'name',
                        'slug',
                        'type'
                    ],
                    'items'
                ]
            ]);
            
        $data = $response->json('data');
        $this->assertEquals($category->id, $data['category']['id']);
        $this->assertIsArray($data['items']);
    }
    
    /** @test */
    public function returns_400_for_invalid_category_type_in_items()
    {
        $response = $this->getJson('/api/categories/invalid/test-slug/items');
        
        $response->assertStatus(400)
            ->assertJson([
                'status' => 'error',
                'message' => 'Invalid category type'
            ]);
    }
    
    /** @test */
    public function returns_404_for_nonexistent_category_slug_in_items()
    {
        $response = $this->getJson('/api/categories/market/nonexistent-slug/items');
        
        $response->assertStatus(404);
    }
    
    /** @test */
    public function categories_are_ordered_correctly()
    {
        Category::truncate();
        
        $category1 = Category::factory()->market()->create(['order' => 3]);
        $category2 = Category::factory()->market()->create(['order' => 1]);
        $category3 = Category::factory()->market()->create(['order' => 2]);
        
        $response = $this->getJson('/api/categories?type=market');
        
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
        
        $parent = Category::factory()->market()->topLevel()->create();
        $child = Category::factory()->market()->withParent($parent)->create();
        
        $response = $this->getJson('/api/categories?type=market');
        
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
        
        $parent = Category::factory()->market()->topLevel()->create();
        $child1 = Category::factory()->market()->withParent($parent)->create(['order' => 2]);
        $child2 = Category::factory()->market()->withParent($parent)->create(['order' => 1]);
        
        $response = $this->getJson('/api/categories?type=market');
        
        $response->assertStatus(200);
        $data = $response->json('data');
        
        $this->assertCount(2, $data[0]['children']);
        $this->assertEquals($child2->id, $data[0]['children'][0]['id']); // order 1
        $this->assertEquals($child1->id, $data[0]['children'][1]['id']); // order 2
    }
    
    private function createTestCategories()
    {
        // Create some test categories for each type
        Category::factory()->market()->topLevel()->create(['name' => 'Men', 'slug' => 'men']);
        Category::factory()->market()->topLevel()->create(['name' => 'Women', 'slug' => 'women']);
        
        Category::factory()->beauty()->topLevel()->create(['name' => 'Skincare', 'slug' => 'skincare']);
        Category::factory()->beauty()->topLevel()->create(['name' => 'Makeup', 'slug' => 'makeup']);
        
        Category::factory()->brand()->topLevel()->create(['name' => 'Luxury Brands', 'slug' => 'luxury-brands']);
        Category::factory()->brand()->topLevel()->create(['name' => 'Local Brands', 'slug' => 'local-brands']);
        
        Category::factory()->school()->topLevel()->create(['name' => 'Academic Programs', 'slug' => 'academic-programs']);
        Category::factory()->school()->topLevel()->create(['name' => 'Skills Training', 'slug' => 'skills-training']);
        
        Category::factory()->sustainability()->topLevel()->create(['name' => 'Eco-Friendly Products', 'slug' => 'eco-friendly-products']);
        Category::factory()->sustainability()->topLevel()->create(['name' => 'Renewable Energy', 'slug' => 'renewable-energy']);
        
        Category::factory()->music()->topLevel()->create(['name' => 'Genres', 'slug' => 'genres']);
        Category::factory()->music()->topLevel()->create(['name' => 'Instruments', 'slug' => 'instruments']);
    }
}
