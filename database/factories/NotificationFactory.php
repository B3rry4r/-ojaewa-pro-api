<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notification>
 */
class NotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = ['push', 'email'];
        $events = ['order_placed', 'order_shipped', 'order_delivered', 'payment_confirmed', 'product_reviewed', 'wishlist_item_sale'];
        
        return [
            'user_id' => User::factory(),
            'type' => $this->faker->randomElement($types),
            'event' => $this->faker->randomElement($events),
            'title' => $this->faker->sentence(4),
            'message' => $this->faker->sentence(10),
            'payload' => $this->faker->optional()->randomElement([
                json_encode(['order_id' => $this->faker->numberBetween(1, 100)]),
                json_encode(['product_id' => $this->faker->numberBetween(1, 50)]),
                json_encode(['amount' => $this->faker->randomFloat(2, 10, 1000)]),
                null
            ]),
            'read_at' => $this->faker->optional(0.3)->dateTimeBetween('-1 month', 'now'),
        ];
    }

    /**
     * Indicate that the notification is read.
     */
    public function read(): static
    {
        return $this->state(fn (array $attributes) => [
            'read_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
        ]);
    }

    /**
     * Indicate that the notification is unread.
     */
    public function unread(): static
    {
        return $this->state(fn (array $attributes) => [
            'read_at' => null,
        ]);
    }

    /**
     * Indicate that the notification has a specific type.
     */
    public function type(string $type): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => $type,
        ]);
    }

    /**
     * Indicate that the notification has a specific event.
     */
    public function event(string $event): static
    {
        return $this->state(fn (array $attributes) => [
            'event' => $event,
        ]);
    }
}
