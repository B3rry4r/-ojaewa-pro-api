<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 * 
 * FINAL LOCKED MODEL - Category Types:
 * =====================================
 * 
 * PRODUCT CATALOGS (return Products):
 * - textiles (3 levels: Group → Leaf)
 * - shoes_bags (3 levels: Group → Leaf)
 * - afro_beauty_products (2 levels: Leaf only)
 * 
 * BUSINESS DIRECTORIES (return BusinessProfiles) - 2 levels:
 * - art (2 levels: Leaf only)
 * - school (2 levels: Leaf only)
 * - afro_beauty_services (2 levels: Leaf only)
 * 
 * INITIATIVES (return SustainabilityInitiatives) - 2 levels:
 * - sustainability (2 levels: Leaf only)
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
        
        return [
            'name' => $name,
            'slug' => Str::slug($name . '-' . $this->faker->unique()->randomNumber(5)),
            'parent_id' => null,
            'type' => $this->faker->randomElement(Category::TYPES),
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
     * Create a textiles category (products, 3 levels).
     */
    public function textiles(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'textiles',
            ];
        });
    }
    
    /**
     * Create a shoes_bags category (products, 3 levels).
     */
    public function shoesBags(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'shoes_bags',
            ];
        });
    }
    
    /**
     * Create an afro_beauty_products category (products, 2 levels).
     */
    public function afroBeautyProducts(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'afro_beauty_products',
            ];
        });
    }
    
    /**
     * Create an afro_beauty_services category (businesses, 2 levels).
     */
    public function afroBeautyServices(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'afro_beauty_services',
            ];
        });
    }
    
    /**
     * Create an art category (businesses, 2 levels).
     */
    public function art(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'art',
            ];
        });
    }
    
    /**
     * Create a school category (businesses, 2 levels).
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
     * Create a sustainability category (initiatives, 2 levels).
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
     * Create a product catalog category (textiles, shoes_bags, or afro_beauty_products).
     */
    public function productType(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => $this->faker->randomElement(Category::PRODUCT_TYPES),
            ];
        });
    }
    
    /**
     * Create a business directory category (art, school, or afro_beauty_services).
     */
    public function businessType(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => $this->faker->randomElement(Category::BUSINESS_TYPES),
            ];
        });
    }
    
    /**
     * Create an initiative category (sustainability).
     */
    public function initiativeType(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => $this->faker->randomElement(Category::INITIATIVE_TYPES),
            ];
        });
    }
}
