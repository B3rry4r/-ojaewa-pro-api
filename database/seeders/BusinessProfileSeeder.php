<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\BusinessProfile;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BusinessProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first 4 users for creating sample businesses
        $users = User::take(4)->get();
        
        if ($users->count() < 4) {
            // If there aren't enough users, create some
            $missingCount = 4 - $users->count();
            for ($i = 0; $i < $missingCount; $i++) {
                $users->push(User::factory()->create());
            }
        }
        
        // Define the categories we need to create
        $categories = ['beauty', 'brand', 'school', 'music'];
        
        // Create one business of each category type
        foreach ($categories as $index => $category) {
            $user = $users[$index];
            
            // Base data for all business profiles
            $data = [
                'user_id' => $user->id,
                'category' => $category,
                'country' => fake()->country(),
                'state' => fake()->state(),
                'city' => fake()->city(),
                'address' => fake()->streetAddress(),
                'business_email' => fake()->companyEmail(),
                'business_phone_number' => fake()->phoneNumber(),
                'business_name' => fake()->company() . ' ' . ucfirst($category),
                'business_description' => fake()->paragraph(3),
                'store_status' => 'approved',
                'subscription_status' => 'active',
                'subscription_ends_at' => now()->addDays(30),
            ];
            
            // Add category-specific data
            if ($category === 'beauty') {
                $data = array_merge($data, [
                    'offering_type' => 'providing_service',
                    'service_list' => json_encode([
                        ['name' => 'Hair Styling', 'price' => 5000],
                        ['name' => 'Makeup', 'price' => 8000],
                        ['name' => 'Nail Art', 'price' => 3000]
                    ]),
                    'professional_title' => 'Beauty Expert',
                    'business_logo' => 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?w=400&h=400&fit=crop',
                    'instagram' => '@' . strtolower(str_replace(' ', '', $data['business_name'])),
                    'facebook' => strtolower(str_replace(' ', '', $data['business_name'])),
                ]);
            } elseif ($category === 'brand') {
                $data = array_merge($data, [
                    'offering_type' => 'selling_product',
                    'product_list' => json_encode([
                        ['name' => 'T-Shirt', 'price' => 2500],
                        ['name' => 'Jeans', 'price' => 6000],
                        ['name' => 'Sneakers', 'price' => 15000]
                    ]),
                    'business_certificates' => json_encode([
                        'business_registration.pdf',
                        'tax_clearance.pdf'
                    ]),
                    'business_logo' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=400&h=400&fit=crop',
                    'facebook' => strtolower(str_replace(' ', '', $data['business_name'])),
                    'website_url' => 'https://www.' . strtolower(str_replace(' ', '', $data['business_name'])) . '.com',
                    'instagram' => '@' . strtolower(str_replace(' ', '', $data['business_name'])),
                ]);
            } elseif ($category === 'school') {
                    'business_logo' => 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=400&h=400&fit=crop',
                $data = array_merge($data, [
                    'school_type' => fake()->randomElement(['fashion', 'music', 'catering', 'beauty']),
                    'school_biography' => fake()->paragraphs(2, true),
                    'classes_offered' => json_encode([
                        ['name' => 'Beginner Class', 'price' => 15000, 'duration' => '4 weeks'],
                        ['name' => 'Intermediate Class', 'price' => 25000, 'duration' => '8 weeks'],
                        ['name' => 'Advanced Class', 'price' => 40000, 'duration' => '12 weeks']
                    ]),
                    'website_url' => 'https://www.' . strtolower(str_replace(' ', '', $data['business_name'])) . '.edu',
                ]);
            } elseif ($category === 'music') {
                    'business_logo' => 'https://images.unsplash.com/photo-1511379938547-c1f69419868d?w=400&h=400&fit=crop',
                $data = array_merge($data, [
                    'music_category' => fake()->randomElement(['dj', 'artist', 'producer']),
                    'identity_document' => null, // Optional field, can be uploaded later
                    'youtube' => 'https://youtube.com/' . strtolower(str_replace(' ', '', $data['business_name'])),
                    'spotify' => 'https://spotify.com/artist/' . strtolower(str_replace(' ', '', $data['business_name'])),
                ]);
            }
            
            // Create the business profile
            BusinessProfile::create($data);
            
            // Output a message for the created business
            $this->command->info("Created {$category} business for user {$user->name}");
        }
    }
}
