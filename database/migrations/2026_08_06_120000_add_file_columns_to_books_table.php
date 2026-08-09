<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * إضافة أعمدة ملف الكتاب (PDF) — يُخزَّن على قرص خاص ويُقدَّم عبر متحكّم فقط.
     */
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->string('file_path')->nullable()->after('cover_image');      // مفتاح التخزين (لا رابط)
            $table->string('file_disk')->default('books_private')->after('file_path'); // اسم القرص (مرونة S3 لاحقاً)
            $table->string('original_name')->nullable()->after('file_disk');    // اسم العرض عند التحميل
            $table->string('mime_type', 100)->nullable()->after('original_name');
            $table->unsignedBigInteger('file_size_bytes')->nullable()->after('mime_type'); // محسوب لا مكتوب يدوياً
            $table->char('sha256', 64)->nullable()->after('file_size_bytes');    // تحقّق سلامة + منع تكرار
            $table->unsignedInteger('download_count')->default(0)->after('sha256');

            $table->index('sha256');
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropIndex(['sha256']);
            $table->dropColumn([
                'file_path', 'file_disk', 'original_name',
                'mime_type', 'file_size_bytes', 'sha256', 'download_count',
            ]);
        });
    }
};
