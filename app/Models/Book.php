<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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

    // تُضاف تلقائياً لكل تحويل toArray()/toJson() (ومنها استجابات JSON بالكنترولر)
    protected $appends = [
        'cover_url', 'pdf_read_url', 'pdf_download_url', 'downloads_count', 'added_date',
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

    /** رابط كامل لصورة الغلاف (القرص العام public) */
    public function getCoverUrlAttribute(): ?string
    {
        return $this->cover_image ? Storage::disk('public')->url($this->cover_image) : null;
    }

    /** رابط قراءة الكتاب داخل التطبيق (محمي بالتوكن) */
    public function getPdfReadUrlAttribute(): ?string
    {
        return $this->hasFile() ? route('books.read', $this->id) : null;
    }

    /** رابط تحميل الكتاب (محمي بالتوكن) */
    public function getPdfDownloadUrlAttribute(): ?string
    {
        return $this->hasFile() ? route('books.download', $this->id) : null;
    }

    /** اسم بديل متوافق مع الفرونت (downloads_count بدل download_count) */
    public function getDownloadsCountAttribute(): int
    {
        return (int) $this->download_count;
    }

    /** تاريخ الإضافة بصيغة مقروءة */
    public function getAddedDateAttribute(): ?string
    {
        return $this->created_at?->format('Y-m-d');
    }
}