<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdminNotificationController extends Controller
{
    /**
     * Send notification to users
     */
    public function send(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:general,promotion,system,security',
            'recipient_type' => 'required|in:all,specific,sellers',
            'user_ids' => 'required_if:recipient_type,specific|array',
            'user_ids.*' => 'exists:users,id',
            'action_url' => 'nullable|url',
            'send_immediately' => 'boolean'
        ]);

        $recipients = [];

        // Determine recipients based on type
        switch ($request->recipient_type) {
            case 'all':
                $recipients = User::pluck('id')->toArray();
                break;
                
            case 'sellers':
                $recipients = User::whereHas('sellerProfile')->pluck('id')->toArray();
                break;
                
            case 'specific':
                $recipients = $request->user_ids;
                break;
        }

        // Create notifications for each recipient
        $notificationsCreated = 0;
        foreach ($recipients as $userId) {
            Notification::create([
                'user_id' => $userId,
                'title' => $request->title,
                'message' => $request->message,
                'type' => $request->type,
                'action_url' => $request->action_url,
                'is_read' => false,
            ]);
            $notificationsCreated++;
        }

        return response()->json([
            'status' => 'success',
            'message' => "Notification sent successfully to {$notificationsCreated} users",
            'data' => [
                'recipients_count' => $notificationsCreated,
                'notification' => [
                    'title' => $request->title,
                    'message' => $request->message,
                    'type' => $request->type,
                    'recipient_type' => $request->recipient_type
                ]
            ]
        ], 201);
    }
}