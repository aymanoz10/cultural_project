<?php

namespace App\Enums;

/**
 * أدوار المشرفين — مصدر الحقيقة الوحيد للأدوار المسموح بها.
 * يستخدمها نموذج Admin للتحقق (isSuper/الحارس) وتصلح كقائمة بيضاء في التحقق.
 */
enum AdminRole: string
{
    case Super = 'super';
    case Admin = 'admin';
    case Tickets = 'ticketsAdmin';

    /** @return array<int, string> */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
