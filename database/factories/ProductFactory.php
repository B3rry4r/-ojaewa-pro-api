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
        $fabrics = ['Ankara','Kente','Adinkra','Aso Oke','Akwa Ocha','George','Kente Prestige','Faso Dan Fani','Korhogo','Kitenge','Leso','Shuka','Liputa','Raffia','Shweshwe','Lishu','IsiShweshwe','Cotton Voile','Woolen','Melhfa'];
        $sizes = ['S', 'M', 'L', 'XL', 'XXL', '36', '38', '40', '42', '44'];
        $genders = ['male', 'female', 'unisex'];
        $status = ['pending', 'approved', 'rejected']; // Only these status values are valid
        
        return [
            'seller_profile_id' => SellerProfile::factory(),
            'name' => fake()->words(3, true) . ' ' . $this->faker->randomElement($africanStyles),
            'gender' => $this->faker->randomElement($genders),
            'style' => $this->faker->randomElement($africanStyles),
            'tribe' => $this->faker->randomElement($africanTribes),
            'fabric_type' => $this->faker->randomElement($fabrics),
            'description' => fake()->paragraphs(2, true),
            'image' => 'https://images.unsplash.com/photo-1485968579580-b6d095142e6e?w=500&h=500&fit=crop',
            'size' => $this->faker->randomElement($sizes),
            'processing_time_type' => $this->faker->randomElement(['normal', 'quick_quick']),
            'processing_days' => $this->faker->numberBetween(2, 14),
            'price' => $this->faker->randomFloat(2, 20, 500),
            'status' => $this->faker->randomElement($status), // Must use enum values from migration
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
