<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->words(2, true);
        $types = ['market', 'beauty', 'brand', 'school', 'sustainability', 'music'];
        
        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'parent_id' => null,
            'type' => $this->faker->randomElement($types),
            'order' => $this->faker->numberBetween(1, 100),
        ];
    }
    
    /**
     * Create a category with a parent.
     */
    public function withParent(Category $parent = null): static
    {
        return $this->state(function (array $attributes) use ($parent) {
            $parentCategory = $parent ?: Category::factory()->create();
            
            return [
                'parent_id' => $parentCategory->id,
                'type' => $parentCategory->type, // Child inherits parent's type
            ];
        });
    }
    
    /**
     * Create a top-level category.
     */
    public function topLevel(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'parent_id' => null,
            ];
        });
    }
    
    /**
     * Create a market category.
     */
    public function market(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'market',
            ];
        });
    }
    
    /**
     * Create a beauty category.
     */
    public function beauty(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'beauty',
            ];
        });
    }
    
    /**
     * Create a brand category.
     */
    public function brand(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'brand',
            ];
        });
    }
    
    /**
     * Create a school category.
     */
    public function school(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'school',
            ];
        });
    }
    
    /**
     * Create a sustainability category.
     */
    public function sustainability(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'sustainability',
            ];
        });
    }
    
    /**
     * Create a music category.
     */
    public function music(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'music',
            ];
        });
    }
}
