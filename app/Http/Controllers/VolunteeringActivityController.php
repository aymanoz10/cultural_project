<?php

namespace App\Http\Controllers;

use App\Models\VolunteeringActivity;
use App\Http\Resources\VolunteeringActivityResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VolunteeringActivityController extends Controller
{
    public function index(Request $request)
    {
        $query = VolunteeringActivity::query();

        if ($request->has('center_id')) {
            $query->where('cultural_center_id', $request->center_id);
        }

        if ($request->has('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        return VolunteeringActivityResource::collection($query->latest('start_time')->get());
    }

    public function add(Request $request)
    {
        $request->validate([
            'cultural_center_id' => 'nullable|exists:cultural_centers,id',
            'title'              => 'required|string|max:255',
            'description'      => 'nullable|string',
            'location'           => 'required|string|max:255',
            'start_time'         => 'required|date',
            'image'              => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->only(['cultural_center_id', 'title', 'description', 'location', 'start_time']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('volunteering-activities', 'public');
        }

        $activity = VolunteeringActivity::create($data);

        return response()->json([
            'success' => true,
            'data'    => new VolunteeringActivityResource($activity),
        ], 201);
    }

    public function edit(Request $request, $id)
    {
        $activity = VolunteeringActivity::findOrFail($id);

        $request->validate([
            'title'       => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'location'    => 'sometimes|required|string|max:255',
            'start_time'  => 'sometimes|required|date',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->only(['cultural_center_id', 'title', 'description', 'location', 'start_time']);

        if ($request->hasFile('image')) {
            if ($activity->image) {
                Storage::disk('public')->delete($activity->image);
            }
            $data['image'] = $request->file('image')->store('volunteering-activities', 'public');
        }

        $activity->update($data);

        return response()->json([
            'success' => true,
            'data'    => new VolunteeringActivityResource($activity),
        ]);
    }

    public function remove($id)
    {
        $activity = VolunteeringActivity::findOrFail($id);

        if ($activity->image) {
            Storage::disk('public')->delete($activity->image);
        }

        $activity->delete();

        return response()->json(['success' => true, 'message' => 'تم حذف الفعالية التطوعية']);
    }
}
