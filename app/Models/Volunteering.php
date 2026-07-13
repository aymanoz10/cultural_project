<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Volunteering extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'volunteering_activity_id',
        'form_data',
        'status',
    ];

    protected $casts = [
        'form_data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function volunteeringActivity()
    {
        return $this->belongsTo(VolunteeringActivity::class);
    }
}
