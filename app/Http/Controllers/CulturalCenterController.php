<?php

namespace App\Http\Controllers;

use App\Http\Resources\CulturalCenterResource;
use App\Models\CulturalCenter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CulturalCenterController extends Controller
{
  public function index(Request $request)
{
    $query = CulturalCenter::query();

    if ($request->has('search') && !empty($request->search)) {
        $searchTerm = $request->search;
        
        $query->where('name', 'like', "%{$searchTerm}%")
              ->orWhere('location', 'like', "%{$searchTerm}%");
    }

    $centers = $query->get();

    return CulturalCenterResource::collection($centers);
}

    public function add(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'location' => 'required|string',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('images', 'public');
        }

        $center = CulturalCenter::create($data);

        return response()->json(['success' => true, 'message' => 'تم إضافة المركز بنجاح', 'data' => $center], 201);
    }
    public function edit(Request $request, $id)
    {
        $center = CulturalCenter::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|string',
            'location' => 'sometimes|string',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->only(['name', 'location', 'description']);

        // تحديث الصورة إذا تم رفع صورة جديدة
        if ($request->hasFile('image')) {
            // حذف الصورة القديمة إذا وجدت
            if ($center->image) {
                Storage::disk('public')->delete($center->image);
            }
            $data['image'] = $request->file('image')->store('images', 'public');
        }

        $center->update($data);

        return response()->json(['success' => true, 'message' => 'تم تحديث المركز بنجاح', 'data' => $center], 200);
    }

    // حذف المركز مع حذف صورته من التخزين
    public function remove($id)
    {
        $center = CulturalCenter::findOrFail($id);
        
        if ($center->image) {
            Storage::disk('public')->delete($center->image);
        }
        
        $center->delete();
        return response()->json(['success' => true, 'message' => 'تم حذف المركز بنجاح'], 200);
    }
    
}