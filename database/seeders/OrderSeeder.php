<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use App\Models\SellerProfile;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Nigerian cities and states for realistic shipping addresses
     */
    private array $nigerianLocations = [
        ['city' => 'Lagos', 'state' => 'Lagos State', 'addresses' => ['12 Victoria Island', '45 Lekki Phase 1', '78 Ikeja GRA', '23 Surulere', '56 Yaba']],
        ['city' => 'Abuja', 'state' => 'FCT', 'addresses' => ['10 Maitama District', '33 Wuse 2', '67 Garki', '89 Asokoro', '21 Gwarinpa']],
        ['city' => 'Port Harcourt', 'state' => 'Rivers State', 'addresses' => ['15 GRA Phase 2', '42 Trans Amadi', '88 Rumuokwuta', '31 Eleme Junction']],
        ['city' => 'Ibadan', 'state' => 'Oyo State', 'addresses' => ['8 Bodija', '55 Ring Road', '72 Dugbe', '19 Challenge']],
        ['city' => 'Kano', 'state' => 'Kano State', 'addresses' => ['25 Nassarawa GRA', '63 Sabon Gari', '91 Bompai']],
        ['city' => 'Enugu', 'state' => 'Enugu State', 'addresses' => ['17 Independence Layout', '44 New Haven', '82 GRA']],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $approvedProducts = Product::where('status', 'approved')->get();
        
        if ($approvedProducts->isEmpty()) {
            $this->command->warn('No approved products found. Skipping order creation.');
            return;
        }

        $users = User::all();
        $sellers = SellerProfile::with('user')->get();

        if ($users->isEmpty()) {
            $this->command->warn('No users found. Skipping order creation.');
            return;
        }

        // Get the test user specifically
        $testUser = User::where('email', 'test@ojaewa.com')->first();
        
        // Order statuses with realistic distribution
        $orderStatuses = [
            'pending' => 2,
            'processing' => 3,
            'shipped' => 2,
            'delivered' => 4,
            'cancelled' => 1,
        ];

        $orderNumber = 1000;
        $totalOrders = 0;

        // Create orders for the test user with all statuses (for comprehensive testing)
        if ($testUser) {
            $this->createTestUserOrders($testUser, $approvedProducts, $orderNumber);
            $orderNumber += 10;
            $totalOrders += 6;
        }

        // Create orders for other users
        foreach ($users as $user) {
            // Skip test user as we already handled them
            if ($testUser && $user->id === $testUser->id) {
                continue;
            }

            // Each user gets 2-4 orders
            $orderCount = rand(2, 4);
            
            for ($i = 0; $i < $orderCount; $i++) {
                $orderNumber++;
                $status = $this->getRandomStatus($orderStatuses);
                
                $order = $this->createOrder($user, $approvedProducts, $orderNumber, $status);
                if ($order) {
                    $totalOrders++;
                }
            }
        }

        $this->command->info("✓ Created {$totalOrders} orders with items linked to products and sellers");
    }

    /**
     * Create comprehensive test orders for the main test user
     */
    private function createTestUserOrders(User $testUser, $products, int &$orderNumber): void
    {
        $statuses = ['pending', 'processing', 'shipped', 'delivered', 'delivered', 'cancelled'];
        
        foreach ($statuses as $index => $status) {
            $orderNumber++;
            $daysAgo = ($index + 1) * 3; // Spread orders over time
            
            $order = $this->createOrder(
                $testUser, 
                $products, 
                $orderNumber, 
                $status,
                Carbon::now()->subDays($daysAgo)
            );

            // Add tracking number for shipped/delivered orders
            if ($order && in_array($status, ['shipped', 'delivered'])) {
                $order->update([
                    'tracking_number' => 'TRK' . str_pad($orderNumber, 10, '0', STR_PAD_LEFT),
                ]);
            }

            // Set delivered_at for delivered orders
            if ($order && $status === 'delivered') {
                $order->update([
                    'delivered_at' => Carbon::now()->subDays($daysAgo - 2),
                ]);
            }

            // Add cancellation reason for cancelled orders
            if ($order && $status === 'cancelled') {
                $order->update([
                    'cancellation_reason' => 'Customer requested cancellation - changed mind about purchase',
                ]);
            }
        }

        $this->command->info("✓ Created 6 test orders for test@ojaewa.com (all statuses)");
    }

    /**
     * Create a single order with items
     */
    private function createOrder(User $user, $products, int $orderNumber, string $status, ?Carbon $createdAt = null): ?Order
    {
        // Get random location
        $location = $this->nigerianLocations[array_rand($this->nigerianLocations)];
        $address = $location['addresses'][array_rand($location['addresses'])];

        // Select 1-4 random products for this order
        $orderProducts = $products->random(min(rand(1, 4), $products->count()));
        
        $subtotal = 0;
        $orderItems = [];

        foreach ($orderProducts as $product) {
            $quantity = rand(1, 3);
            $unitPrice = $product->price;
            
            $orderItems[] = [
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
            ];
            
            $subtotal += $quantity * $unitPrice;
        }

        $deliveryFee = 2000;
        $createdAt = $createdAt ?? Carbon::now()->subDays(rand(1, 30));

        // Create the order
        $order = Order::create([
            'user_id' => $user->id,
            'status' => $status,
            'subtotal' => $subtotal,
            'delivery_fee' => $deliveryFee,
            'total_price' => $subtotal + $deliveryFee,
            'shipping_name' => $user->firstname . ' ' . $user->lastname,
            'shipping_phone' => $user->phone ?? '+234' . rand(8010000000, 9099999999),
            'shipping_address' => $address,
            'shipping_city' => $location['city'],
            'shipping_state' => $location['state'],
            'shipping_country' => 'Nigeria',
        ]);

        // Manually update timestamps to spread orders over time
        $order->created_at = $createdAt;
        $order->updated_at = $createdAt->copy()->addHours(rand(1, 48));
        $order->save();

        // Create order items
        foreach ($orderItems as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
            ]);
        }

        return $order;
    }

    /**
     * Get a random status based on distribution weights
     */
    private function getRandomStatus(array $statusWeights): string
    {
        $total = array_sum($statusWeights);
        $random = rand(1, $total);
        $cumulative = 0;

        foreach ($statusWeights as $status => $weight) {
            $cumulative += $weight;
            if ($random <= $cumulative) {
                return $status;
            }
        }

        return 'pending';
    }
}
