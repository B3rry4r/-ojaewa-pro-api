<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if we have products to add to orders
        $productsCount = Product::where('status', 'approved')->count();
        
        if ($productsCount === 0) {
            $this->command->warn('No approved products found. Skipping order creation.');
            return;
        }
        
        // Get all users
        $users = User::all();
        
        foreach ($users as $user) {
            // Create 2 orders for each user
            for ($i = 0; $i < 2; $i++) {
                $order = Order::factory()->create([
                    'user_id' => $user->id,
                    'status' => $i === 0 ? 'paid' : 'pending', // First order paid, second pending
                ]);
                
                // Create 1-3 order items for each order
                $orderTotal = 0;
                $itemCount = rand(1, 3);
                
                // Get random approved products
                $products = Product::where('status', 'approved')
                    ->inRandomOrder()
                    ->take($itemCount)
                    ->get();
                
                foreach ($products as $product) {
                    $quantity = rand(1, 3);
                    $unitPrice = $product->price;
                    $itemTotal = $quantity * $unitPrice;
                    
                    // Create order item
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                    ]);
                    
                    $orderTotal += $itemTotal;
                }
                
                // Update order totals and shipping details
                $deliveryFee = 2000;
                $order->update([
                    'subtotal' => $orderTotal,
                    'delivery_fee' => $deliveryFee,
                    'total_price' => $orderTotal + $deliveryFee,
                    'shipping_name' => $user->firstname . ' ' . $user->lastname,
                    'shipping_phone' => $user->phone ?? fake()->phoneNumber(),
                    'shipping_address' => fake()->streetAddress(),
                    'shipping_city' => fake()->city(),
                    'shipping_state' => fake()->state(),
                    'shipping_country' => 'Nigeria',
                ]);
            }
            
            $this->command->info("Created 2 orders with items for user {$user->firstname} {$user->lastname}");
        }
    }
}
