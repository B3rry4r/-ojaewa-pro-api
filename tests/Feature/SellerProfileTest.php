<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\SellerProfile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class SellerProfileTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_user_can_create_seller_profile(): void
    {
        $profileData = [
            'country' => 'Nigeria',
            'state' => 'Lagos',
            'city' => 'Lagos',
            'address' => '123 Test Street',
            'business_email' => 'business@test.com',
            'business_phone_number' => '+234123456789',
            'instagram' => 'test_business',
            'facebook' => 'test_business_fb',
            'business_name' => 'Test Business Ltd',
            'business_registration_number' => 'REG123456789',
            'bank_name' => 'First Bank',
            'account_number' => '1234567890',
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/seller/profile', $profileData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'user_id',
                'country',
                'state',
                'city',
                'address',
                'business_email',
                'business_phone_number',
                'business_name',
                'created_at',
                'updated_at'
            ]);

        $this->assertDatabaseHas('seller_profiles', [
            'user_id' => $this->user->id,
            'business_email' => 'business@test.com',
            'registration_status' => 'pending'
        ]);
    }

    public function test_user_cannot_create_multiple_seller_profiles(): void
    {
        SellerProfile::factory()->create(['user_id' => $this->user->id]);

        $profileData = [
            'country' => 'Nigeria',
            'state' => 'Lagos',
            'city' => 'Lagos',
            'address' => '123 Test Street',
            'business_email' => 'business@test.com',
            'business_phone_number' => '+234123456789',
            'business_name' => 'Test Business Ltd',
            'business_registration_number' => 'REG123456789',
            'bank_name' => 'First Bank',
            'account_number' => '1234567890',
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/seller/profile', $profileData);

        $response->assertStatus(409)
            ->assertJson(['message' => 'User already has a seller profile']);
    }

    public function test_user_can_view_their_seller_profile(): void
    {
        $sellerProfile = SellerProfile::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/seller/profile');

        $response->assertStatus(200)
            ->assertJson([
                'id' => $sellerProfile->id,
                'user_id' => $this->user->id,
                'business_name' => $sellerProfile->business_name,
            ]);
    }

    public function test_user_gets_404_when_no_seller_profile_exists(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/seller/profile');

        $response->assertStatus(404)
            ->assertJson(['message' => 'Seller profile not found']);
    }

    public function test_user_can_update_their_seller_profile(): void
    {
        $sellerProfile = SellerProfile::factory()->create(['user_id' => $this->user->id]);

        $updateData = [
            'business_name' => 'Updated Business Name',
            'city' => 'Updated City',
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson('/api/seller/profile', $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'business_name' => 'Updated Business Name',
                'city' => 'Updated City',
            ]);

        $this->assertDatabaseHas('seller_profiles', [
            'id' => $sellerProfile->id,
            'business_name' => 'Updated Business Name',
            'city' => 'Updated City',
        ]);
    }

    public function test_user_can_soft_delete_their_seller_profile(): void
    {
        $sellerProfile = SellerProfile::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson('/api/seller/profile', ['reason' => 'No longer selling']);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Seller profile deleted successfully']);

        $this->assertSoftDeleted('seller_profiles', ['id' => $sellerProfile->id]);
    }

    public function test_user_can_upload_files_to_seller_profile(): void
    {
        $sellerProfile = SellerProfile::factory()->create(['user_id' => $this->user->id]);
        
        Storage::fake('local');
        $file = UploadedFile::fake()->image('test-logo.jpg');

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/seller/profile/upload', [
                'file' => $file,
                'type' => 'business_logo'
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'file_path',
                'type'
            ])
            ->assertJson([
                'message' => 'File uploaded successfully',
                'type' => 'business_logo'
            ]);

        // Check that the seller profile was updated with the file path
        $sellerProfile->refresh();
        $this->assertNotNull($sellerProfile->business_logo);
        $this->assertStringContainsString('spaces/business_logo/', $sellerProfile->business_logo);
    }

    public function test_unauthenticated_user_cannot_access_seller_profile_endpoints(): void
    {
        $responses = [
            $this->getJson('/api/seller/profile'),
            $this->postJson('/api/seller/profile', []),
            $this->putJson('/api/seller/profile', []),
            $this->deleteJson('/api/seller/profile'),
            $this->postJson('/api/seller/profile/upload', []),
        ];

        foreach ($responses as $response) {
            $response->assertStatus(401);
        }
    }

    public function test_seller_profile_validation_rules(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/seller/profile', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'country',
                'state',
                'city',
                'address',
                'business_email',
                'business_phone_number',
                'business_name',
                'business_registration_number',
                'bank_name',
                'account_number',
            ]);
    }
}
