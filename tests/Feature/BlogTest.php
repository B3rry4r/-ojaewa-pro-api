<?php

namespace Tests\Feature;

use App\Models\Blog;
use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BlogTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = Admin::factory()->create();
    }

    public function test_can_get_published_blogs()
    {
        // Create published blogs
        Blog::factory()->count(3)->create([
            'admin_id' => $this->admin->id,
            'published_at' => now()->subDay(),
        ]);

        // Create unpublished blog
        Blog::factory()->create([
            'admin_id' => $this->admin->id,
            'published_at' => null,
        ]);

        $response = $this->getJson('/api/blogs');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'data' => [
                        '*' => [
                            'id',
                            'title',
                            'slug',
                            'body',
                            'featured_image',
                            'published_at',
                            'admin',
                            'created_at',
                            'updated_at'
                        ]
                    ]
                ]
            ])
            ->assertJsonCount(3, 'data.data');
    }

    public function test_can_get_blog_by_slug()
    {
        $blog = Blog::factory()->create([
            'admin_id' => $this->admin->id,
            'title' => 'Test Blog Post',
            'published_at' => now()->subDay(),
        ]);

        $response = $this->getJson('/api/blogs/' . $blog->slug);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'id' => $blog->id,
                    'title' => $blog->title,
                    'slug' => $blog->slug,
                ]
            ]);
    }

    public function test_cannot_get_unpublished_blog_by_slug()
    {
        $blog = Blog::factory()->create([
            'admin_id' => $this->admin->id,
            'published_at' => null,
        ]);

        $response = $this->getJson('/api/blogs/' . $blog->slug);

        $response->assertStatus(404)
            ->assertJson([
                'status' => 'error',
                'message' => 'Blog post not found'
            ]);
    }

    public function test_can_get_latest_blogs()
    {
        // Create blogs with different published dates
        Blog::factory()->create([
            'admin_id' => $this->admin->id,
            'published_at' => now()->subDays(3),
        ]);
        Blog::factory()->create([
            'admin_id' => $this->admin->id,
            'published_at' => now()->subDay(),
        ]);
        Blog::factory()->create([
            'admin_id' => $this->admin->id,
            'published_at' => now()->subHour(),
        ]);

        $response = $this->getJson('/api/blogs/latest');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'slug',
                        'published_at',
                        'admin'
                    ]
                ]
            ])
            ->assertJsonCount(3, 'data');
    }

    public function test_can_search_blogs()
    {
        Blog::factory()->create([
            'admin_id' => $this->admin->id,
            'title' => 'Laravel Tutorial',
            'body' => 'This is a comprehensive guide to Laravel',
            'published_at' => now()->subDay(),
        ]);

        Blog::factory()->create([
            'admin_id' => $this->admin->id,
            'title' => 'PHP Best Practices',
            'body' => 'Learn about PHP coding standards',
            'published_at' => now()->subDay(),
        ]);

        $response = $this->getJson('/api/blogs/search?query=Laravel');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.data')
            ->assertJsonFragment(['title' => 'Laravel Tutorial']);
    }

    public function test_search_requires_minimum_query_length()
    {
        $response = $this->getJson('/api/blogs/search?query=ab');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['query']);
    }

    public function test_search_requires_query_parameter()
    {
        $response = $this->getJson('/api/blogs/search');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['query']);
    }

    public function test_blog_includes_admin_relationship()
    {
        $blog = Blog::factory()->create([
            'admin_id' => $this->admin->id,
            'published_at' => now()->subDay(),
        ]);

        $response = $this->getJson('/api/blogs/' . $blog->slug);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'admin' => [
                        'id',
                        'firstname',
                        'lastname'
                    ]
                ]
            ]);
    }

    public function test_blogs_are_ordered_by_published_date()
    {
        $oldBlog = Blog::factory()->create([
            'admin_id' => $this->admin->id,
            'published_at' => now()->subDays(2),
        ]);

        $newBlog = Blog::factory()->create([
            'admin_id' => $this->admin->id,
            'published_at' => now()->subHour(),
        ]);

        $response = $this->getJson('/api/blogs');

        $response->assertStatus(200);
        
        $blogs = $response->json('data.data');
        $this->assertEquals($newBlog->id, $blogs[0]['id']);
        $this->assertEquals($oldBlog->id, $blogs[1]['id']);
    }

    public function test_nonexistent_blog_slug_returns_404()
    {
        $response = $this->getJson('/api/blogs/nonexistent-slug');

        $response->assertStatus(404)
            ->assertJson([
                'status' => 'error',
                'message' => 'Blog post not found'
            ]);
    }
}
