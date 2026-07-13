<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Library extends Model
{
    protected $fillable = ['cultural_center_id', 'name', 'image'];

    public function culturalCenter()
    {
        return $this->belongsTo(CulturalCenter::class);
    }

    public function books()
    {
        return $this->hasMany(Book::class);
    }
}
