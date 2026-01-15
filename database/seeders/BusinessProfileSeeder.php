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
        $categories = ['school', 'afro_beauty'];
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

        // For afro_beauty business profiles, we attach them to the SERVICES subtree
        $afroBeautyServices = Category::where('type', 'afro_beauty')
            ->where('slug', 'afro-beauty-services')
            ->with('children')
            ->first();

        // Create businesses per type with deterministic subcategories so subcategory screens differ
        foreach ($categories as $index => $category) {
            $user = $users[$index];
            
            // Base data for all business profiles
            $data = [
                'user_id' => $user->id,
                'category_id' => $category === 'afro_beauty'
                    ? optional($afroBeautyServices)->id
                    : optional($topLevelCategoryNodes[$category]?->first())->id,

                // subcategory: for afro_beauty, pick a service category; for school, pick first child
                'subcategory_id' => $category === 'afro_beauty'
                    ? optional($afroBeautyServices?->children()->orderBy('id')->first())->id
                    : optional(optional($topLevelCategoryNodes[$category]?->first())?->children()->orderBy('id')->first())->id,
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
            if ($category === 'afro_beauty') {
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
            $secondSubcategoryId = $category === 'afro_beauty'
                ? optional($afroBeautyServices?->children()->orderByDesc('id')->first())->id
                : optional(optional($topLevelCategoryNodes[$category]?->first())?->children()->orderByDesc('id')->first())->id;
            if ($secondSubcategoryId && $secondSubcategoryId !== $data['subcategory_id']) {
                $data2 = $data;
                $data2['business_name'] = fake()->company() . ' ' . ucfirst($category) . ' 2';
                $data2['business_email'] = fake()->companyEmail();
                $data2['subcategory_id'] = $secondSubcategoryId;
                BusinessProfile::create($data2);
            }
            
            // Output a message for the created business
            $this->command->info("Created {$category} businesses for user {$user->name}");
        }
    }
}
