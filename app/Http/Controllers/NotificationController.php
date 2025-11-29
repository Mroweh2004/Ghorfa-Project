<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $notifications = $user->notifications()
            ->recent(50)
            ->get();

        return response()->json([
            'success' => true,
            'notifications' => $notifications,
            'unread_count' => $user->notifications()->unread()->count(),
        ]);
    }

    public function unreadCount()
    {
        $user = Auth::user();
        $count = $user->notifications()->unread()->count();

        return response()->json([
            'success' => true,
            'count' => $count,
        ]);
    }

    public function markAsRead(Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read',
        ]);
    }

    public function markAllAsRead()
    {
        $user = Auth::user();
        $user->notifications()
            ->unread()
            ->update([
                'read' => true,
                'read_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read',
        ]);
    }
}
