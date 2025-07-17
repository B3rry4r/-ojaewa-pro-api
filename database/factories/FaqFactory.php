<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Faq>
 */
class FaqFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = ['general', 'billing', 'technical', 'shipping', 'returns', 'account'];
        
        return [
            'question' => $this->faker->sentence() . '?',
            'answer' => $this->faker->paragraphs(2, true),
            'category' => $this->faker->optional(0.8)->randomElement($categories),
        ];
    }

    /**
     * Indicate that the FAQ has a specific category.
     */
    public function category(string $category): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => $category,
        ]);
    }

    /**
     * Indicate that the FAQ has no category.
     */
    public function noCategory(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => null,
        ]);
    }
}
