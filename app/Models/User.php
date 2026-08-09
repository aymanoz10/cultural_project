<?php

namespace App\Models;

use App\Models\Concerns\HasDeviceTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasDeviceTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'date_of_birth',
        'gender',
        'avatar',
        'phone',
    ];

    protected $hidden = [
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
        ];
    }

    public function reservations() { return $this->hasMany(Reservation::class); }
    public function ratings() { return $this->hasMany(Rating::class); }
    public function suggestions() { return $this->hasMany(Suggestion::class); }
    public function volunteerings() { return $this->hasMany(Volunteering::class); }

    public function admin() { return $this->belongsTo(Admin::class); }
}
