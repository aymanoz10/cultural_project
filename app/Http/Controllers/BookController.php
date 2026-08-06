<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Library;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    /**
     * قواعد التحقق مع رسائل عربية
     */
    private function messages(): array
    {
        return [
            'library_id.required' => 'يجب اختيار المكتبة.',
            'library_id.exists'   => 'المكتبة المحددة غير موجودة.',
            'title.required'      => 'عنوان الكتاب مطلوب.',
            'title.max'           => 'العنوان يجب ألا يتجاوز 255 حرفاً.',
            'author.required'     => 'اسم المؤلف مطلوب.',
            'author.max'          => 'اسم المؤلف يجب ألا يتجاوز 255 حرفاً.',
            'category.required'   => 'تصنيف الكتاب مطلوب.',
            'category.max'        => 'التصنيف يجب ألا يتجاوز 255 حرفاً.',
            'language.required'   => 'لغة الكتاب مطلوبة.',
            'language.max'        => 'اللغة يجب ألا تتجاوز 255 حرفاً.',
            'description.string'  => 'الوصف يجب أن يكون نصاً.',
            'pages_count.integer' => 'عدد الصفحات يجب أن يكون رقماً صحيحاً.',
            'pages_count.min'     => 'عدد الصفحات يجب أن يكون 1 على الأقل.',
            'file_size.max'       => 'حجم الملف يجب ألا يتجاوز 50 حرفاً.',
            'cover_image.image'   => 'صورة الغلاف يجب أن تكون صورة صالحة.',
            'cover_image.mimes'   => 'صيغة صورة الغلاف يجب أن تكون jpeg أو png أو jpg أو webp.',
            'cover_image.max'     => 'حجم صورة الغلاف يجب ألا يتجاوز 2 ميجابايت.',
            'file.mimetypes'      => 'ملف الكتاب يجب أن يكون بصيغة PDF.',
            'file.mimes'          => 'ملف الكتاب يجب أن يكون بصيغة PDF.',
            'file.max'            => 'حجم ملف الكتاب يجب ألا يتجاوز 50 ميجابايت.',
        ];
    }

    /**
     * يخزّن ملف PDF على القرص الخاص ويعيد أعمدة الملف المحسوبة (لا مكتوبة يدوياً).
     */
    private function storePdf(\Illuminate\Http\UploadedFile $file): array
    {
        // يُحسب قبل النقل: getRealPath صالح فقط قبل store()
        $bytes = $file->getSize();
        $hash  = hash_file('sha256', $file->getRealPath());

        return [
            'file_path'       => $file->store('', 'books_private'),
            'file_disk'       => 'books_private',
            'original_name'   => $file->getClientOriginalName(),
            'mime_type'       => 'application/pdf',
            'file_size_bytes' => $bytes,
            'sha256'          => $hash,
            'file_size'       => $this->formatBytes($bytes), // يملأ الحقل النصّي القديم تلقائياً
        ];
    }

    /** تنسيق الحجم بالبايت إلى نص عربي مقروء */
    private function formatBytes(int $bytes): string
    {
        if ($bytes <= 0) {
            return '0 بايت';
        }
        $units = ['بايت', 'كيلوبايت', 'ميجابايت', 'جيجابايت'];
        $i = (int) floor(log($bytes, 1024));
        $i = min($i, count($units) - 1);

        return round($bytes / (1024 ** $i), 2) . ' ' . $units[$i];
    }

    /**
     * عرض قائمة الكتب مع الفلترة والتصفح
     */
    public function index(Request $request)
    {
        $query = Book::with('library')->latest();

        // البحث في العنوان أو المؤلف أو التصنيف (غير حساس لحالة الأحرف — PostgreSQL ilike)
        if ($request->filled('search')) {
            $term = "%{$request->search}%";
            $query->where(function ($q) use ($term) {
                $q->where('title', 'ilike', $term)
                  ->orWhere('author', 'ilike', $term)
                  ->orWhere('category', 'ilike', $term);
            });
        }

        // الفلترة حسب التصنيف
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // الفلترة حسب المكتبة
        if ($request->filled('library_id')) {
            $query->where('library_id', $request->library_id);
        }

        $books = $query->paginate(12)->withQueryString();

        $categories = Book::query()
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $libraries = Library::all();

        return view('admin.books.index', compact('books', 'categories', 'libraries'));
    }

    /**
     * عرض صفحة إضافة كتاب جديد
     */
    public function create()
    {
        return view('admin.books.create', ['libraries' => Library::all()]);
    }

    /**
     * حفظ كتاب جديد (AJAX / JSON)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'library_id'  => 'required|exists:libraries,id',
            'title'       => 'required|string|max:255',
            'author'      => 'required|string|max:255',
            'category'    => 'required|string|max:255',
            'language'    => 'required|string|max:255',
            'description' => 'nullable|string',
            'pages_count' => 'nullable|integer|min:1',
            'file_size'   => 'nullable|string|max:50',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'file'        => 'nullable|file|mimetypes:application/pdf|mimes:pdf|max:51200',
            'is_available' => 'boolean',
        ], $this->messages());

        $validated['is_available'] = $request->boolean('is_available');

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('books', 'public');
        }

        if ($request->hasFile('file')) {
            $validated = array_merge($validated, $this->storePdf($request->file('file')));
        }
        unset($validated['file']);

        $book = Book::create($validated);
        $book->load('library');

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة الكتاب بنجاح',
            'data'    => $book,
        ], 201);
    }

    /**
     * عرض بيانات كتاب واحد (واجهة قراءة فقط)
     */
    public function show($id)
    {
        $book = Book::with('library')->findOrFail($id);

        return view('admin.books.show', compact('book'));
    }

    /**
     * عرض صفحة تعديل كتاب
     */
    public function editView($id)
    {
        return view('admin.books.edit', [
            'book'      => Book::findOrFail($id),
            'libraries' => Library::all(),
        ]);
    }

    /**
     * تحديث بيانات الكتاب (AJAX / JSON)
     */
    public function update(Request $request, $id)
    {
        $book = Book::findOrFail($id);

        $validated = $request->validate([
            'library_id'  => 'sometimes|required|exists:libraries,id',
            'title'       => 'sometimes|required|string|max:255',
            'author'      => 'sometimes|required|string|max:255',
            'category'    => 'sometimes|required|string|max:255',
            'language'    => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'pages_count' => 'nullable|integer|min:1',
            'file_size'   => 'nullable|string|max:50',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'file'        => 'nullable|file|mimetypes:application/pdf|mimes:pdf|max:51200',
            'is_available' => 'boolean',
        ], $this->messages());

        $validated['is_available'] = $request->boolean('is_available');

        if ($request->hasFile('cover_image')) {
            if ($book->cover_image) {
                Storage::disk('public')->delete($book->cover_image);
            }
            $validated['cover_image'] = $request->file('cover_image')->store('books', 'public');
        }

        if ($request->hasFile('file')) {
            if ($book->file_path) {
                Storage::disk($book->file_disk)->delete($book->file_path);
            }
            $validated = array_merge($validated, $this->storePdf($request->file('file')));
        }
        unset($validated['file']);

        $book->update($validated);
        $book->load('library');

        return response()->json([
            'success' => true,
            'message' => 'تم تعديل بيانات الكتاب بنجاح',
            'data'    => $book,
        ]);
    }

    /**
     * حذف كتاب
     */
    public function destroy($id)
    {
        $book = Book::findOrFail($id);

        if ($book->cover_image) {
            Storage::disk('public')->delete($book->cover_image);
        }

        if ($book->file_path) {
            Storage::disk($book->file_disk)->delete($book->file_path);
        }

        $book->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الكتاب بنجاح',
        ]);
    }
}
