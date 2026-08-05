<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminNotificationController extends Controller
{
    /**
     * تغذية الجرس: أحدث الإشعارات + عدد غير المقروء (JSON للاستطلاع)
     */
    public function feed(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $notifications = $admin->notifications()->latest()->limit(12)->get();

        return response()->json([
            'unread_count'  => $admin->unreadNotifications()->count(),
            'notifications' => $notifications->map(fn ($n) => $this->transform($n))->values(),
        ]);
    }

    /**
     * صفحة كل الإشعارات (قائمة كاملة مع ترقيم)
     */
    public function page(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        $notifications = $admin->notifications()->latest()->paginate(20);

        return view('admin.notifications.index', compact('notifications'));
    }

    /**
     * تعليم إشعار واحد كمقروء
     */
    public function markRead(Request $request, string $id)
    {
        $admin = Auth::guard('admin')->user();
        $admin->notifications()->where('id', $id)->first()?->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * تعليم كل الإشعارات كمقروءة
     */
    public function markAllRead(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        $admin->unreadNotifications->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * تحويل سجل الإشعار إلى بنية موحّدة للواجهة
     */
    private function transform($n): array
    {
        $data = is_array($n->data) ? $n->data : (json_decode($n->data, true) ?: []);

        return [
            'id'         => $n->id,
            'title'      => $data['title'] ?? 'إشعار',
            'body'       => $data['body'] ?? '',
            'icon'       => $data['icon'] ?? $data['type'] ?? 'bell',
            'action_url' => $data['action_url'] ?? null,
            'read'       => $n->read_at !== null,
            'time'       => $n->created_at?->locale('ar')->diffForHumans(),
        ];
    }
}
