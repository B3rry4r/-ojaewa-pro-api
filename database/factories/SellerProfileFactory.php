<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\SellerProfile;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SellerProfile>
 */
class SellerProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(['pending', 'approved', 'rejected']);
        
        return [
            'country' => fake()->country(),
            'state' => fake()->state(),
            'city' => fake()->city(),
            'address' => fake()->address(),
            'business_email' => fake()->companyEmail(),
            'business_phone_number' => fake()->phoneNumber(),
            'instagram' => fake()->optional()->userName(),
            'facebook' => fake()->optional()->userName(),
            'identity_document' => fake()->optional()->filePath(),
            'business_name' => fake()->company(),
            'business_registration_number' => fake()->numerify('REG-#########'),
            'business_certificate' => fake()->optional()->filePath(),
            'business_logo' => fake()->optional()->filePath(),
            'bank_name' => fake()->randomElement(['First Bank', 'GTBank', 'Access Bank', 'UBA', 'Zenith Bank']),
            'account_number' => fake()->numerify('##########'),
            'registration_status' => $status,
            'badge' => $status === 'approved' 
                ? fake()->randomElement(['certified_authentic', 'heritage_artisan', 'sustainable_innovator', 'design_excellence'])
                : null, // Only approved sellers get badges
        ];
    }
}
