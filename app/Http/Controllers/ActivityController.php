<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\CulturalCenter;
use App\Models\Venue;
use App\Http\Resources\ActivityResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::with(['culturalCenter', 'venue', 'activityType']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%")
                  ->orWhere('presenter_name', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('center_id')) {
            $query->where('cultural_center_id', $request->center_id);
        }

        if ($request->filled('activity_type_id')) {
            $query->where('activity_type_id', $request->activity_type_id);
        }

        if ($request->filled('id')) {
            $query->where('id', $request->id);
        }

        $perPage = max(1, min($request->integer('per_page', 10), 100));
        $activities = $query->latest('start_time')->paginate($perPage);

        if ($request->wantsJson()) {
            return ActivityResource::collection($activities);
        }

        $centers = CulturalCenter::all();

        return view('admin.events.index', [
            'events'  => $activities,
            'centers' => $centers,
        ]);
    }

    public function create()
    {
        $centers       = CulturalCenter::all();
        $venues        = Venue::all();
        $activityTypes = ActivityType::all();

        return view('admin.events.create', compact('centers', 'venues', 'activityTypes'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'cultural_center_id' => 'required|exists:cultural_centers,id',
            'activity_type_id'   => 'required|exists:activity_types,id',
            'title'              => 'required|string',
            'presenter_name'     => 'nullable|string|max:255',
            'presenter_avatar'   => 'nullable|image|max:2048',
            'description'        => 'nullable|string',
            'ticket_price'       => 'nullable|numeric|min:0',
            'capacity'           => 'nullable|integer|min:1',
            'start_time'         => 'required|date',
            'end_time'           => 'required|date|after:start_time',
            'venue_id'           => 'nullable|exists:venues,id',
            'image'              => 'nullable|image|max:2048',
        ]);

        if ($this->hasConflict($request->start_time, $request->end_time, $request->venue_id)) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'يوجد فعالية في نفس المكان والوقت'], 422);
            }
            return back()->withInput()->withErrors(['start_time' => 'يوجد تعارض مع فعالية أخرى في نفس المكان والوقت']);
        }

        $data = $request->only([
            'cultural_center_id', 'activity_type_id', 'venue_id',
            'title', 'presenter_name', 'description', 'ticket_price', 'capacity', 'start_time', 'end_time',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('activities', 'public');
        }

        if ($request->hasFile('presenter_avatar')) {
            $data['presenter_avatar'] = $request->file('presenter_avatar')->store('activities/presenters', 'public');
        }

        $activity = Activity::create($data);

        if ($request->wantsJson()) {
            return response()->json(['data' => new ActivityResource($activity)], 201);
        }

        return redirect()->route('admin.events.index')->with('success', 'تمت إضافة الفعالية بنجاح');
    }

    public function show(Request $request, $id)
    {
        $activity = Activity::with(['culturalCenter', 'venue', 'activityType'])->findOrFail($id);

        if ($request->wantsJson()) {
            return new ActivityResource($activity);
        }

        return view('admin.events.show', compact('activity'));
    }

    public function editView($id)
    {
        $activity     = Activity::findOrFail($id);
        $centers      = CulturalCenter::all();
        $venues       = Venue::all();
        $activityTypes = ActivityType::all();

        return view('admin.events.edit', compact('activity', 'centers', 'venues', 'activityTypes'));
    }

    public function edit(Request $request, $id)
    {
        $activity = Activity::findOrFail($id);

        $request->validate([
            'activity_type_id'   => ['sometimes', 'required', 'exists:activity_types,id'],
            'title'              => 'sometimes|required|string',
            'presenter_name'   => 'nullable|string|max:255',
            'presenter_avatar' => 'nullable|image|max:2048',
            'description'      => 'nullable|string',
            'ticket_price'     => 'nullable|numeric|min:0',
            'capacity'         => 'nullable|integer|min:1',
            'start_time'       => 'sometimes|required|date',
            'end_time'         => 'sometimes|required|date|after:start_time',
            'venue_id'         => 'nullable|exists:venues,id',
            'image'            => 'nullable|image|max:2048',
        ]);

        $startTime = $request->start_time ?? $activity->start_time;
        $endTime   = $request->end_time ?? $activity->end_time;
        $venueId   = $request->venue_id ?? $activity->venue_id;

        if ($this->hasConflict($startTime, $endTime, $venueId, $activity->id)) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'يوجد فعالية في نفس المكان والوقت'], 422);
            }
            return back()->withInput()->withErrors(['start_time' => 'يوجد تعارض مع فعالية أخرى في نفس المكان والوقت']);
        }

        $data = $request->except(['image', 'presenter_avatar', '_method', '_token']);

        if ($request->hasFile('image')) {
            if ($activity->image) {
                Storage::disk('public')->delete($activity->image);
            }
            $data['image'] = $request->file('image')->store('activities', 'public');
        }

        if ($request->hasFile('presenter_avatar')) {
            if ($activity->presenter_avatar) {
                Storage::disk('public')->delete($activity->presenter_avatar);
            }
            $data['presenter_avatar'] = $request->file('presenter_avatar')->store('activities/presenters', 'public');
        }

        $activity->update($data);

        if ($request->wantsJson()) {
            return response()->json(['data' => new ActivityResource($activity)], 200);
        }

        return redirect()->route('admin.events.index')->with('success', 'تم تعديل بيانات الفعالية بنجاح');
    }

    public function remove(Request $request, $id)
    {
        $activity = Activity::findOrFail($id);

        if ($activity->image) {
            Storage::disk('public')->delete($activity->image);
        }

        if ($activity->presenter_avatar) {
            Storage::disk('public')->delete($activity->presenter_avatar);
        }

        $activity->delete();

        if ($request->wantsJson()) {
            return response()->json(['success' => true], 200);
        }

        return redirect()->route('admin.events.index')->with('success', 'تم حذف الفعالية بنجاح');
    }

    public function finished(Request $request)
    {
        $perPage = max(1, min($request->integer('per_page', 10), 100));
        $activities = Activity::where('end_time', '<', now())
            ->latest('end_time')
            ->paginate($perPage);

        return ActivityResource::collection($activities);
    }

    public function coming(Request $request)
    {
        $perPage = max(1, min($request->integer('per_page', 10), 100));
        $activities = Activity::where('start_time', '>', now())
            ->orderBy('start_time', 'asc')
            ->paginate($perPage);

        return ActivityResource::collection($activities);
    }

    private function hasConflict(string $startTime, string $endTime, ?int $venueId, ?int $excludeId = null): bool
    {
        if (! $venueId) {
            return false;
        }

        $query = Activity::where('venue_id', $venueId)
            ->where('start_time', '<', $endTime)
            ->where('end_time', '>', $startTime);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }
}
