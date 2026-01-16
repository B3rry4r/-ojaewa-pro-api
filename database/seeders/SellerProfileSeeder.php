<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\SellerProfile;

class SellerProfileSeeder extends Seeder
{
    /**
     * Seller profile images
     */
    private array $sellerLogos = [
        'https://images.unsplash.com/photo-1560179707-f14e90ef3623?w=400&h=400&fit=crop',
        'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=400&h=400&fit=crop',
        'https://images.unsplash.com/photo-1497366216548-37526070297c?w=400&h=400&fit=crop',
        'https://images.unsplash.com/photo-1497215842964-222b430dc094?w=400&h=400&fit=crop',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $totalCreated = 0;

        // 1. Create seller profile for test user
        $testUser = User::where('email', 'test@ojaewa.com')->first();
        if ($testUser && !SellerProfile::where('user_id', $testUser->id)->exists()) {
            SellerProfile::create([
                'user_id' => $testUser->id,
                'country' => 'Nigeria',
                'state' => 'Lagos State',
                'city' => 'Lagos',
                'address' => '45 Victoria Island, Lagos',
                'business_email' => 'seller@ojaewa.com',
                'business_phone_number' => '+2348012345678',
                'instagram' => '@ojaewa_fashion',
                'facebook' => 'OjaEwaFashion',
                'business_name' => 'OjaEwa Fashion House',
                'business_registration_number' => 'RC-' . rand(100000, 999999),
                'business_logo' => $this->sellerLogos[0],
                'bank_name' => 'First Bank Nigeria',
                'account_number' => '30' . rand(10000000, 99999999),
                'registration_status' => 'approved',
                'active' => true,
            ]);
            $totalCreated++;
            $this->command->info('✓ Created seller profile for test@ojaewa.com');
        }

        // 2. Create seller profiles for dedicated seller users
        $sellerData = [
            [
                'email' => 'olumide@seller.com',
                'business_name' => 'Olumide African Fashions',
                'description' => 'Premium African fashion house specializing in traditional and contemporary designs.',
                'state' => 'Lagos State',
                'city' => 'Lagos',
                'address' => '12 Broad Street, Lagos Island',
            ],
            [
                'email' => 'amara@seller.com',
                'business_name' => 'Amara Designs Studio',
                'description' => 'Bespoke African couture and ready-to-wear fashion for the modern African.',
                'state' => 'FCT',
                'city' => 'Abuja',
                'address' => '8 Wuse Zone 5, Abuja',
            ],
            [
                'email' => 'kwame@seller.com',
                'business_name' => 'Kwame Artistry Collection',
                'description' => 'Authentic African art, sculptures, and handcrafted pieces from across the continent.',
                'state' => 'Rivers State',
                'city' => 'Port Harcourt',
                'address' => '23 GRA Phase 2, Port Harcourt',
            ],
        ];

        foreach ($sellerData as $index => $data) {
            $user = User::where('email', $data['email'])->first();
            if ($user && !SellerProfile::where('user_id', $user->id)->exists()) {
                SellerProfile::create([
                    'user_id' => $user->id,
                    'country' => 'Nigeria',
                    'state' => $data['state'],
                    'city' => $data['city'],
                    'address' => $data['address'],
                    'business_email' => $data['email'],
                    'business_phone_number' => '+234' . rand(8010000000, 9099999999),
                    'instagram' => '@' . strtolower(str_replace(' ', '_', $data['business_name'])),
                    'facebook' => str_replace(' ', '', $data['business_name']),
                    'business_name' => $data['business_name'],
                    'business_registration_number' => 'RC-' . rand(100000, 999999),
                    'business_logo' => $this->sellerLogos[($index + 1) % count($this->sellerLogos)],
                    'bank_name' => fake()->randomElement(['GTBank', 'Access Bank', 'Zenith Bank', 'UBA']),
                    'account_number' => rand(1000000000, 9999999999),
                    'registration_status' => 'approved',
                    'active' => true,
                ]);
                $totalCreated++;
            }
        }

        $this->command->info("✓ Created {$totalCreated} seller profiles (all approved)");
    }
}
