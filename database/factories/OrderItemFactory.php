<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $product = Product::where('status', 'approved')
                         ->inRandomOrder()
                         ->first();
        
        return [
            'order_id' => Order::factory(),
            'product_id' => $product ? $product->id : Product::factory()->approved(),
            'quantity' => $this->faker->numberBetween(1, 5),
            'unit_price' => $product ? $product->price : $this->faker->randomFloat(2, 20, 500),
        ];
    }
}
