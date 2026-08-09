<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use App\Models\VenueType;
use App\Models\CulturalCenter;
use App\Http\Resources\VenueResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VenueController extends Controller
{
    /**
     * عرض قائمة القاعات والمرافق مع الفلترة والتصفح
     */
    public function index(Request $request)
    {
        // شحن العلاقات مسبقاً (Eager Loading) لتجنب مشكلة N+1
        $query = Venue::with(['culturalCenter', 'type']);

        // الفلترة حسب المركز الثقافي
        if ($request->filled('cultural_center_id') || $request->filled('center_id')) {
            $centerId = $request->input('cultural_center_id', $request->input('center_id'));
            $query->where('cultural_center_id', $centerId);
        }

        // الفلترة حسب نوع القاعة
        if ($request->filled('venue_type_id') || $request->filled('type')) {
            $typeId = $request->input('venue_type_id', $request->input('type'));
            $query->where('venue_type_id', $typeId);
        }

        // البحث باسم القاعة
        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $venuesList = $query->latest()->paginate(10)->withQueryString();
        $venuesCount = Venue::count();
        $totalCapacity = Venue::sum('capacity');
        $venueTypes = VenueType::where('is_active', true)->get();

        // إرجاع استجابة JSON إذا كان الطلب API
        if ($request->wantsJson()) {
            return VenueResource::collection($venuesList);
        }

        // إرجاع واجهة Blade للمتصفح
        return view('admin.venues.index', compact('venuesList', 'venuesCount', 'totalCapacity', 'venueTypes'));
    }

    /**
     * عرض صفحة إضافة قاعة جديدة
     */
    public function create()
    {
        $culturalCenters = CulturalCenter::all();
        $venueTypes = VenueType::where('is_active', true)->get();

        return view('admin.venues.create', compact('culturalCenters', 'venueTypes'));
    }

    /**
     * حفظ قاعة جديدة (يدعم AJAX و HTML Forms)
     */
    public function store(Request $request)
    {
        // توحيد اسم مسمى النوع إذا تم إرساله باسم 'type' من النموذج
        if ($request->has('type') && !$request->has('venue_type_id')) {
            $request->merge(['venue_type_id' => $request->input('type')]);
        }

        // معالجة الميزات إذا أُرسلت كنص مفصول بفواصل
        $this->normalizeFeaturesInput($request);

        $validated = $request->validate([
            'cultural_center_id' => 'required|exists:cultural_centers,id',
            'venue_type_id'      => 'required|exists:venue_types,id',
            'name'               => 'required|string|max:255',
            'capacity'           => 'required|integer|min:1',
            'features'           => 'nullable|array',
            'features.*'         => 'string',
            'image'              => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('venues', 'public');
        }

        $venue = Venue::create($validated);
        $venue->load(['culturalCenter', 'type']);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'تم إضافة القاعة بنجاح',
                'data'    => new VenueResource($venue),
            ], 201);
        }

        return redirect()->route('admin.venues.index')->with('success', 'تم إضافة القاعة بنجاح');
    }

    /**
     * عرض بيانات قاعة واحدة (API / JSON)
     */
    public function show($id)
    {
        $venue = Venue::with(['culturalCenter', 'type'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => new VenueResource($venue),
        ]);
    }

    /**
     * عرض صفحة تعديل قاعة
     */
    public function edit($id)
    {
        $venue = Venue::findOrFail($id);
        $culturalCenters = CulturalCenter::all();
        $venueTypes = VenueType::where('is_active', true)->get();

        return view('admin.venues.edit', compact('venue', 'culturalCenters', 'venueTypes'));
    }

    /**
     * تحديث بيانات القاعة (يدعم AJAX و HTML Forms)
     */
    public function update(Request $request, $id)
    {
        $venue = Venue::findOrFail($id);

        if ($request->has('type') && !$request->has('venue_type_id')) {
            $request->merge(['venue_type_id' => $request->input('type')]);
        }

        $this->normalizeFeaturesInput($request);

        $validated = $request->validate([
            'cultural_center_id' => 'sometimes|required|exists:cultural_centers,id',
            'venue_type_id'      => 'sometimes|required|exists:venue_types,id',
            'name'               => 'sometimes|required|string|max:255',
            'capacity'           => 'sometimes|required|integer|min:1',
            'features'           => 'nullable|array',
            'features.*'         => 'string',
            'image'              => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($venue->image) {
                Storage::disk('public')->delete($venue->image);
            }
            $validated['image'] = $request->file('image')->store('venues', 'public');
        }

        $venue->update($validated);
        $venue->load(['culturalCenter', 'type']);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'تم تعديل البيانات بنجاح',
                'data'    => new VenueResource($venue),
            ]);
        }

        return redirect()->route('admin.venues.index')->with('success', 'تم تعديل بيانات القاعة بنجاح');
    }

    /**
     * حذف قاعة
     */
    public function destroy(Request $request, $id)
    {
        $venue = Venue::findOrFail($id);

        if ($venue->image) {
            Storage::disk('public')->delete($venue->image);
        }

        $venue->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'تم حذف المكان بنجاح',
            ]);
        }

        return redirect()->route('admin.venues.index')->with('success', 'تم حذف القاعة بنجاح');
    }

    /**
     * دالة مساعدة لتحويل النص المفصول بفواصل إلى مصفوفة تلقائياً
     */
    private function normalizeFeaturesInput(Request $request): void
    {
        if ($request->has('features') && is_string($request->input('features'))) {
            $raw = $request->input('features');
            // التعديل تم هنا بإضافة علامات التنصيص حول النمط
            $array = array_filter(array_map('trim', preg_split('/[,،]/', $raw)));
            $request->merge(['features' => array_values($array)]);
        }
    }
}