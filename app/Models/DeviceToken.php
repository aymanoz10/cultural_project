<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceToken extends Model
{
    protected $fillable = ['token', 'platform'];

    public function tokenable()
    {
        return $this->morphTo();
    }
}
