<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function index()
    {
        return view('notifications.index');
    }

    public function markAllRead(Request $request)
    {
        return response()->json(['success' => true]);
    }

    public function getNotifications()
    {
        $user = Auth::user();
        $notifications = [];
        $unreadCount = 0;

        if ($user) {
            $notifications = Notification::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            $unreadCount = Notification::where('user_id', $user->id)
                ->where('is_read', false)
                ->count();
        }

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ]);
    }

    public function markAsRead($id)
    {
        $user = Auth::user();
        if ($user) {
            Notification::where('user_id', $user->id)
                ->where('id', $id)
                ->update(['is_read' => true]);
        }

        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        $user = Auth::user();
        if ($user) {
            Notification::where('user_id', $user->id)
                ->update(['is_read' => true]);
        }

        return response()->json(['success' => true]);
    }

    public function checkNewNotifications()
    {
        $user = Auth::user();
        $hasNew = false;

        if ($user) {
            $hasNew = Notification::where('user_id', $user->id)
                ->where('is_read', false)
                ->where('created_at', '>', now()->subMinutes(5))
                ->exists();
        }

        return response()->json(['has_new' => $hasNew]);
    }
}
    