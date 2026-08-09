<?php

namespace App\Http\Controllers;

use App\Http\Resources\CulturalCenterResource;
use App\Http\Resources\CulturalCenterPhotoResource;
use App\Http\Resources\VenueResource;
use App\Models\CulturalCenter;
use App\Models\CulturalCenterPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CulturalCenterController extends Controller
{
    /**
     * عرض قائمة المراكز الثقافية (يدعم Web و API)
     */
    public function index(Request $request)
    {
        $query = CulturalCenter::with(['photos', 'venues']);

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('location', 'like', "%{$searchTerm}%");
            });
        }

        $centers = $query->latest()->get();

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'data'    => CulturalCenterResource::collection($centers),
            ]);
        }

        return view('admin.cultural_centers.index', compact('centers'));
    }

    /**
     * عرض تفاصيل مركز ثقافي واحد مع القاعات/المقرات (venues) والصور
     */
    public function show(Request $request, $id)
    {
        // شحن العلاقات مسبقاً (photos و venues)
        $center = CulturalCenter::with(['photos', 'venues.type'])->findOrFail($id);

        // 🟢 إرجاع استجابة JSON للـ API وتطبيق Flutter
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'data'    => [
                    'id'           => $center->id,
                    'name'         => $center->name,
                    'location'     => $center->location,
                    'map_location' => $center->map_location,
                    'description'  => $center->description,
                    'features'     => $center->features,
                    'photos'       => CulturalCenterPhotoResource::collection($center->photos),
                    'venues'       => VenueResource::collection($center->venues), // إرجاع القاعات باستخدام VenueResource
                    'created_at'   => $center->created_at?->format('Y-m-d H:i:s'),
                ],
            ]);
        }

        // 🔵 إرجاع واجهة Blade للويب
        return view('admin.cultural_centers.show', compact('center'));
    }

    public function create()
    {
        return view('admin.cultural_centers.create');
    }

    public function editView($id)
    {
        $center = CulturalCenter::findOrFail($id);
        return view('admin.cultural_centers.edit', compact('center'));
    }

    /**
     * إضافة مركز جديد (يدعم Web و API)
     */
    public function add(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'location'    => 'required|string|max:255',
            'map_location'=> 'nullable|string',
            'description' => 'nullable|string',
            'features'    => 'nullable|array',
            'features.*'  => 'string',
        ]);

        $center = CulturalCenter::create($request->only(['name', 'location', 'map_location', 'description', 'features']));

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'message' => 'تم إضافة المركز بنجاح',
                'data'    => new CulturalCenterResource($center->load(['photos', 'venues'])),
            ], 201);
        }

        return redirect()->route('admin.cultural_centers.index')->with('success', 'تم إضافة المركز بنجاح');
    }

    /**
     * تعديل بيانات مركز (يدعم Web و API)
     */
    public function edit(Request $request, $id)
    {
        $center = CulturalCenter::findOrFail($id);

        $request->validate([
            'name'        => 'sometimes|required|string|max:255',
            'location'    => 'sometimes|required|string|max:255',
            'map_location'=> 'nullable|string',
            'description' => 'nullable|string',
            'features'    => 'nullable|array',
            'features.*'  => 'string',
        ]);

        $center->update($request->only(['name', 'location', 'map_location', 'description', 'features']));

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'message' => 'تم تحديث المركز بنجاح',
                'data'    => new CulturalCenterResource($center->fresh()->load(['photos', 'venues'])),
            ]);
        }

        return redirect()->route('admin.cultural_centers.index')->with('success', 'تم تحديث المركز بنجاح');
    }

    /**
     * حذف مركز ثقافي (يدعم Web و API)
     */
    public function remove($id, Request $request)
    {
        $center = CulturalCenter::findOrFail($id);

        foreach ($center->photos as $photo) {
            Storage::disk('public')->delete($photo->photo);
        }

        $center->delete();

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'message' => 'تم حذف المركز بنجاح',
            ]);
        }

        return redirect()->route('admin.cultural_centers.index')->with('success', 'تم حذف المركز بنجاح');
    }

    /**
     * إضافة صور للمركز
     */
    public function addPhotos(Request $request, $id)
    {
        $center = CulturalCenter::findOrFail($id);

        $request->validate([
            'photos'   => 'required|array|min:1',
            'photos.*' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $savedPhotos = [];

        foreach ($request->file('photos') as $file) {
            $path = $file->store('cultural_centers', 'public');
            $photo = $center->photos()->create(['photo' => $path]);
            $savedPhotos[] = asset('storage/' . $photo->photo);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة الصور بنجاح',
            'photos'  => $savedPhotos,
        ], 201);
    }

    /**
     * حذف صورة واحدة للمركز
     */
    public function removePhoto($id)
    {
        $photo = CulturalCenterPhoto::findOrFail($id);

        Storage::disk('public')->delete($photo->photo);
        $photo->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الصورة بنجاح',
        ]);
    }
}