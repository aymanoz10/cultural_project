<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Http\Resources\ActivityResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::query();

        if ($request->has('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('center_id')) {
            $query->where('cultural_center_id', $request->center_id);
        }

        return ActivityResource::collection($query->get());
    }

    public function add(Request $request)
    {
        $request->validate([
            'cultural_center_id' => 'required|exists:cultural_centers,id',
            'type'               => ['required', Rule::in(Activity::TYPES)],
            'title'              => 'required|string',
            'description'        => 'nullable|string',
            'start_time'         => 'required|date',
            'end_time'           => 'required|date|after:start_time',
            'capacity'           => 'nullable|integer|min:1',
            'hall_id'            => 'nullable|exists:halls,id',
            'theater_id'         => 'nullable|exists:theaters,id',
            'image'             => 'nullable|image|max:2048',
        ]);

        $data = $request->only([
            'cultural_center_id', 'type', 'hall_id', 'theater_id',
            'title', 'description', 'start_time', 'end_time', 'capacity',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('activities', 'public');
        }

        $activity = Activity::create($data);

        return response()->json(['data' => new ActivityResource($activity)], 201);
    }

    public function edit(Request $request, $id)
    {
        $activity = Activity::findOrFail($id);

        $request->validate([
            'type'        => ['sometimes', 'required', Rule::in(Activity::TYPES)],
            'title'       => 'sometimes|required|string',
            'description' => 'nullable|string',
            'start_time'  => 'sometimes|required|date',
            'end_time'    => 'sometimes|required|date|after:start_time',
            'capacity'    => 'nullable|integer|min:1',
            'image'      => 'nullable|image|max:2048',
        ]);

        $data = $request->except(['image', '_method']);

        if ($request->hasFile('image')) {
            if ($activity->image) {
                Storage::disk('public')->delete($activity->image);
            }
            $data['image'] = $request->file('image')->store('activities', 'public');
        }

        $activity->update($data);

        return response()->json(['data' => new ActivityResource($activity)], 200);
    }

    public function remove($id)
    {
        $activity = Activity::findOrFail($id);

        if ($activity->image) {
            Storage::disk('public')->delete($activity->image);
        }

        $activity->delete();

        return response()->json(['success' => true], 200);
    }
}
