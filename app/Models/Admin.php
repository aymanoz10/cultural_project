<?php

namespace App\Models;

use App\Models\Concerns\HasDeviceTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasApiTokens, HasDeviceTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'avatar',
        'phone',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function isSuper(): bool
    {
        return $this->role === 'super';
    }
}
