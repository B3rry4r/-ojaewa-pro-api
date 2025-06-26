<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Default to product reviews
        $product = Product::inRandomOrder()->first();
        
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'reviewable_id' => $product ? $product->id : Product::factory(),
            'reviewable_type' => Product::class,
            'rating' => $this->faker->numberBetween(1, 5),
            'headline' => $this->faker->sentence(),
            'body' => $this->faker->paragraphs(2, true),
        ];
    }
    
    /**
     * Configure the model factory to create a review for a specific reviewable model.
     *
     * @param mixed $reviewable
     * @return $this
     */
    public function forReviewable($reviewable)
    {
        return $this->state(function (array $attributes) use ($reviewable) {
            return [
                'reviewable_id' => $reviewable->id,
                'reviewable_type' => get_class($reviewable),
            ];
        });
    }
}
