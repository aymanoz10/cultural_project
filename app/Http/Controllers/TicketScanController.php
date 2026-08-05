<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;

class TicketScanController extends Controller
{
    /**
     * التحقّق من باركود التذكرة وتسجيل الحضور.
     * يفكّ التشفير (أو يقبل رقم تذكرة مباشر)، ثم ينقل الحالة: فعّالة → مكتملة.
     */
    public function verify(Request $request)
    {
        $request->validate(['code' => 'required|string']);
        $raw = trim($request->input('code'));

        // 1) محاولة فكّ التشفير؛ فإن فشل نعامله كـ JSON عادي أو رقم تذكرة مباشر
        $data = Reservation::decodeQrPayload($raw)
            ?? (json_decode($raw, true) ?: ['ticket_id' => $raw]);

        $ticketId = $data['ticket_id'] ?? null;
        if (! $ticketId) {
            return response()->json([
                'success' => false, 'result' => 'invalid',
                'message' => 'باركود غير صالح أو غير قابل للقراءة',
            ], 422);
        }

        $reservation = Reservation::with(['user', 'activity'])
            ->where('ticket_id', $ticketId)
            ->first();

        if (! $reservation) {
            return response()->json([
                'success' => false, 'result' => 'not_found',
                'message' => 'التذكرة غير موجودة في النظام',
            ], 404);
        }

        $info = [
            'ticket_id'      => $reservation->ticket_id,
            'holder_name'    => $reservation->user?->name ?? ($data['holder_name'] ?? 'ضيف'),
            'activity_title' => $reservation->activity?->title ?? ($data['activity_title'] ?? '—'),
            'activity_time'  => optional($reservation->activity?->start_time)->format('Y-m-d H:i'),
            'seats_count'    => $reservation->seats_count,
            'status'         => $reservation->status,
            'status_label'   => $reservation->statusLabel(),
        ];

        // الحالات غير القابلة للدخول
        if ($reservation->status === Reservation::STATUS_CANCELLED) {
            return response()->json(['success' => false, 'result' => 'cancelled', 'message' => 'التذكرة ملغاة', 'info' => $info], 200);
        }
        if ($reservation->status === Reservation::STATUS_WAIT_LIST) {
            return response()->json(['success' => false, 'result' => 'wait_list', 'message' => 'التذكرة في قائمة الانتظار (غير مؤكّدة)', 'info' => $info], 200);
        }
        if ($reservation->status === Reservation::STATUS_COMPLETED) {
            $info['used_at'] = optional($reservation->updated_at)->format('Y-m-d H:i');
            return response()->json(['success' => false, 'result' => 'already_used', 'message' => 'التذكرة مستخدمة مسبقاً (تم تسجيل الحضور)', 'info' => $info], 200);
        }

        // فعّالة (CONFIRMED) → مكتملة (COMPLETED): تسجيل الحضور
        $reservation->update(['status' => Reservation::STATUS_COMPLETED]);
        $info['status'] = Reservation::STATUS_COMPLETED;
        $info['status_label'] = Reservation::STATUS_LABELS[Reservation::STATUS_COMPLETED];

        return response()->json([
            'success' => true, 'result' => 'checked_in',
            'message' => 'تم تسجيل الحضور بنجاح ✓',
            'info'    => $info,
        ], 200);
    }
}
