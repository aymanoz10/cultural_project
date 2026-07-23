<?php

namespace App\Http\Controllers;

use App\Models\Theater;
use App\Models\CulturalCenter;
use App\Http\Resources\TheaterResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TheaterController extends Controller
{
    /**
     * عرض قائمة المسارح مع إمكانية التصفية
     */
    public function index(Request $request)
    {
        $query = Theater::with('culturalCenter');

        if ($request->filled('center_id')) {
            $query->where('cultural_center_id', $request->center_id);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $theaters = $query->latest()->get();

        if ($request->wantsJson()) {
            return TheaterResource::collection($theaters);
        }

        return view('admin.theaters.index', compact('theaters'));
    }

    /**
     * واجهة إنشاء مسرح جديد
     */
    public function create()
    {
        $culturalCenters = CulturalCenter::select('id', 'name')->get();
        return view('admin.theaters.create', compact('culturalCenters'));
    }

    /**
     * تخزين مسرح جديد
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cultural_center_id' => 'required|exists:cultural_centers,id',
            'name'               => 'required|string|max:255',
            'capacity'           => 'required|integer|min:1',
            'description'        => 'nullable|string',
            'features'           => 'nullable|array',
            'features.*'         => 'string',
            'image'              => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('theaters', 'public');
        }

        $theater = Theater::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تم إضافة المسرح بنجاح',
                'data'    => new TheaterResource($theater)
            ], 201);
        }

        return redirect()->route('admin.theaters.index')->with('success', 'تم إضافة المسرح بنجاح');
    }

    /**
     * واجهة تعديل المسرح
     */
    public function editView($id)
    {
        $theater = Theater::findOrFail($id);
        $culturalCenters = CulturalCenter::select('id', 'name')->get();

        return view('admin.theaters.edit', compact('theater', 'culturalCenters'));
    }

    /**
     * تحديث بيانات المسرح
     */
    public function update(Request $request, $id)
    {
        $theater = Theater::findOrFail($id);

        $validated = $request->validate([
            'cultural_center_id' => 'sometimes|required|exists:cultural_centers,id',
            'name'               => 'sometimes|required|string|max:255',
            'capacity'           => 'sometimes|required|integer|min:1',
            'description'        => 'nullable|string',
            'features'           => 'nullable|array',
            'features.*'         => 'string',
            'image'              => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($theater->image) {
                Storage::disk('public')->delete($theater->image);
            }
            $validated['image'] = $request->file('image')->store('theaters', 'public');
        }

        $theater->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تم تحديث المسرح بنجاح',
                'data'    => new TheaterResource($theater)
            ], 200);
        }

        return redirect()->route('admin.theaters.index')->with('success', 'تم تحديث المسرح بنجاح');
    }

    /**
     * حذف مسرح
     */
    public function destroy($id, Request $request)
    {
        $theater = Theater::findOrFail($id);

        if ($theater->image) {
            Storage::disk('public')->delete($theater->image);
        }

        $theater->delete();

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'تم حذف المسرح بنجاح'], 200);
        }

        return redirect()->route('admin.theaters.index')->with('success', 'تم حذف المسرح بنجاح');
    }
}