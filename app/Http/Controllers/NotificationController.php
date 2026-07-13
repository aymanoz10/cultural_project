<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    private function actor(Request $request)
    {
        return $request->user('admin') ?? $request->user();
    }

    public function index(Request $request)
    {
        $notifications = $this->actor($request)
            ->notifications()
            ->paginate(20);

        return response()->json([
            'data' => $notifications->map(fn (DatabaseNotification $n) => $this->format($n)),
            'meta' => [
                'current_page' => $notifications->currentPage(),
                'last_page'    => $notifications->lastPage(),
                'total'        => $notifications->total(),
            ],
        ]);
    }

    public function unreadCount(Request $request)
    {
        return response()->json([
            'count' => $this->actor($request)->unreadNotifications()->count(),
        ]);
    }

    public function markAsRead(Request $request, string $id)
    {
        $notification = $this->actor($request)->notifications()->where('id', $id)->firstOrFail();
        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'data'    => $this->format($notification->fresh()),
        ]);
    }

    public function markAllAsRead(Request $request)
    {
        $this->actor($request)->unreadNotifications->markAsRead();

        return response()->json(['success' => true, 'message' => 'تم تعليم جميع الإشعارات كمقروءة']);
    }

    public function destroy(Request $request, string $id)
    {
        $this->actor($request)->notifications()->where('id', $id)->delete();

        return response()->json(['success' => true]);
    }

    private function format(DatabaseNotification $notification): array
    {
        return [
            'id'         => $notification->id,
            'type'       => class_basename($notification->type),
            'data'       => $notification->data,
            'read_at'    => $notification->read_at,
            'created_at' => $notification->created_at,
        ];
    }
}
