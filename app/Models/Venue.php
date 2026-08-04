<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Venue extends Model
{
    use HasFactory;

    protected $fillable = [
        'cultural_center_id',
        'venue_type_id',
        'name',
        'capacity',
        'features',
        'image',
    ];

    protected $casts = [
        'features' => 'array',
    ];

    /**
     * العلاقة: القاعة تنتمي إلى نوع معين
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(VenueType::class, 'venue_type_id');
    }

    public function culturalCenter(): BelongsTo
    {
        return $this->belongsTo(CulturalCenter::class);
    }
}