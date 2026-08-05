<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = ['library_id', 'cover_image', 'title', 'author', 'category', 'description', 'pages_count', 'file_size', 'language', 'is_available'];

    protected $casts = [
        'is_available' => 'boolean',
        'pages_count'  => 'integer',
    ];

    public function library()
    {
        return $this->belongsTo(Library::class);
    }
}
