<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venue extends Model
{
    use HasFactory;

    public const TYPES = ['hall', 'theater'];

    protected $fillable = [
        'cultural_center_id',
        'name',
        'type',
        'capacity',
        'features',
        'image',
    ];

    protected $casts = [
        'features' => 'array',
    ];

    public function culturalCenter(): BelongsTo
    {
        return $this->belongsTo(CulturalCenter::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }
}
