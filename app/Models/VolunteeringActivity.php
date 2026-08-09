<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VolunteeringActivity extends Model
{
    use HasFactory;
    protected $fillable = [
        'cultural_center_id',
        'title',
        'description',
        'image',
        'location',
        'start_time',
    ];

    protected $casts = [
        'start_time' => 'datetime',
    ];

    public function culturalCenter()
    {
        return $this->belongsTo(CulturalCenter::class);
    }

    public function volunteerings()
    {
        return $this->hasMany(Volunteering::class);
    }

    public function ads()
    {
        return $this->morphMany(Ad::class, 'advertable');
    }

    public function volunteersCount(): int
    {
        return $this->volunteerings()->where('status', 'accepted')->count();
    }
}
