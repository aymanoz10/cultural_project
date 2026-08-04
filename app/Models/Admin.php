<?php

namespace App\Models;

use App\Enums\AdminRole;
use App\Models\Concerns\HasDeviceTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'avatar',
        'phone',
        'password',
        'role',
        'center_id',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * حارس على مستوى النموذج: يمنع حفظ أي دور خارج القائمة البيضاء
     * (خطّ دفاع ثانٍ حتى لو لم يُضبط التحقق في المتحكّم).
     */
    protected static function booted(): void
    {
        static::saving(function (Admin $admin) {
            if ($admin->role !== null && ! in_array($admin->role, AdminRole::values(), true)) {
                throw ValidationException::withMessages([
                    'role' => 'الدور غير صالح؛ القيم المسموحة: ' . implode('، ', AdminRole::values()),
                ]);
            }
        });
    }

    // ✅ مساواة صارمة بدل str_contains — تُغلق ثغرة تصعيد الصلاحيات (مثل «supervisor»)
    public function isSuper(): bool
    {
        return $this->role === AdminRole::Super->value;
    }

    public function isAdmin(): bool
    {
        return $this->role === AdminRole::Admin->value;
    }

    public function isTicketsAdmin(): bool
    {
        return $this->role === AdminRole::Tickets->value;
    }
}
