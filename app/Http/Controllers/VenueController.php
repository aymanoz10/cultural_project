<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use App\Http\Resources\VenueResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class VenueController extends Controller
{
    public function index(Request $request)
    {
        $query = Venue::with('culturalCenter');

        if ($request->filled('center_id')) {
            $query->where('cultural_center_id', $request->center_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        return VenueResource::collection($query->latest()->get());
    }

    public function show($id)
    {
        $venue = Venue::with('culturalCenter')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => new VenueResource($venue),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'cultural_center_id' => 'required|exists:cultural_centers,id',
            'name'               => 'required|string|max:255',
            'type'               => ['required', Rule::in(Venue::TYPES)],
            'capacity'           => 'required|integer|min:1',
            'features'           => 'nullable|array',
            'features.*'         => 'string',
            'image'              => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = $request->only([
            'cultural_center_id', 'name', 'type', 'capacity', 'features',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('venues', 'public');
        }

        $venue = Venue::create($data);

        return response()->json([
            'success' => true,
            'data'    => new VenueResource($venue),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $venue = Venue::findOrFail($id);

        $request->validate([
            'name'       => 'sometimes|required|string|max:255',
            'type'       => ['sometimes', 'required', Rule::in(Venue::TYPES)],
            'capacity'   => 'sometimes|required|integer|min:1',
            'features'   => 'nullable|array',
            'features.*' => 'string',
            'image'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = $request->only(['name', 'type', 'capacity', 'features']);

        if ($request->hasFile('image')) {
            if ($venue->image) {
                Storage::disk('public')->delete($venue->image);
            }
            $data['image'] = $request->file('image')->store('venues', 'public');
        }

        $venue->update($data);

        return response()->json([
            'success' => true,
            'data'    => new VenueResource($venue->fresh()),
        ]);
    }

    public function destroy($id)
    {
        $venue = Venue::findOrFail($id);

        if ($venue->image) {
            Storage::disk('public')->delete($venue->image);
        }

        $venue->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف المكان بنجاح',
        ]);
    }
}
