<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CulturalCenterPhoto extends Model
{
    protected $fillable = [
        'cultural_center_id',
        'photo',
    ];

    public function culturalCenter(): BelongsTo
    {
        return $this->belongsTo(CulturalCenter::class);
    }
}
