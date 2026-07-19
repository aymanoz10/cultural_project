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
            'host_name'          => 'nullable|string|max:255',
            'description'        => 'nullable|string',
            'start_time'         => 'required|date',
            'end_time'           => 'required|date|after:start_time',
            'capacity'           => 'nullable|integer|min:1',
            'hall_id'            => 'nullable|exists:halls,id',
            'theater_id'         => 'nullable|exists:theaters,id',
            'image'             => 'nullable|image|max:2048',
        ]);

        if ($this->hasConflict($request->start_time, $request->end_time, $request->hall_id, $request->theater_id)) {
            return response()->json(['message' => 'يوجد فعالية في نفس المكان والوقت'], 422);
        }

        $data = $request->only([
            'cultural_center_id', 'type', 'hall_id', 'theater_id',
            'title', 'host_name', 'description', 'start_time', 'end_time', 'capacity',
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
            'host_name'   => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'start_time'  => 'sometimes|required|date',
            'end_time'    => 'sometimes|required|date|after:start_time',
            'capacity'    => 'nullable|integer|min:1',
            'image'      => 'nullable|image|max:2048',
        ]);

        $startTime = $request->start_time ?? $activity->start_time;
        $endTime = $request->end_time ?? $activity->end_time;
        $hallId = $request->hall_id ?? $activity->hall_id;
        $theaterId = $request->theater_id ?? $activity->theater_id;

        if ($this->hasConflict($startTime, $endTime, $hallId, $theaterId, $activity->id)) {
            return response()->json(['message' => 'يوجد فعالية في نفس المكان والوقت'], 422);
        }

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

    public function finished()
    {
        $activities = Activity::where('end_time', '<', now())->latest('end_time')->get();

        return ActivityResource::collection($activities);
    }

    public function coming()
    {
        $activities = Activity::where('start_time', '>', now())->orderBy('start_time', 'asc')->get();

        return ActivityResource::collection($activities);
    }

    private function hasConflict(string $startTime, string $endTime, ?int $hallId, ?int $theaterId, ?int $excludeId = null): bool
    {
        $query = Activity::where('start_time', '<', $endTime)
            ->where('end_time', '>', $startTime);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        if ($hallId) {
            $conflict = (clone $query)->where('hall_id', $hallId)->exists();
            if ($conflict) return true;
        }

        if ($theaterId) {
            $conflict = (clone $query)->where('theater_id', $theaterId)->exists();
            if ($conflict) return true;
        }

        return false;
    }
}
