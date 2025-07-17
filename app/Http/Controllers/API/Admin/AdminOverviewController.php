<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessProfile;
use App\Models\Order;
use App\Models\Product;
use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdminOverviewController extends Controller
{
    /**
     * Get dashboard overview statistics
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        // Get total users count
        $totalUsers = User::count();
        
        // Get total revenue (sum of paid orders)
        $totalRevenue = Order::where('status', 'paid')->sum('total_price');
        
        // Get total businesses count
        $totalBusinesses = BusinessProfile::count();
        
        // Get total sellers count
        $totalSellers = SellerProfile::count();
        
        // Get market revenue (sum of orders from products)
        $marketRevenue = Order::whereHas('orderItems.product')
            ->where('status', 'paid')
            ->sum('total_price');
        
        return response()->json([
            'status' => 'success',
            'data' => [
                'total_users' => $totalUsers,
                'total_revenue' => $totalRevenue,
                'total_businesses' => $totalBusinesses,
                'total_sellers' => $totalSellers,
                'market_revenue' => $marketRevenue,
            ]
        ]);
    }
}
