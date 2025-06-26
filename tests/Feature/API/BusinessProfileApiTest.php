<?php

namespace Tests\Feature\API;

use App\Models\User;
use App\Models\BusinessProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BusinessProfileApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    
    private $user;
    private $businessData;
    private $token;
    private $headers;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a user
        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('test-token')->plainTextToken;
        $this->headers = ['Authorization' => 'Bearer ' . $this->token];
        
        // Sample business profile data
        $this->businessData = [
            'category' => 'beauty',
            'offering_type' => 'providing_service',
            'country' => 'Nigeria',
            'state' => 'Lagos',
            'city' => 'Ikeja',
            'address' => '123 Beauty Boulevard',
            'business_email' => 'beauty@example.com',
            'business_phone_number' => '08012345678',
            'business_name' => 'Glamour Beauty Palace',
            'business_description' => 'Premier beauty salon offering a wide range of services',
            'service_list' => json_encode([
                ['name' => 'Hair Styling', 'price' => 5000],
                ['name' => 'Makeup', 'price' => 8000]
            ]),
            'professional_title' => 'Beauty Expert',
            'instagram' => '@glamourbeauty',
        ];
    }
    
    /**
     * Test creating a new business profile.
     */
    public function test_can_create_business_profile(): void
    {
        $response = $this->withHeaders($this->headers)
            ->postJson('/api/business', $this->businessData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id',
                    'user_id',
                    'category',
                    'business_name',
                    'business_email',
                    'created_at',
                    'updated_at',
                ]
            ]);
            
        $this->assertDatabaseHas('business_profiles', [
            'user_id' => $this->user->id,
            'category' => 'beauty',
            'business_name' => 'Glamour Beauty Palace',
        ]);
    }
    
    /**
     * Test listing all business profiles for the authenticated user.
     */
    public function test_can_get_business_profiles(): void
    {
        // Create some business profiles for the user
        BusinessProfile::factory()->count(3)->create([
            'user_id' => $this->user->id
        ]);
        
        $response = $this->withHeaders($this->headers)
            ->getJson('/api/business');
            
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'user_id',
                        'category',
                        'business_name',
                        'created_at',
                        'updated_at',
                    ]
                ]
            ]);
    }
    
    /**
     * Test getting a specific business profile.
     */
    public function test_can_get_specific_business_profile(): void
    {
        // Create a business profile
        $business = BusinessProfile::factory()->create([
            'user_id' => $this->user->id,
            'business_name' => 'Test Business',
        ]);
        
        $response = $this->withHeaders($this->headers)
            ->getJson("/api/business/{$business->id}");
            
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id',
                    'user_id',
                    'category',
                    'business_name',
                    'business_email',
                    'created_at',
                    'updated_at',
                ]
            ])
            ->assertJson([
                'data' => [
                    'id' => $business->id,
                    'user_id' => $this->user->id,
                    'business_name' => 'Test Business',
                ]
            ]);
    }
    
    /**
     * Test updating a business profile.
     */
    public function test_can_update_business_profile(): void
    {
        $business = BusinessProfile::factory()->create([
            'user_id' => $this->user->id,
            'business_name' => 'Original Name',
        ]);
        
        $updateData = [
            'business_name' => 'Updated Name',
            'business_description' => 'Updated description',
        ];
        
        $response = $this->withHeaders($this->headers)
            ->putJson("/api/business/{$business->id}", $updateData);
            
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $business->id,
                    'business_name' => 'Updated Name',
                    'business_description' => 'Updated description',
                ]
            ]);
            
        $this->assertDatabaseHas('business_profiles', [
            'id' => $business->id,
            'business_name' => 'Updated Name',
            'business_description' => 'Updated description',
        ]);
    }
    
    /**
     * Test deleting a business profile.
     */
    public function test_can_delete_business_profile(): void
    {
        $business = BusinessProfile::factory()->create([
            'user_id' => $this->user->id,
        ]);
        
        $response = $this->withHeaders($this->headers)
            ->deleteJson("/api/business/{$business->id}");
            
        $response->assertStatus(200);
        
        $this->assertDatabaseMissing('business_profiles', [
            'id' => $business->id,
            'deleted_at' => null,
        ]);
    }
    
    /**
     * Test deactivating a business profile.
     */
    public function test_can_deactivate_business_profile(): void
    {
        $business = BusinessProfile::factory()->create([
            'user_id' => $this->user->id,
            'store_status' => 'approved',
        ]);
        
        $response = $this->withHeaders($this->headers)
            ->patchJson("/api/business/{$business->id}/deactivate");
            
        $response->assertStatus(200);
        
        $this->assertDatabaseHas('business_profiles', [
            'id' => $business->id,
            'store_status' => 'deactivated',
        ]);
    }
    
    /**
     * Test accessing another user's business profile is forbidden.
     */
    public function test_cannot_access_other_users_business_profile(): void
    {
        // Create another user and business profile
        $otherUser = User::factory()->create();
        $business = BusinessProfile::factory()->create([
            'user_id' => $otherUser->id,
        ]);
        
        // Try to access the other user's business profile
        $response = $this->withHeaders($this->headers)
            ->getJson("/api/business/{$business->id}");
            
        $response->assertStatus(403);
        
        // Try to update the other user's business profile
        $response = $this->withHeaders($this->headers)
            ->putJson("/api/business/{$business->id}", ['business_name' => 'Hacked Name']);
            
        $response->assertStatus(403);
        
        // Try to delete the other user's business profile
        $response = $this->withHeaders($this->headers)
            ->deleteJson("/api/business/{$business->id}");
            
        $response->assertStatus(403);
    }
    
    /**
     * Test validation for required fields when creating a business profile.
     */
    public function test_business_profile_creation_requires_fields(): void
    {
        // Test with empty data
        $response = $this->withHeaders($this->headers)
            ->postJson('/api/business', []);
            
        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'category', 
                'country', 
                'state',
                'city',
                'business_name',
                'business_description',
                'business_email',
                'business_phone_number',
            ]);
        
        // Test with invalid category
        $invalidData = $this->businessData;
        $invalidData['category'] = 'invalid_category';
        
        $response = $this->withHeaders($this->headers)
            ->postJson('/api/business', $invalidData);
            
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['category']);
    }
    
    /**
     * Test validation for offering_type based on category.
     */
    public function test_offering_type_validation_based_on_category(): void
    {
        // Test that when offering_type is providing_service, service_list is required
        $invalidData = $this->businessData;
        $invalidData['offering_type'] = 'providing_service';
        unset($invalidData['service_list']);
        
        $response = $this->withHeaders($this->headers)
            ->postJson('/api/business', $invalidData);
            
        $response->assertStatus(422);
        $errors = $response->json('errors');
        $this->assertArrayHasKey('service_list', $errors);
        
        // Test that when offering_type is selling_product, product_list is required
        $invalidData = $this->businessData;
        $invalidData['offering_type'] = 'selling_product';
        unset($invalidData['product_list']);
        
        $response = $this->withHeaders($this->headers)
            ->postJson('/api/business', $invalidData);
            
        $response->assertStatus(422);
        $errors = $response->json('errors');
        $this->assertArrayHasKey('product_list', $errors);
        
        // Test that when offering_type is selling_product, business_certificates is required
        $invalidData = $this->businessData;
        $invalidData['offering_type'] = 'selling_product';
        $invalidData['product_list'] = json_encode([['name' => 'Product 1', 'price' => 5000]]);
        unset($invalidData['business_certificates']);
        
        $response = $this->withHeaders($this->headers)
            ->postJson('/api/business', $invalidData);
            
        $response->assertStatus(422);
        $errors = $response->json('errors');
        $this->assertArrayHasKey('business_certificates', $errors);
    }
    
    /**
     * Test category-specific field validations.
     */
    public function test_category_specific_field_validations(): void
    {
        // Test that when offering_type is providing_service, professional_title is required
        $invalidData = $this->businessData;
        $invalidData['offering_type'] = 'providing_service';
        $invalidData['service_list'] = json_encode([['name' => 'Service 1', 'price' => 5000]]);
        unset($invalidData['professional_title']);
        
        $response = $this->withHeaders($this->headers)
            ->postJson('/api/business', $invalidData);
            
        $response->assertStatus(422);
        $errors = $response->json('errors');
        $this->assertArrayHasKey('professional_title', $errors);
        
        // Test that school category requires school_type
        $schoolData = $this->businessData;
        $schoolData['category'] = 'school';
        // No school_type provided
        
        $response = $this->withHeaders($this->headers)
            ->postJson('/api/business', $schoolData);
            
        $response->assertStatus(422);
        $errors = $response->json('errors');
        $this->assertArrayHasKey('school_type', $errors);
        
        // Test that school category requires school_biography
        $schoolData = $this->businessData;
        $schoolData['category'] = 'school';
        $schoolData['school_type'] = 'fashion';
        // No school_biography provided
        
        $response = $this->withHeaders($this->headers)
            ->postJson('/api/business', $schoolData);
            
        $response->assertStatus(422);
        $errors = $response->json('errors');
        $this->assertArrayHasKey('school_biography', $errors);
        
        // Test that music category requires music_category
        $musicData = $this->businessData;
        $musicData['category'] = 'music';
        // No music_category provided
        
        $response = $this->withHeaders($this->headers)
            ->postJson('/api/business', $musicData);
            
        $response->assertStatus(422);
        $errors = $response->json('errors');
        $this->assertArrayHasKey('music_category', $errors);
    }
    
    /**
     * Test uploading business logo file.
     */
    public function test_can_upload_business_logo(): void
    {
        Storage::fake('public');
        
        $business = BusinessProfile::factory()->create([
            'user_id' => $this->user->id,
        ]);
        
        $file = UploadedFile::fake()->image('logo.jpg');
        
        $response = $this->withHeaders($this->headers)
            ->postJson("/api/business/{$business->id}/upload", [
                'file_type' => 'business_logo',
                'file' => $file,
            ]);
            
        $response->assertStatus(200);
        
        // Check that the file was stored
        $path = 'business_logos/' . $file->hashName();
        $this->assertTrue(Storage::disk('public')->exists($path));
        
        // Check that the business profile was updated with the logo path
        $this->assertDatabaseHas('business_profiles', [
            'id' => $business->id,
            'business_logo' => 'storage/' . $path,
        ]);
    }
    
    /**
     * Test uploading business documents.
     */
    public function test_can_upload_business_document(): void
    {
        Storage::fake('public');
        
        $business = BusinessProfile::factory()->create([
            'user_id' => $this->user->id,
            'category' => 'brand',
        ]);
        
        $file = UploadedFile::fake()->create('certificate.pdf', 500);
        
        $response = $this->withHeaders($this->headers)
            ->postJson("/api/business/{$business->id}/upload", [
                'file_type' => 'business_certificates',
                'file' => $file,
            ]);
            
        $response->assertStatus(200);
        
        // Verify file was stored
        $path = 'business_documents/' . $file->hashName();
        $this->assertTrue(Storage::disk('public')->exists($path));
    }
    
    /**
     * Test that unauthorized users cannot access business profiles.
     */
    public function test_unauthenticated_users_cannot_access_business_profiles(): void
    {
        // Try to access API without authentication
        $response = $this->getJson('/api/business');
        $response->assertStatus(401);
        
        // Create a business profile record
        $business = BusinessProfile::factory()->create();
        
        // Try different operations without authentication
        $this->getJson("/api/business/{$business->id}")->assertStatus(401);
        $this->postJson('/api/business', $this->businessData)->assertStatus(401);
        $this->putJson("/api/business/{$business->id}", ['business_name' => 'Updated'])->assertStatus(401);
        $this->deleteJson("/api/business/{$business->id}")->assertStatus(401);
    }
}
