<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationsController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $notifications = $user->notifications()
            ->select('id', 'type', 'data', 'read_at', 'created_at')
            ->latest()
            ->paginate(10) 
            ->through(function (DatabaseNotification $notification) {
                return [
                    'id'         => $notification->id,
                    'type'       => class_basename($notification->type),
                    'data'       => $notification->data,
                    'is_read'    => $notification->read_at !== null,
                    'created_at' => $notification->created_at->format('Y-m-d H:i:s'),
                ];
            });

        return response()->json([
            'result' => true,
            'data'   => $notifications->items(), // notifications for current page
            'pagination' => [
                'current_page' => $notifications->currentPage(),
                'last_page'    => $notifications->lastPage(),
                'per_page'     => $notifications->perPage(),
                'total'        => $notifications->total(),
            ],
        ]);
    }

    public function markAsRead(Request $request, string $id)
    {
        $notification = $request->user()
            ->notifications()
            ->where('id', $id)
            ->firstOrFail();

        $notification->markAsRead();

        return response()->json([
            'result'  => true,
            'message' => 'Notification marked as read.',
        ]);
    }

    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return response()->json([
            'result'  => true,
            'message' => 'All notifications marked as read.',
        ]);
    }

    public function unreadCount(Request $request)
    {
        return response()->json([
            'result' => true,
            'unread_count' => $request->user()->unreadNotifications()->count()
        ]);
    }
}
