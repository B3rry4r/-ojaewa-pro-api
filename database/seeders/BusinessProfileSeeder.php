<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\BusinessProfile;
use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BusinessProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define the categories we need to create
        // Business profiles represent service providers (schools + afro beauty services)
        $categories = ['school', 'afro_beauty_services'];
        $neededUsers = count($categories);

        // Get first N users for creating sample businesses
        $users = User::take($neededUsers)->get();
        
        if ($users->count() < $neededUsers) {
            // If there aren't enough users, create some
            $missingCount = $neededUsers - $users->count();
            for ($i = 0; $i < $missingCount; $i++) {
                $users->push(User::factory()->create());
            }
        }

        // Preload top-level category nodes for each type
        $topLevelCategoryNodes = Category::whereNull('parent_id')
            ->whereIn('type', $categories)
            ->get()
            ->groupBy('type');

        // Leaf category pools for business directories (2 levels only)
        $afroBeautyServiceLeafIds = Category::where('type', 'afro_beauty_services')->pluck('id')->toArray();
        $schoolLeafIds = Category::where('type', 'school')->pluck('id')->toArray();

        // Ensure the fixed test user always has an approved store
        $testUser = User::where('email', 'test@ojaewa.com')->first();
        if ($testUser && !BusinessProfile::where('user_id', $testUser->id)->exists()) {
            $leafId = $afroBeautyServiceLeafIds[array_rand($afroBeautyServiceLeafIds)] ?? ($schoolLeafIds[array_rand($schoolLeafIds)] ?? null);
            if ($leafId) {
                BusinessProfile::create([
                    'user_id' => $testUser->id,
                    'category_id' => $leafId,
                    'subcategory_id' => null,
                    'category' => 'afro_beauty_services',
                    'country' => 'Nigeria',
                    'state' => 'Lagos',
                    'city' => 'Lagos',
                    'address' => '123 Test Business Street',
                    'business_email' => 'teststore@ojaewa.com',
                    'business_phone_number' => '+2348012345678',
                    'business_name' => 'Test User Approved Store',
                    'business_description' => 'Approved test store for test@ojaewa.com',
                    'store_status' => 'approved',
                    'subscription_status' => 'active',
                    'subscription_ends_at' => now()->addDays(30),
                    'offering_type' => 'providing_service',
                    'service_list' => json_encode([
                        ['name' => 'Hair Styling', 'price' => 5000],
                        ['name' => 'Makeup', 'price' => 8000]
                    ]),
                    'professional_title' => 'Test Stylist',
                ]);
                $this->command->info('✓ Created approved store for test@ojaewa.com');
            }
        }

        // Create businesses per type with deterministic subcategories so subcategory screens differ
        foreach ($categories as $index => $category) {
            $user = $users[$index];
            
            // Base data for all business profiles
            $data = [
                'user_id' => $user->id,
                'category_id' => match ($category) {
                    'afro_beauty_services' => $afroBeautyServiceLeafIds[array_rand($afroBeautyServiceLeafIds)] ?? null,
                    'school' => $schoolLeafIds[array_rand($schoolLeafIds)] ?? null,
                    default => null,
                },

                'subcategory_id' => null,
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
            if ($category === 'afro_beauty_services') {
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
            } elseif ($category === 'school') {
                $data = array_merge($data, [
                    'business_logo' => 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=400&h=400&fit=crop',
                    'school_type' => fake()->randomElement(['fashion', 'music', 'catering', 'beauty']),
                    'school_biography' => fake()->paragraphs(2, true),
                    'classes_offered' => json_encode([
                        ['name' => 'Beginner Class', 'price' => 15000, 'duration' => '4 weeks'],
                        ['name' => 'Intermediate Class', 'price' => 25000, 'duration' => '8 weeks'],
                        ['name' => 'Advanced Class', 'price' => 40000, 'duration' => '12 weeks']
                    ]),
                    'website_url' => 'https://www.' . strtolower(str_replace(' ', '', $data['business_name'])) . '.edu',
                ]);
            }
            
            // Create the business profile
            BusinessProfile::create($data);

            // Create a second business under a different subcategory (if available)
            // Optionally create a second business in a different leaf for richer data
            $secondLeafId = match ($category) {
                'afro_beauty_services' => $afroBeautyServiceLeafIds[array_rand($afroBeautyServiceLeafIds)] ?? null,
                'school' => $schoolLeafIds[array_rand($schoolLeafIds)] ?? null,
                default => null,
            };

            if ($secondLeafId && $secondLeafId !== $data['category_id']) {
                $data2 = $data;
                $data2['business_name'] = fake()->company() . ' ' . ucfirst($category) . ' 2';
                $data2['business_email'] = fake()->companyEmail();
                $data2['category_id'] = $secondLeafId;
                BusinessProfile::create($data2);
            }
            
            // Output a message for the created business
            $this->command->info("Created {$category} businesses for user {$user->name}");
        }
    }
}
