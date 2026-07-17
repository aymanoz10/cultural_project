<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CulturalCenter extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'location', 'description'];

    public function photos(): HasMany
    {
        return $this->hasMany(CulturalCenterPhoto::class);
    }

    public function theaters(): HasMany
    {
        return $this->hasMany(Theater::class);
    }

    public function halls(): HasMany
    {
        return $this->hasMany(Hall::class);
    }

    public function libraries(): HasMany
    {
        return $this->hasMany(Library::class);
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
