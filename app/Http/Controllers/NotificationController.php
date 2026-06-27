<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\NotificationUrlResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(Request $request, NotificationUrlResolver $urlResolver)
    {
        $user = Auth::user();
        $limit = min(max((int) $request->input('limit', 8), 1), 50);

        $notifications = $user->notifications()
            ->with('notifiable')
            ->recent($limit)
            ->get()
            ->map(function (Notification $notification) use ($urlResolver, $user) {
                return array_merge($notification->toArray(), [
                    'url' => $urlResolver->resolve($notification, $user),
                ]);
            });

        return response()->json([
            'success' => true,
            'notifications' => $notifications,
            'unread_count' => $user->notifications()->unread()->count(),
            'has_more' => $user->notifications()->count() > $limit,
        ]);
    }

    public function history(Request $request, NotificationUrlResolver $urlResolver)
    {
        $user = Auth::user();
        $search = trim((string) $request->input('q', ''));

        $query = $user->notifications()
            ->with('notifiable')
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        if ($search !== '') {
            $escaped = str_replace(['%', '_'], ['\\%', '\\_'], $search);
            $query->where(function ($builder) use ($escaped) {
                $builder->where('title', 'like', '%' . $escaped . '%')
                    ->orWhere('message', 'like', '%' . $escaped . '%')
                    ->orWhere('type', 'like', '%' . $escaped . '%');
            });
        }

        $notifications = $query->paginate(15)->withQueryString();

        $notifications->getCollection()->transform(function (Notification $notification) use ($urlResolver, $user) {
            $notification->action_url = $urlResolver->resolve($notification, $user);

            return $notification;
        });

        return view('notifications.history', [
            'notifications' => $notifications,
            'unreadCount' => $user->notifications()->unread()->count(),
            'search' => $search,
            'totalCount' => $user->notifications()->count(),
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
            'unread_count' => Auth::user()->notifications()->unread()->count(),
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
            'unread_count' => 0,
        ]);
    }
}
