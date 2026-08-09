<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class BookFileController extends Controller
{
    /** تحميل الكتاب — يعمل كتحميل مباشر للويب ولـ Flutter Dio/Http */
    public function download(Request $request, Book $book)
    {
        $disk = $this->ensureFile($book);
        $book->increment('download_count');

        // إذا كان التخزين سحابياً (S3)
        if ($this->isS3($book)) {
            $temporaryUrl = $disk->temporaryUrl($book->file_path, now()->addMinutes(10), [
                'ResponseContentDisposition' => 'attachment; filename="'.$this->asciiName($book).'"',
            ]);

            // إذا كان طلب Flutter يتوقع JSON بدلاً من التوجيه التلقائي
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'download_url' => $temporaryUrl,
                ]);
            }

            return redirect($temporaryUrl);
        }

        // التخزين المحلي: يرجع الملف كـ Stream باينري مباشر لتطبيقات Flutter والويب
        return $disk->download($book->file_path, $this->downloadName($book), [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /** قراءة الكتاب — عرض inline داخل المتصفح أو قارئ PDF في Flutter */
    public function read(Request $request, Book $book)
    {
        $disk = $this->ensureFile($book);

        // إذا كان التخزين سحابياً (S3)
        if ($this->isS3($book)) {
            $temporaryUrl = $disk->temporaryUrl($book->file_path, now()->addMinutes(10), [
                'ResponseContentDisposition' => 'inline; filename="'.$this->asciiName($book).'"',
            ]);

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'read_url' => $temporaryUrl,
                ]);
            }

            return redirect($temporaryUrl);
        }

        // التخزين المحلي: BinaryFileResponse يدعم Range تلقائياً لتصفح الصفحات دون تنزيل كامل الملف
        $response = response()->file($disk->path($book->file_path), [
            'Content-Type' => 'application/pdf',
            'Access-Control-Allow-Origin' => '*', // يمنع مشاكل CORS عند القراءة من Flutter Web أو WebViews
        ]);

        $response->headers->set(
            'Content-Disposition',
            $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_INLINE,
                $this->downloadName($book),
                $this->asciiName($book)
            )
        );

        return $response;
    }

    /** يتحقق من وجود الملف فعلياً ويعيد القرص، أو 404 */
    private function ensureFile(Book $book)
    {
        abort_unless($book->hasFile(), 404, 'لا يوجد ملف لهذا الكتاب');

        $disk = Storage::disk($book->file_disk);
        abort_unless($disk->exists($book->file_path), 404, 'الملف غير موجود على الخادم');

        return $disk;
    }

    private function isS3(Book $book): bool
    {
        return config("filesystems.disks.{$book->file_disk}.driver") === 's3';
    }

    /** اسم عربي مقروء للتحميل */
    private function downloadName(Book $book): string
    {
        $base = $book->original_name ?: (($book->title ?: 'book-'.$book->id).'.pdf');

        return str_ends_with(mb_strtolower($base), '.pdf') ? $base : $base.'.pdf';
    }

    /** بديل ASCII آمن للترويسة */
    private function asciiName(Book $book): string
    {
        return 'book-'.$book->id.'.pdf';
    }
}
