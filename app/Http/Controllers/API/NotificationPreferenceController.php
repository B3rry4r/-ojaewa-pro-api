<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class NotificationPreferenceController extends Controller
{
    /**
     * Get notification preferences
     */
    public function show(): JsonResponse
    {
        $user = Auth::user();
        
        $preferences = [
            'email_notifications' => $user->email_notifications ?? true,
            'push_notifications' => $user->push_notifications ?? true,
            'sms_notifications' => $user->sms_notifications ?? false,
            'order_updates' => $user->order_updates ?? true,
            'promotional_emails' => $user->promotional_emails ?? true,
            'security_alerts' => $user->security_alerts ?? true,
        ];

        return response()->json([
            'status' => 'success',
            'message' => 'Notification preferences retrieved successfully',
            'data' => $preferences
        ]);
    }

    /**
     * Update notification preferences
     */
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email_notifications' => 'sometimes|boolean',
            'push_notifications' => 'sometimes|boolean',
            'sms_notifications' => 'sometimes|boolean',
            'order_updates' => 'sometimes|boolean',
            'promotional_emails' => 'sometimes|boolean',
            'security_alerts' => 'sometimes|boolean',
        ]);

        $user = Auth::user();
        
        $user->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Notification preferences updated successfully',
            'data' => $validated
        ]);
    }
}