<?php

namespace App\Http\Controllers;

use App\Http\Resources\CulturalCenterResource;
use App\Models\CulturalCenter;
use App\Models\CulturalCenterPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CulturalCenterController extends Controller
{
    public function index(Request $request)
    {
        $query = CulturalCenter::with('photos');

        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('location', 'like', "%{$searchTerm}%");
        }

        return CulturalCenterResource::collection($query->latest()->get());
    }

    public function add(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'location'    => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $center = CulturalCenter::create($request->only(['name', 'location', 'description']));

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة المركز بنجاح',
            'data'    => new CulturalCenterResource($center),
        ], 201);
    }

    public function edit(Request $request, $id)
    {
        $center = CulturalCenter::findOrFail($id);

        $request->validate([
            'name'        => 'sometimes|required|string|max:255',
            'location'    => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $center->update($request->only(['name', 'location', 'description']));

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث المركز بنجاح',
            'data'    => new CulturalCenterResource($center->fresh()->load('photos')),
        ]);
    }

    public function remove($id)
    {
        $center = CulturalCenter::findOrFail($id);

        foreach ($center->photos as $photo) {
            Storage::disk('public')->delete($photo->photo);
        }

        $center->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف المركز بنجاح',
        ]);
    }

    public function addPhotos(Request $request, $id)
    {
        $center = CulturalCenter::findOrFail($id);

        $request->validate([
            'photos'   => 'required|array|min:1',
            'photos.*' => 'required|image|mimes:jpeg,png,jpg|max:2048',
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
