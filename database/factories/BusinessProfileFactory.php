<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\BusinessProfile;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BusinessProfileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BusinessProfile::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $categories = ['beauty', 'brand', 'music', 'school'];
        $category = $this->faker->randomElement($categories);

        $offeringType = 'selling_product';
        if ($category === 'beauty') {
            $offeringType = 'providing_service';
        }

        $baseData = [
            'user_id' => User::factory(),
            'category' => $category,
            'offering_type' => $offeringType,
            'country' => $this->faker->country(),
            'state' => $this->faker->state(),
            'city' => $this->faker->city(),
            'address' => $this->faker->address(),
            'business_email' => $this->faker->unique()->safeEmail(),
            'business_phone_number' => $this->faker->phoneNumber(),
            'business_name' => $this->faker->company(),
            'business_description' => $this->faker->paragraph(),
            'instagram' => '@' . Str::lower($this->faker->word()),
            'facebook' => 'fb/' . Str::lower($this->faker->word()),
            'store_status' => $this->faker->randomElement(['pending', 'approved', 'deactivated']),
        ];

        // Add category-specific fields
        switch ($category) {
            case 'beauty':
                $baseData['professional_title'] = $this->faker->jobTitle();
                $baseData['service_list'] = json_encode([
                    ['name' => 'Service 1', 'price' => $this->faker->numberBetween(1000, 10000)],
                    ['name' => 'Service 2', 'price' => $this->faker->numberBetween(1000, 10000)]
                ]);
                $baseData['school_type'] = 'beauty';
                break;
            case 'brand':
                $baseData['website_url'] = $this->faker->url();
                $baseData['product_list'] = json_encode([
                    ['name' => 'Product 1', 'price' => $this->faker->numberBetween(1000, 10000)],
                    ['name' => 'Product 2', 'price' => $this->faker->numberBetween(1000, 10000)]
                ]);
                $baseData['business_certificates'] = json_encode([
                    'certificate1.pdf', 'certificate2.pdf'
                ]);
                break;
            case 'music':
                $baseData['music_category'] = $this->faker->randomElement(['dj', 'artist', 'producer']);
                $baseData['youtube'] = $this->faker->url();
                $baseData['spotify'] = $this->faker->url();
                break;
            case 'school':
                $baseData['school_type'] = $this->faker->randomElement(['fashion', 'music', 'catering', 'beauty']);
                $baseData['website_url'] = $this->faker->url();
                $baseData['school_biography'] = $this->faker->paragraph();
                $baseData['classes_offered'] = json_encode([
                    ['name' => 'Class 1', 'price' => $this->faker->numberBetween(1000, 10000)],
                    ['name' => 'Class 2', 'price' => $this->faker->numberBetween(1000, 10000)]
                ]);
                break;
        }

        return $baseData;
    }
}
