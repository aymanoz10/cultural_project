<?php

namespace App\Models;

use App\Models\Concerns\HasDeviceTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
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
        'center_id', // ✅ تم إضافتها حتى يتم حفظ المركز الثقافي عند إنشاء المستخدم
        'status',    // ✅ تم إضافتها حتى يتم حفظ حالة الحساب (active, pending, banned)
    ];

    protected $hidden = [
        'password', 
        'remember_token',
    ];

    // ✅ التحقق إن كان سوبر أدمن (يفحص النص مرونة لو كان يحتوي على super)
    public function isSuper(): bool
    {
        return str_contains($this->role ?? '', 'super');
    }

    // ✅ التحقق إن كان أدمن عادي
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    // ✅ التحقق إن كان مسؤول تذاكر فقط
    public function isTicketsAdmin(): bool
    {
        return $this->role === 'ticketsAdmin';
    }
}