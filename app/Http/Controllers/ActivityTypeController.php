<?php

namespace App\Http\Controllers;

use App\Http\Resources\ActivityTypeResource;
use App\Models\ActivityType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ActivityTypeController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityType::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%");
            });
        }

        $types = $query->latest()->get();

        if ($request->wantsJson()) {
            return response()->json([
                'data' => ActivityTypeResource::collection($types),
            ]);
        }

        return view('admin.activity_types.index', compact('types'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['title', 'description']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('activity_types', 'public');
        }

        $type = ActivityType::create($data);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تم إضافة نوع الفعالية بنجاح',
                'data'    => new ActivityTypeResource($type),
            ], 201);
        }

        return redirect()->route('admin.activity_types.index')->with('success', 'تم إضافة نوع الفعالية بنجاح');
    }

    public function update(Request $request, $id)
    {
        $type = ActivityType::findOrFail($id);

        $request->validate([
            'title'       => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['title', 'description']);

        if ($request->hasFile('image')) {
            if ($type->image) {
                Storage::disk('public')->delete($type->image);
            }
            $data['image'] = $request->file('image')->store('activity_types', 'public');
        }

        $type->update($data);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تم تحديث نوع الفعالية بنجاح',
                'data'    => new ActivityTypeResource($type->fresh()),
            ]);
        }

        return redirect()->route('admin.activity_types.index')->with('success', 'تم تحديث نوع الفعالية بنجاح');
    }

    public function destroy(Request $request, $id)
    {
        $type = ActivityType::findOrFail($id);

        if ($type->image) {
            Storage::disk('public')->delete($type->image);
        }

        $type->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تم حذف نوع الفعالية بنجاح',
            ]);
        }

        return redirect()->route('admin.activity_types.index')->with('success', 'تم حذف نوع الفعالية بنجاح');
    }
}
