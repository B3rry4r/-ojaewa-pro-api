<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    /**
     * Get all notifications for the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $notifications = $request->user()->notifications()
            ->latest()
            ->paginate(20);

        return response()->json([
            'status' => 'success',
            'data' => $notifications,
        ]);
    }

    /**
     * Get unread notifications count.
     */
    public function unreadCount(Request $request): JsonResponse
    {
        $count = $request->user()->notifications()
            ->unread()
            ->count();

        return response()->json([
            'status' => 'success',
            'data' => ['unread_count' => $count],
        ]);
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(Request $request, int $id): JsonResponse
    {
        $notification = $request->user()->notifications()->find($id);

        if (!$notification) {
            return response()->json([
                'status' => 'error',
                'message' => 'Notification not found',
            ], 404);
        }

        $notification->markAsRead();

        return response()->json([
            'status' => 'success',
            'message' => 'Notification marked as read',
            'data' => $notification->fresh(),
        ]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $updated = $request->user()->notifications()
            ->unread()
            ->update(['read_at' => now()]);

        return response()->json([
            'status' => 'success',
            'message' => "Marked {$updated} notifications as read",
        ]);
    }

    /**
     * Delete a notification.
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $notification = $request->user()->notifications()->find($id);

        if (!$notification) {
            return response()->json([
                'status' => 'error',
                'message' => 'Notification not found',
            ], 404);
        }

        $notification->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Notification deleted',
        ]);
    }

    /**
     * Get notifications by type or event.
     */
    public function filter(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'nullable|in:push,email',
            'event' => 'nullable|string',
            'read' => 'nullable|boolean',
        ]);

        $query = $request->user()->notifications();

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('event')) {
            $query->where('event', $request->event);
        }

        if ($request->has('read')) {
            if ($request->read) {
                $query->read();
            } else {
                $query->unread();
            }
        }

        $notifications = $query->latest()->paginate(20);

        return response()->json([
            'status' => 'success',
            'data' => $notifications,
        ]);
    }
}
