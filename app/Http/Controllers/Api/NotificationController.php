<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * GET /notifications
     * List user's notifications (newest first, unread on top).
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $perPage = min((int) $request->get('per_page', 20), 50);

        $notifications = Notification::where('user_id', $user->id)
            ->orderByRaw('is_read ASC, created_at DESC')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $notifications,
        ]);
    }

    /**
     * GET /notifications/unread-count
     * Return the number of unread notifications.
     */
    public function unreadCount()
    {
        $count = Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->count();

        return response()->json([
            'success' => true,
            'data' => ['count' => $count],
        ]);
    }

    /**
     * POST /notifications/{id}/read
     * Mark a single notification as read.
     */
    public function markRead($id)
    {
        $notification = Notification::where('user_id', auth()->id())
            ->where('id', $id)
            ->firstOrFail();

        $notification->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read',
        ]);
    }

    /**
     * POST /notifications/read-all
     * Mark all notifications as read.
     */
    public function markAllRead()
    {
        Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read',
        ]);
    }
}
