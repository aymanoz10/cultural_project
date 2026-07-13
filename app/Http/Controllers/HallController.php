<?php

namespace App\Http\Controllers;

use App\Models\Hall;
use App\Http\Resources\HallResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HallController extends Controller
{
    public function index(Request $request)
    {
        $query = Hall::query();

        if ($request->has('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        if ($request->has('center_id')) {
            $query->where('cultural_center_id', $request->center_id);
        }

        return HallResource::collection($query->get());
    }

    public function add(Request $request)
    {
        $request->validate([
            'cultural_center_id' => 'required|exists:cultural_centers,id',
            'name'               => 'required|string',
            'capacity'           => 'required|integer|min:1',
            'features'           => 'nullable|array',
            'features.*'         => 'string',
            'image'             => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->only(['cultural_center_id', 'name', 'capacity', 'features']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('halls', 'public');
        }

        $hall = Hall::create($data);

        return response()->json(['success' => true, 'data' => new HallResource($hall)], 201);
    }

    public function edit(Request $request, $id)
    {
        $hall = Hall::findOrFail($id);

        $request->validate([
            'name'       => 'sometimes|required|string',
            'capacity'   => 'sometimes|required|integer|min:1',
            'features'   => 'nullable|array',
            'features.*' => 'string',
            'image'     => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->only(['name', 'capacity', 'features']);

        if ($request->hasFile('image')) {
            if ($hall->image) {
                Storage::disk('public')->delete($hall->image);
            }
            $data['image'] = $request->file('image')->store('halls', 'public');
        }

        $hall->update($data);

        return response()->json(['success' => true, 'data' => new HallResource($hall)], 200);
    }

    public function remove($id)
    {
        $hall = Hall::findOrFail($id);

        if ($hall->image) {
            Storage::disk('public')->delete($hall->image);
        }

        $hall->delete();

        return response()->json(['success' => true], 200);
    }
}
