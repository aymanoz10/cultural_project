<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CulturalCenter extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'location', 'description', 'features'];

    protected $casts = [
        'features' => 'array',
    ];

    public function photos(): HasMany
    {
        return $this->hasMany(CulturalCenterPhoto::class);
    }

    public function venues(): HasMany
    {
        return $this->hasMany(Venue::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function volunteeringActivities(): HasMany
    {
        return $this->hasMany(VolunteeringActivity::class);
    }
}
