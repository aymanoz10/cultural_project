<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\CulturalCenter;
use App\Models\Hall;
use App\Models\Theater;
use App\Http\Resources\ActivityResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ActivityController extends Controller
{
    // عرض قائمة الفعاليات (يدعم Web + API)
   public function index(Request $request)
{
    $query = Activity::with(['culturalCenter', 'hall', 'theater']);

    if ($request->filled('search')) {
        $query->where('title', 'like', "%{$request->search}%");
    }

    if ($request->filled('center_id')) {
        $query->where('cultural_center_id', $request->center_id);
    }

    $activities = $query->latest('start_time')->paginate(10);
    
    // جلب المراكز لفلترة القائمة
    $centers = \App\Models\CulturalCenter::all();

    // إرسال $centers إلى الـ View
    return view('admin.events.index', [
        'events'  => $activities,
        'centers' => $centers,
    ]);
}
    // عرض صفحة إضافة فعالية (للـ Web)
    public function create()
    {
        $centers  = CulturalCenter::all();
        $halls    = Hall::all();
        $theaters = Theater::all();

        return view('admin.events.create', compact('centers', 'halls', 'theaters'));
    }

    // إضافة فعالية جديدة
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
            'image'              => 'nullable|image|max:2048',
        ]);

        if ($this->hasConflict($request->start_time, $request->end_time, $request->hall_id, $request->theater_id)) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'يوجد فعالية في نفس المكان والوقت'], 422);
            }
            return back()->withInput()->withErrors(['start_time' => 'يوجد تعارض مع فعالية أخرى في نفس المكان والوقت']);
        }

        $data = $request->only([
            'cultural_center_id', 'type', 'hall_id', 'theater_id',
            'title', 'host_name', 'description', 'start_time', 'end_time', 'capacity',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('activities', 'public');
        }

        $activity = Activity::create($data);

        if ($request->wantsJson()) {
            return response()->json(['data' => new ActivityResource($activity)], 201);
        }

        return redirect()->route('admin.events.index')->with('success', 'تمت إضافة الفعالية بنجاح');
    }

    // عرض التفاصيل (للـ Web)
    public function show(Request $request, $id)
    {
        $activity = Activity::with(['culturalCenter', 'hall', 'theater'])->findOrFail($id);

        if ($request->wantsJson()) {
            return new ActivityResource($activity);
        }

        return view('admin.events.show', compact('activity'));
    }

    // عرض صفحة التعديل (للـ Web)
    public function editView($id)
    {
        $activity = Activity::findOrFail($id);
        $centers  = CulturalCenter::all();
        $halls    = Hall::all();
        $theaters = Theater::all();

        return view('admin.events.edit', compact('activity', 'centers', 'halls', 'theaters'));
    }

    // تعديل الفعالية
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
            'image'       => 'nullable|image|max:2048',
        ]);

        $startTime = $request->start_time ?? $activity->start_time;
        $endTime   = $request->end_time ?? $activity->end_time;
        $hallId    = $request->hall_id ?? $activity->hall_id;
        $theaterId = $request->theater_id ?? $activity->theater_id;

        if ($this->hasConflict($startTime, $endTime, $hallId, $theaterId, $activity->id)) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'يوجد فعالية في نفس المكان والوقت'], 422);
            }
            return back()->withInput()->withErrors(['start_time' => 'يوجد تعارض مع فعالية أخرى في نفس المكان والوقت']);
        }

        $data = $request->except(['image', '_method', '_token']);

        if ($request->hasFile('image')) {
            if ($activity->image) {
                Storage::disk('public')->delete($activity->image);
            }
            $data['image'] = $request->file('image')->store('activities', 'public');
        }

        $activity->update($data);

        if ($request->wantsJson()) {
            return response()->json(['data' => new ActivityResource($activity)], 200);
        }

        return redirect()->route('admin.events.index')->with('success', 'تم تعديل بيانات الفعالية بنجاح');
    }

    // حذف فعالية
    public function remove(Request $request, $id)
    {
        $activity = Activity::findOrFail($id);

        if ($activity->image) {
            Storage::disk('public')->delete($activity->image);
        }

        $activity->delete();

        if ($request->wantsJson()) {
            return response()->json(['success' => true], 200);
        }

        return redirect()->route('admin.events.index')->with('success', 'تم حذف الفعالية بنجاح');
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