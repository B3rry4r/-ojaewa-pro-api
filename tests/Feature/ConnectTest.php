<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ConnectTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_all_connect_links()
    {
        $response = $this->getJson('/api/connect');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'social_links' => [
                        'facebook',
                        'twitter',
                        'instagram',
                        'linkedin',
                        'youtube',
                        'tiktok'
                    ],
                    'contact' => [
                        'email',
                        'phone',
                        'address',
                        'website'
                    ],
                    'app_links' => [
                        'ios',
                        'android',
                        'web'
                    ]
                ]
            ])
            ->assertJson([
                'status' => 'success'
            ]);
    }

    public function test_can_get_social_links_only()
    {
        $response = $this->getJson('/api/connect/social');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'facebook',
                    'twitter',
                    'instagram',
                    'linkedin',
                    'youtube',
                    'tiktok'
                ]
            ])
            ->assertJson([
                'status' => 'success'
            ]);

        // Verify it contains expected social media URLs
        $data = $response->json('data');
        $this->assertArrayHasKey('facebook', $data);
        $this->assertArrayHasKey('twitter', $data);
        $this->assertArrayHasKey('instagram', $data);
    }

    public function test_can_get_contact_info_only()
    {
        $response = $this->getJson('/api/connect/contact');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'email',
                    'phone',
                    'address',
                    'website'
                ]
            ])
            ->assertJson([
                'status' => 'success'
            ]);

        // Verify it contains expected contact information
        $data = $response->json('data');
        $this->assertArrayHasKey('email', $data);
        $this->assertArrayHasKey('phone', $data);
        $this->assertArrayHasKey('address', $data);
        $this->assertArrayHasKey('website', $data);
    }

    public function test_can_get_app_links_only()
    {
        $response = $this->getJson('/api/connect/app-links');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'ios',
                    'android',
                    'web'
                ]
            ])
            ->assertJson([
                'status' => 'success'
            ]);

        // Verify it contains expected app download links
        $data = $response->json('data');
        $this->assertArrayHasKey('ios', $data);
        $this->assertArrayHasKey('android', $data);
        $this->assertArrayHasKey('web', $data);
    }

    public function test_connect_endpoints_are_publicly_accessible()
    {
        // Test all endpoints without authentication
        $response = $this->getJson('/api/connect');
        $response->assertStatus(200);

        $response = $this->getJson('/api/connect/social');
        $response->assertStatus(200);

        $response = $this->getJson('/api/connect/contact');
        $response->assertStatus(200);

        $response = $this->getJson('/api/connect/app-links');
        $response->assertStatus(200);
    }

    public function test_connect_links_contain_expected_structure()
    {
        $response = $this->getJson('/api/connect');
        
        $response->assertStatus(200);
        $data = $response->json('data');

        // Verify main structure
        $this->assertArrayHasKey('social_links', $data);
        $this->assertArrayHasKey('contact', $data);
        $this->assertArrayHasKey('app_links', $data);

        // Verify social links structure
        $socialLinks = $data['social_links'];
        $expectedSocialPlatforms = ['facebook', 'twitter', 'instagram', 'linkedin', 'youtube', 'tiktok'];
        foreach ($expectedSocialPlatforms as $platform) {
            $this->assertArrayHasKey($platform, $socialLinks);
        }

        // Verify contact structure
        $contact = $data['contact'];
        $expectedContactFields = ['email', 'phone', 'address', 'website'];
        foreach ($expectedContactFields as $field) {
            $this->assertArrayHasKey($field, $contact);
        }

        // Verify app links structure
        $appLinks = $data['app_links'];
        $expectedAppPlatforms = ['ios', 'android', 'web'];
        foreach ($expectedAppPlatforms as $platform) {
            $this->assertArrayHasKey($platform, $appLinks);
        }
    }

    public function test_social_links_have_valid_urls()
    {
        $response = $this->getJson('/api/connect/social');
        
        $response->assertStatus(200);
        $socialLinks = $response->json('data');

        // Check that social links contain URLs (basic validation)
        foreach ($socialLinks as $platform => $url) {
            if (!empty($url)) {
                $this->assertStringContainsString('http', $url, "Social link for {$platform} should be a valid URL");
            }
        }
    }

    public function test_contact_info_has_expected_format()
    {
        $response = $this->getJson('/api/connect/contact');
        
        $response->assertStatus(200);
        $contact = $response->json('data');

        // Basic validation for contact information format
        if (!empty($contact['email'])) {
            $this->assertStringContainsString('@', $contact['email'], 'Email should contain @ symbol');
        }
        
        if (!empty($contact['website'])) {
            $this->assertStringContainsString('http', $contact['website'], 'Website should be a valid URL');
        }
    }

    public function test_app_links_have_valid_urls()
    {
        $response = $this->getJson('/api/connect/app-links');
        
        $response->assertStatus(200);
        $appLinks = $response->json('data');

        // Check that app links contain URLs (basic validation)
        foreach ($appLinks as $platform => $url) {
            if (!empty($url)) {
                $this->assertStringContainsString('http', $url, "App link for {$platform} should be a valid URL");
            }
        }
    }

    public function test_connect_endpoints_return_consistent_response_format()
    {
        $endpoints = [
            '/api/connect',
            '/api/connect/social',
            '/api/connect/contact',
            '/api/connect/app-links'
        ];

        foreach ($endpoints as $endpoint) {
            $response = $this->getJson($endpoint);
            
            $response->assertStatus(200)
                ->assertJsonStructure([
                    'status',
                    'data'
                ])
                ->assertJson([
                    'status' => 'success'
                ]);
        }
    }
}
