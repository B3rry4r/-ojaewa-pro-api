<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class AdminSettingsController extends Controller
{
    /**
     * Get application settings
     */
    public function show(): JsonResponse
    {
        $settings = [
            'maintenance_mode' => Cache::get('app.maintenance_mode', false),
            'maintenance_message' => Cache::get('app.maintenance_message', 'System is under maintenance'),
            'allow_registrations' => Cache::get('app.allow_registrations', true),
            'allow_seller_registrations' => Cache::get('app.allow_seller_registrations', true),
            'max_products_per_seller' => Cache::get('app.max_products_per_seller', 100),
            'commission_rate' => Cache::get('app.commission_rate', 5.0),
            'default_currency' => Cache::get('app.default_currency', 'NGN'),
            'email_notifications_enabled' => Cache::get('app.email_notifications_enabled', true),
            'sms_notifications_enabled' => Cache::get('app.sms_notifications_enabled', false),
            'auto_approve_products' => Cache::get('app.auto_approve_products', false),
            'auto_approve_sellers' => Cache::get('app.auto_approve_sellers', false),
            'min_order_amount' => Cache::get('app.min_order_amount', 1000),
            'max_order_amount' => Cache::get('app.max_order_amount', 1000000),
            'featured_products_limit' => Cache::get('app.featured_products_limit', 20),
            'support_email' => Cache::get('app.support_email', 'support@example.com'),
            'support_phone' => Cache::get('app.support_phone', '+234XXXXXXXXXX'),
        ];

        return response()->json([
            'status' => 'success',
            'message' => 'Application settings retrieved successfully',
            'data' => $settings
        ]);
    }

    /**
     * Update application settings
     */
    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'maintenance_mode' => 'sometimes|boolean',
            'maintenance_message' => 'sometimes|string|max:500',
            'allow_registrations' => 'sometimes|boolean',
            'allow_seller_registrations' => 'sometimes|boolean',
            'max_products_per_seller' => 'sometimes|integer|min:1|max:1000',
            'commission_rate' => 'sometimes|numeric|min:0|max:100',
            'default_currency' => 'sometimes|in:NGN,USD,EUR,GBP',
            'email_notifications_enabled' => 'sometimes|boolean',
            'sms_notifications_enabled' => 'sometimes|boolean',
            'auto_approve_products' => 'sometimes|boolean',
            'auto_approve_sellers' => 'sometimes|boolean',
            'min_order_amount' => 'sometimes|numeric|min:0',
            'max_order_amount' => 'sometimes|numeric|min:1000',
            'featured_products_limit' => 'sometimes|integer|min:1|max:100',
            'support_email' => 'sometimes|email',
            'support_phone' => 'sometimes|string|max:20',
        ]);

        $updatedSettings = [];

        // Update each setting that was provided
        foreach ($request->validated() as $key => $value) {
            $cacheKey = "app.{$key}";
            Cache::forever($cacheKey, $value);
            $updatedSettings[$key] = $value;
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Application settings updated successfully',
            'data' => $updatedSettings
        ]);
    }
}