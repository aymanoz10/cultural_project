<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CulturalCenter extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'location', 'description', 'image'];

    public function theaters()
    {
        return $this->hasMany(Theater::class);
    }

    public function halls()
    {
        return $this->hasMany(Hall::class);
    }

    public function libraries()
    {
        return $this->hasMany(Library::class);
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function volunteeringActivities()
    {
        return $this->hasMany(VolunteeringActivity::class);
    }
}
