<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    public const PURPOSE_LOGIN = 'login';
    public const PURPOSE_REGISTER = 'register';

    protected $fillable = [
        'phone',
        'code',
        'purpose',
        'payload',
        'attempts',
        'expires_at',
    ];

    protected $casts = [
        'payload'    => 'array',
        'expires_at' => 'datetime',
    ];

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }
}
