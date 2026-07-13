<?php

namespace App\Http\Controllers;

use App\Models\Theater;
use App\Http\Resources\TheaterResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TheaterController extends Controller
{
    public function index(Request $request)
    {
        $query = Theater::query();

        if ($request->has('center_id')) {
            $query->where('cultural_center_id', $request->center_id);
        }

        if ($request->has('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        return TheaterResource::collection($query->get());
    }

    public function add(Request $request)
    {
        $request->validate([
            'cultural_center_id' => 'required|exists:cultural_centers,id',
            'name'               => 'required|string',
            'capacity'           => 'required|integer|min:1',
            'description'        => 'nullable|string',
            'features'           => 'nullable|array',
            'features.*'         => 'string',
            'image'             => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['cultural_center_id', 'name', 'capacity', 'description', 'features']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('theaters', 'public');
        }

        $theater = Theater::create($data);

        return response()->json(['success' => true, 'data' => new TheaterResource($theater)], 201);
    }

    public function edit(Request $request, $id)
    {
        $theater = Theater::findOrFail($id);

        $request->validate([
            'name'        => 'sometimes|required|string',
            'capacity'    => 'sometimes|required|integer|min:1',
            'description' => 'nullable|string',
            'features'    => 'nullable|array',
            'features.*'  => 'string',
            'image'      => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['name', 'capacity', 'description', 'features']);

        if ($request->hasFile('image')) {
            if ($theater->image) {
                Storage::disk('public')->delete($theater->image);
            }
            $data['image'] = $request->file('image')->store('theaters', 'public');
        }

        $theater->update($data);

        return response()->json(['success' => true, 'data' => new TheaterResource($theater)], 200);
    }

    public function remove($id)
    {
        $theater = Theater::findOrFail($id);

        if ($theater->image) {
            Storage::disk('public')->delete($theater->image);
        }

        $theater->delete();

        return response()->json(['success' => true, 'message' => 'تم حذف المسرح'], 200);
    }
}
