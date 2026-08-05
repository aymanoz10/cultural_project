<?php

namespace App\Http\Controllers;

use App\Models\CulturalCenter;
use App\Models\Library;
use App\Http\Resources\LibraryResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LibraryController extends Controller
{
    // ==========================================================
    // 🔌 واجهة الـ API (للتطبيق) — تبقى كما هي دون تغيير
    // ==========================================================

    public function index() { return LibraryResource::collection(Library::all()); }

    public function add(Request $request)
    {
        $request->validate(['cultural_center_id' => 'required|exists:cultural_centers,id', 'name' => 'required']);
        $data = $request->only(['cultural_center_id', 'name']);
        if ($request->hasFile('image')) $data['image'] = $request->file('image')->store('libraries', 'public');
        return response()->json(['data' => Library::create($data)], 201);
    }

    public function edit(Request $request, $id)
    {
        $lib = Library::findOrFail($id);
        $data = $request->only(['name']);
        if ($request->hasFile('image')) {
            if ($lib->image) Storage::disk('public')->delete($lib->image);
            $data['image'] = $request->file('image')->store('libraries', 'public');
        }
        $lib->update($data);
        return response()->json(['data' => $lib], 200);
    }

    public function remove($id)
    {
        $lib = Library::findOrFail($id);
        if ($lib->image) Storage::disk('public')->delete($lib->image);
        $lib->delete();
        return response()->json(['success' => true], 200);
    }

    // ==========================================================
    // 🖥️ واجهة الإدارة (Web + JSON للـ AJAX) — على نمط وحدة الكتب
    // ==========================================================

    /**
     * رسائل التحقق العربية
     */
    private function messages(): array
    {
        return [
            'cultural_center_id.required' => 'يجب اختيار المركز الثقافي.',
            'cultural_center_id.exists'   => 'المركز الثقافي المحدد غير موجود.',
            'name.required'               => 'اسم المكتبة مطلوب.',
            'name.max'                    => 'اسم المكتبة يجب ألا يتجاوز 255 حرفاً.',
            'image.image'                 => 'صورة المكتبة يجب أن تكون صورة صالحة.',
            'image.mimes'                 => 'صيغة الصورة يجب أن تكون jpeg أو png أو jpg أو webp.',
            'image.max'                   => 'حجم الصورة يجب ألا يتجاوز 2 ميجابايت.',
        ];
    }

    /**
     * عرض قائمة المكتبات مع الفلترة والتصفح (واجهة إدارة)
     */
    public function webIndex(Request $request)
    {
        $query = Library::with('culturalCenter')->withCount('books')->latest();

        // البحث باسم المكتبة (غير حساس لحالة الأحرف — PostgreSQL ilike)
        if ($request->filled('search')) {
            $query->where('name', 'ilike', "%{$request->search}%");
        }

        // الفلترة حسب المركز الثقافي
        if ($request->filled('cultural_center_id')) {
            $query->where('cultural_center_id', $request->cultural_center_id);
        }

        $libraries = $query->paginate(12)->withQueryString();
        $culturalCenters = CulturalCenter::all();

        return view('admin.libraries.index', compact('libraries', 'culturalCenters'));
    }

    /**
     * عرض صفحة إضافة مكتبة جديدة
     */
    public function create()
    {
        return view('admin.libraries.create', ['culturalCenters' => CulturalCenter::all()]);
    }

    /**
     * حفظ مكتبة جديدة (AJAX / JSON)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cultural_center_id' => 'required|exists:cultural_centers,id',
            'name'               => 'required|string|max:255',
            'image'              => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], $this->messages());

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('libraries', 'public');
        }

        $library = Library::create($validated);
        $library->load('culturalCenter');

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة المكتبة بنجاح',
            'data'    => $library,
        ], 201);
    }

    /**
     * عرض تفاصيل مكتبة واحدة مع كتبها (واجهة قراءة فقط)
     */
    public function show($id)
    {
        $library = Library::with(['culturalCenter', 'books'])->findOrFail($id);

        return view('admin.libraries.show', compact('library'));
    }

    /**
     * عرض صفحة تعديل مكتبة
     */
    public function editView($id)
    {
        return view('admin.libraries.edit', [
            'library'         => Library::findOrFail($id),
            'culturalCenters' => CulturalCenter::all(),
        ]);
    }

    /**
     * تحديث بيانات المكتبة (AJAX / JSON)
     */
    public function update(Request $request, $id)
    {
        $library = Library::findOrFail($id);

        $validated = $request->validate([
            'cultural_center_id' => 'sometimes|required|exists:cultural_centers,id',
            'name'               => 'sometimes|required|string|max:255',
            'image'              => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], $this->messages());

        if ($request->hasFile('image')) {
            if ($library->image) {
                Storage::disk('public')->delete($library->image);
            }
            $validated['image'] = $request->file('image')->store('libraries', 'public');
        }

        $library->update($validated);
        $library->load('culturalCenter');

        return response()->json([
            'success' => true,
            'message' => 'تم تعديل بيانات المكتبة بنجاح',
            'data'    => $library,
        ]);
    }

    /**
     * حذف مكتبة (وكتبها تُحذف تلقائياً عبر onDelete cascade)
     */
    public function destroy($id)
    {
        $library = Library::findOrFail($id);

        if ($library->image) {
            Storage::disk('public')->delete($library->image);
        }

        $library->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف المكتبة بنجاح',
        ]);
    }
}
