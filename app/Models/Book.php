<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'library_id', 'cover_image', 'title', 'author', 'category', 'description',
        'pages_count', 'file_size', 'language', 'is_available',
        'file_path', 'file_disk', 'original_name', 'mime_type', 'file_size_bytes', 'sha256', 'download_count',
    ];

    protected $casts = [
        'is_available'    => 'boolean',
        'pages_count'     => 'integer',
        'file_size_bytes' => 'integer',
        'download_count'  => 'integer',
    ];

    /** هل للكتاب ملف PDF مرفوع؟ */
    public function hasFile(): bool
    {
        return filled($this->file_path);
    }

    public function library()
    {
        return $this->belongsTo(Library::class);
    }
}
