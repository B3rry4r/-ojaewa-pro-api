<?php

namespace Tests\Feature;

use App\Models\Faq;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FaqTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_all_faqs()
    {
        Faq::factory()->count(5)->create();

        $response = $this->getJson('/api/faqs');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'data' => [
                        '*' => [
                            'id',
                            'question',
                            'answer',
                            'category',
                            'created_at',
                            'updated_at'
                        ]
                    ]
                ]
            ])
            ->assertJsonCount(5, 'data.data');
    }

    public function test_can_filter_faqs_by_category()
    {
        Faq::factory()->create(['category' => 'general']);
        Faq::factory()->create(['category' => 'billing']);
        Faq::factory()->create(['category' => 'general']);
        Faq::factory()->create(['category' => 'technical']);

        $response = $this->getJson('/api/faqs?category=general');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data.data');

        $faqs = $response->json('data.data');
        foreach ($faqs as $faq) {
            $this->assertEquals('general', $faq['category']);
        }
    }

    public function test_can_get_faq_categories()
    {
        Faq::factory()->create(['category' => 'general']);
        Faq::factory()->create(['category' => 'billing']);
        Faq::factory()->create(['category' => 'general']); // duplicate
        Faq::factory()->create(['category' => 'technical']);
        Faq::factory()->create(['category' => null]); // no category

        $response = $this->getJson('/api/faqs/categories');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data'
            ]);

        $categories = $response->json('data');
        $this->assertContains('general', $categories);
        $this->assertContains('billing', $categories);
        $this->assertContains('technical', $categories);
        $this->assertCount(3, $categories); // Should not include duplicates or null
    }

    public function test_can_search_faqs()
    {
        Faq::factory()->create([
            'question' => 'How to reset password?',
            'answer' => 'Click on forgot password link'
        ]);
        
        Faq::factory()->create([
            'question' => 'How to update profile?',
            'answer' => 'Go to settings and update your information'
        ]);
        
        Faq::factory()->create([
            'question' => 'Payment methods',
            'answer' => 'We accept credit cards and PayPal'
        ]);

        $response = $this->getJson('/api/faqs/search?query=password');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.data')
            ->assertJsonFragment(['question' => 'How to reset password?']);
    }

    public function test_search_works_on_answer_content()
    {
        Faq::factory()->create([
            'question' => 'Payment options',
            'answer' => 'We accept credit cards and PayPal payments'
        ]);
        
        Faq::factory()->create([
            'question' => 'Shipping info',
            'answer' => 'We ship worldwide within 3-5 business days'
        ]);

        $response = $this->getJson('/api/faqs/search?query=PayPal');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.data')
            ->assertJsonFragment(['question' => 'Payment options']);
    }

    public function test_search_requires_minimum_query_length()
    {
        $response = $this->getJson('/api/faqs/search?query=ab');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['query']);
    }

    public function test_search_requires_query_parameter()
    {
        $response = $this->getJson('/api/faqs/search');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['query']);
    }

    public function test_can_get_specific_faq_by_id()
    {
        $faq = Faq::factory()->create([
            'question' => 'Test Question',
            'answer' => 'Test Answer'
        ]);

        $response = $this->getJson('/api/faqs/' . $faq->id);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'id' => $faq->id,
                    'question' => 'Test Question',
                    'answer' => 'Test Answer'
                ]
            ]);
    }

    public function test_nonexistent_faq_returns_404()
    {
        $response = $this->getJson('/api/faqs/99999');

        $response->assertStatus(404)
            ->assertJson([
                'status' => 'error',
                'message' => 'FAQ not found'
            ]);
    }

    public function test_faqs_are_ordered_by_latest()
    {
        $oldFaq = Faq::factory()->create(['created_at' => now()->subDay()]);
        $newFaq = Faq::factory()->create(['created_at' => now()]);

        $response = $this->getJson('/api/faqs');

        $response->assertStatus(200);
        
        $faqs = $response->json('data.data');
        $this->assertEquals($newFaq->id, $faqs[0]['id']);
        $this->assertEquals($oldFaq->id, $faqs[1]['id']);
    }

    public function test_empty_category_filter_returns_all_faqs()
    {
        Faq::factory()->count(3)->create();

        $response = $this->getJson('/api/faqs?category=');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data.data');
    }

    public function test_faqs_are_publicly_accessible()
    {
        Faq::factory()->create();

        // Test without authentication
        $response = $this->getJson('/api/faqs');
        $response->assertStatus(200);

        $response = $this->getJson('/api/faqs/categories');
        $response->assertStatus(200);

        $response = $this->getJson('/api/faqs/search?query=test');
        $response->assertStatus(200);
    }
}
