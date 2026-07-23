<?php

namespace App\Http\Controllers;

use App\Models\Hall;
use App\Http\Resources\HallResource;
use App\Models\CulturalCenter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HallController extends Controller
{
    public function index(Request $request)
    {
        $query = Hall::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        if ($request->filled('center_id')) {
            $query->where('cultural_center_id', $request->center_id);
        }

        $halls = $query->get();

        if ($request->wantsJson()) {
            return HallResource::collection($halls);
        }

        return view('admin.halls.index', compact('halls'));
    }

   public function create()
{
    $culturalCenters = CulturalCenter::select('id', 'name')->get();
    return view('admin.halls.create', compact('culturalCenters'));
}

    public function store(Request $request)
    {
        $request->validate([
            'cultural_center_id' => 'required|exists:cultural_centers,id',
            'name'               => 'required|string',
            'capacity'           => 'required|integer|min:1',
            'features'           => 'nullable|array',
            'features.*'         => 'string',
            'image'              => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->only(['cultural_center_id', 'name', 'capacity', 'features']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('halls', 'public');
        }

        $hall = Hall::create($data);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => new HallResource($hall)], 201);
        }

        return redirect()->route('admin.halls.index')->with('success', 'تم إضافة القاعة بنجاح');
    }

    public function editView($id)
     {
    $hall = Hall::findOrFail($id); // أو $theater = Theater::findOrFail($id);
    $culturalCenters = CulturalCenter::all();
    return view('admin.halls.edit', compact('hall', 'culturalCenters')); // أو admin.theaters.edit
     }

    public function update(Request $request, $id)
    {
        $hall = Hall::findOrFail($id);

        $request->validate([
            'name'       => 'sometimes|required|string',
            'capacity'   => 'sometimes|required|integer|min:1',
            'features'   => 'nullable|array',
            'features.*' => 'string',
            'image'      => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->only(['name', 'capacity', 'features']);

        if ($request->hasFile('image')) {
            if ($hall->image) {
                Storage::disk('public')->delete($hall->image);
            }
            $data['image'] = $request->file('image')->store('halls', 'public');
        }

        $hall->update($data);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => new HallResource($hall)], 200);
        }

        return redirect()->route('admin.halls.index')->with('success', 'تم تحديث القاعة بنجاح');
    }

    public function destroy($id, Request $request)
    {
        $hall = Hall::findOrFail($id);

        if ($hall->image) {
            Storage::disk('public')->delete($hall->image);
        }

        $hall->delete();

        if ($request->wantsJson()) {
            return response()->json(['success' => true], 200);
        }

        return redirect()->route('admin.halls.index')->with('success', 'تم حذف القاعة بنجاح');
    }
    
}
