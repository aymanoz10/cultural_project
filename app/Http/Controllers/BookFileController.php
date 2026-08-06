<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * تقديم ملف الكتاب (PDF) عبر مسارين خلف نفس بوابة الصلاحية:
 *   - download() : تحميل الملف على الجهاز (attachment) + عدّ التحميلات.
 *   - read()     : قراءة الملف داخل المتصفح (inline) مع دعم Range للتصفّح بلا تنزيل كامل.
 *
 * الملف خاص (قرص books_private) ولا يُخدَم أبداً من مسار عام.
 * التصميم محايد عن القرص: لو صار file_disk = s3 يتحوّل تلقائياً إلى رابط موقّت موقّع.
 */
class BookFileController extends Controller
{
    /** تحميل الكتاب — يحفظه المستخدم على جهازه */
    public function download(Book $book)
    {
        $disk = $this->ensureFile($book);
        $book->increment('download_count');

        // قرص سحابي → رابط موقّت موقّع (لا تمرّ البايتات عبر الخادم)
        if ($this->isS3($book)) {
            return redirect($disk->temporaryUrl($book->file_path, now()->addMinutes(10), [
                'ResponseContentDisposition' => 'attachment; filename="'.$this->asciiName($book).'"',
            ]));
        }

        return $disk->download($book->file_path, $this->downloadName($book), [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /** قراءة الكتاب داخل المتصفح — عرض inline بلا حفظ مباشر، مع دعم Range */
    public function read(Book $book)
    {
        $disk = $this->ensureFile($book);

        if ($this->isS3($book)) {
            return redirect($disk->temporaryUrl($book->file_path, now()->addMinutes(10), [
                'ResponseContentDisposition' => 'inline; filename="'.$this->asciiName($book).'"',
            ]));
        }

        // BinaryFileResponse يدعم Range فيتصفّح القارئ الصفحات دون تنزيل الملف كاملاً
        $response = response()->file($disk->path($book->file_path), [
            'Content-Type' => 'application/pdf',
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

    /** يتحقّق من وجود الملف فعلياً ويعيد القرص، أو 404 */
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

    /** اسم عربي مقروء للتحميل (يتكفّل Symfony بترميز RFC 5987) */
    private function downloadName(Book $book): string
    {
        $base = $book->original_name ?: (($book->title ?: 'book-'.$book->id).'.pdf');

        return str_ends_with(mb_strtolower($base), '.pdf') ? $base : $base.'.pdf';
    }

    /** بديل ASCII آمن للترويسة (لعملاء لا يدعمون الترميز الموسّع) */
    private function asciiName(Book $book): string
    {
        return 'book-'.$book->id.'.pdf';
    }
}
