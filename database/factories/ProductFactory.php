<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\SellerProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $africanStyles = ['Ankara', 'Kente', 'Aso Oke', 'Agbada', 'Dashiki', 'Kaftan', 'Gele', 'Adire', 'Boubou'];
        $africanTribes = ['Yoruba', 'Igbo', 'Hausa', 'Ashanti', 'Zulu', 'Masai', 'Xhosa', 'Fulani', 'Tuareg'];
        $sizes = ['S', 'M', 'L', 'XL', 'XXL', '36', '38', '40', '42', '44'];
        $genders = ['male', 'female', 'unisex'];
        $status = ['pending', 'approved', 'rejected'];
        
        return [
            'seller_profile_id' => SellerProfile::inRandomOrder()->first()->id,
            'name' => fake()->words(3, true) . ' ' . $this->faker->randomElement($africanStyles),
            'gender' => $this->faker->randomElement($genders),
            'style' => $this->faker->randomElement($africanStyles),
            'tribe' => $this->faker->randomElement($africanTribes),
            'description' => fake()->paragraphs(2, true),
            'image' => 'https://via.placeholder.com/500x500.png?text=African+Style+Fashion',
            'size' => $this->faker->randomElement($sizes),
            'processing_time_type' => $this->faker->randomElement(['normal', 'quick_quick']),
            'processing_days' => $this->faker->numberBetween(2, 14),
            'price' => $this->faker->randomFloat(2, 20, 500),
            'status' => $this->faker->randomElement($status),
        ];
    }
    
    /**
     * Indicate that the product is approved.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function approved(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'approved',
            ];
        });
    }
}
