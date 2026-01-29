<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\BusinessProfile;
use App\Models\Category;
use Illuminate\Database\Seeder;

class BusinessProfileSeeder extends Seeder
{
    /**
     * Business images by category
     */
    private array $businessImages = [
        'afro_beauty_services' => [
            'default' => [
                'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?w=400&h=400&fit=crop',
            ],
        ],
        'school' => [
            'Fashion' => [
                'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=400&h=400&fit=crop',
                'https://images.unsplash.com/photo-1537832816519-689ad163d0c4?w=400&h=400&fit=crop',
            ],
            'Music' => [
                'https://images.unsplash.com/photo-1507838153414-b4b713384a76?w=400&h=400&fit=crop',
                'https://images.unsplash.com/photo-1511379938547-c1f69419868d?w=400&h=400&fit=crop',
            ],
            'Catering' => [
                'https://images.unsplash.com/photo-1556910103-1c02745aae4d?w=400&h=400&fit=crop',
                'https://images.unsplash.com/photo-1577219491135-ce391730fb2c?w=400&h=400&fit=crop',
            ],
            'Beauty' => [
                'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?w=400&h=400&fit=crop',
                'https://images.unsplash.com/photo-1560066984-138dadb4c035?w=400&h=400&fit=crop',
            ],
            'default' => [
                'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=400&h=400&fit=crop',
            ],
        ],
    ];

    /**
     * Sample business data
     */
    private array $beautyBusinesses = [
        [
            'name' => 'Afro Glam Beauty Studio',
            'description' => 'Premier African beauty salon specializing in natural hair care, braiding, locs, and traditional African hairstyles. Our expert stylists bring out your natural beauty with culturally-inspired techniques.',
            'services' => [
                ['name' => 'Box Braids', 'price' => 15000],
                ['name' => 'Cornrows', 'price' => 8000],
                ['name' => 'Locs Installation', 'price' => 25000],
                ['name' => 'Natural Hair Treatment', 'price' => 12000],
                ['name' => 'Silk Press', 'price' => 10000],
            ],
            'title' => 'Master Hair Stylist',
        ],
        [
            'name' => 'Melanin Glow Aesthetics',
            'description' => 'Specialized skincare services for melanin-rich skin. We offer facials, chemical peels, and treatments specifically formulated for African skin types.',
            'services' => [
                ['name' => 'Melanin Facial', 'price' => 20000],
                ['name' => 'Hyperpigmentation Treatment', 'price' => 35000],
                ['name' => 'Glow Up Package', 'price' => 45000],
                ['name' => 'Skin Analysis', 'price' => 5000],
            ],
            'title' => 'Licensed Aesthetician',
        ],
        [
            'name' => 'Queens Touch MUA',
            'description' => 'Professional makeup artistry celebrating African beauty. Specializing in bridal makeup, editorial looks, and traditional ceremony makeup.',
            'services' => [
                ['name' => 'Bridal Makeup', 'price' => 50000],
                ['name' => 'Traditional Wedding Makeup', 'price' => 40000],
                ['name' => 'Editorial Makeup', 'price' => 35000],
                ['name' => 'Party Makeup', 'price' => 20000],
                ['name' => 'Makeup Lesson', 'price' => 25000],
            ],
            'title' => 'Celebrity Makeup Artist',
        ],
        [
            'name' => 'Kings Barbershop Lagos',
            'description' => 'Premium barbershop offering classic and contemporary cuts for African men. Specializing in fades, lineups, and beard grooming.',
            'services' => [
                ['name' => 'Classic Haircut', 'price' => 3000],
                ['name' => 'Fade with Design', 'price' => 5000],
                ['name' => 'Beard Trim & Shape', 'price' => 2500],
                ['name' => 'Hot Towel Shave', 'price' => 4000],
                ['name' => 'VIP Package', 'price' => 10000],
            ],
            'title' => 'Master Barber',
        ],
        [
            'name' => 'Natural Roots Wellness',
            'description' => 'Holistic beauty and wellness center combining traditional African healing with modern techniques. Offering massages, body treatments, and spiritual wellness.',
            'services' => [
                ['name' => 'African Hot Stone Massage', 'price' => 25000],
                ['name' => 'Shea Butter Body Wrap', 'price' => 20000],
                ['name' => 'Detox Treatment', 'price' => 30000],
                ['name' => 'Aromatherapy Session', 'price' => 15000],
            ],
            'title' => 'Wellness Therapist',
        ],
    ];

    private array $schools = [
        [
            'name' => 'African Fashion Academy',
            'description' => 'Leading fashion school teaching African textile design, pattern making, and contemporary African fashion. Our graduates work with top African designers.',
            'type' => 'fashion',
            'classes' => [
                ['name' => 'Introduction to African Textiles', 'price' => 50000, 'duration' => '4 weeks'],
                ['name' => 'Pattern Making & Cutting', 'price' => 80000, 'duration' => '8 weeks'],
                ['name' => 'Advanced Fashion Design', 'price' => 150000, 'duration' => '12 weeks'],
                ['name' => 'Business of Fashion', 'price' => 45000, 'duration' => '4 weeks'],
            ],
            'bio' => 'Established in 2015, African Fashion Academy has trained over 500 fashion designers who now work across Africa and internationally.',
        ],
        [
            'name' => 'Afrobeat Music Institute',
            'description' => 'Learn African music from masters. We teach traditional drumming, Afrobeat, Highlife, and contemporary African music production.',
            'type' => 'music',
            'classes' => [
                ['name' => 'Traditional Drumming', 'price' => 40000, 'duration' => '6 weeks'],
                ['name' => 'Afrobeat Production', 'price' => 100000, 'duration' => '10 weeks'],
                ['name' => 'Voice Training', 'price' => 60000, 'duration' => '8 weeks'],
                ['name' => 'Music Business', 'price' => 35000, 'duration' => '4 weeks'],
            ],
            'bio' => 'Founded by veteran musicians, we preserve and promote African musical heritage while embracing modern production techniques.',
        ],
        [
            'name' => 'Taste of Africa Culinary School',
            'description' => 'Master the art of African cuisine. From West African jollof to East African pilau, learn authentic recipes from experienced chefs.',
            'type' => 'catering',
            'classes' => [
                ['name' => 'West African Cuisine', 'price' => 45000, 'duration' => '4 weeks'],
                ['name' => 'East African Specialties', 'price' => 45000, 'duration' => '4 weeks'],
                ['name' => 'Professional Catering', 'price' => 120000, 'duration' => '12 weeks'],
                ['name' => 'African Pastry & Desserts', 'price' => 55000, 'duration' => '6 weeks'],
            ],
            'bio' => 'Our school celebrates the rich culinary traditions of Africa. Students learn from award-winning chefs in our state-of-the-art kitchen.',
        ],
        [
            'name' => 'Glamour Pro Beauty Academy',
            'description' => 'Professional beauty training school offering certification in hair styling, makeup artistry, and skincare for African beauty standards.',
            'type' => 'beauty',
            'classes' => [
                ['name' => 'Hair Styling Certificate', 'price' => 150000, 'duration' => '16 weeks'],
                ['name' => 'Makeup Artistry Diploma', 'price' => 180000, 'duration' => '20 weeks'],
                ['name' => 'Skincare Specialist', 'price' => 200000, 'duration' => '24 weeks'],
                ['name' => 'Nail Technology', 'price' => 80000, 'duration' => '8 weeks'],
            ],
            'bio' => 'Accredited beauty school with job placement assistance. 95% of our graduates find employment within 3 months.',
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        
        if ($users->count() < 6) {
            $this->command->warn('Need at least 6 users for business profiles. Creating additional users.');
            while ($users->count() < 6) {
                $users->push(User::factory()->create());
            }
        }

        // Get category IDs
        $schoolCategories = Category::where('type', 'school')->get()->keyBy('name');

        $totalCreated = 0;
        $userIndex = 0;

        // Skip afro beauty services for now

        // Create schools
        $testUser = User::where('email', 'test@ojaewa.com')->first();
        if ($testUser && !BusinessProfile::where('user_id', $testUser->id)->exists()) {
            $schoolCategory = $schoolCategories->first();
            if ($schoolCategory) {
                BusinessProfile::create([
                    'user_id' => $testUser->id,
                    'category_id' => $schoolCategory->id,
                    'category' => 'school',
                    'country' => 'Nigeria',
                    'state' => 'Lagos State',
                    'city' => 'Lagos',
                    'address' => '25 Admiralty Way, Lekki Phase 1',
                    'business_email' => 'school@ojaewa.com',
                    'business_phone_number' => '+2348012345678',
                    'business_name' => 'OjaEwa School Test',
                    'business_description' => 'Seeded school business profile',
                    'store_status' => 'approved',
                    'subscription_status' => 'active',
                    'subscription_ends_at' => now()->addDays(60),
                    'school_type' => 'fashion',
                    'school_biography' => 'Seeded school biography',
                    'classes_offered' => json_encode([
                        ['name' => 'Beginner Class', 'price' => 15000, 'duration' => '4 weeks'],
                    ]),
                    'business_logo' => $this->getImageForCategory('school', 'Fashion'),
                ]);
                $totalCreated++;
                $this->command->info("✓ Created school business for test@ojaewa.com");
            }
        }

        // Create schools
        foreach ($this->schools as $school) {
            $user = $users[$userIndex % $users->count()];
            $userIndex++;

            // Skip test user
            if ($user->email === 'test@ojaewa.com') {
                $user = $users[$userIndex % $users->count()];
                $userIndex++;
            }

            $categoryName = ucfirst($school['type']);
            $category = $schoolCategories[$categoryName] ?? $schoolCategories->first();

            BusinessProfile::create([
                'user_id' => $user->id,
                'category_id' => $category?->id,
                'category' => 'school',
                'country' => 'Nigeria',
                'state' => fake()->randomElement(['Lagos State', 'Abuja', 'Rivers State', 'Oyo State']),
                'city' => fake()->randomElement(['Lagos', 'Abuja', 'Port Harcourt', 'Ibadan']),
                'address' => fake()->streetAddress(),
                'business_email' => fake()->companyEmail(),
                'business_phone_number' => '+234' . rand(8010000000, 9099999999),
                'business_name' => $school['name'],
                'business_description' => $school['description'],
                'store_status' => 'approved',
                'subscription_status' => 'active',
                'subscription_ends_at' => now()->addDays(rand(30, 90)),
                'school_type' => $school['type'],
                'school_biography' => $school['bio'],
                'classes_offered' => $school['classes'],
                'business_logo' => $this->getImageForCategory('school', $categoryName),
                'website_url' => 'https://www.' . strtolower(str_replace(' ', '', $school['name'])) . '.edu.ng',
            ]);
            $totalCreated++;
        }

        $this->command->info("✓ Created {$totalCreated} business profiles (beauty services + schools)");
    }

    /**
     * Get an image URL for a category
     */
    private function getImageForCategory(string $type, string $categoryName): string
    {
        $images = $this->businessImages[$type][$categoryName] 
            ?? $this->businessImages[$type]['default'] 
            ?? ['https://images.unsplash.com/photo-1556761175-b413da4baf72?w=400&h=400&fit=crop'];
        
        return $images[array_rand($images)];
    }
}
