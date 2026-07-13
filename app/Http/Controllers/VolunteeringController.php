<?php

namespace App\Http\Controllers;

use App\Events\VolunteeringStatusUpdated;
use App\Events\VolunteeringSubmitted;
use App\Models\Volunteering;
use App\Models\VolunteeringActivity;
use App\Http\Resources\VolunteeringResource;
use Illuminate\Http\Request;

class VolunteeringController extends Controller
{
    public function index(Request $request)
    {
        $query = Volunteering::with(['user', 'volunteeringActivity']);

        if ($request->user()) {
            $query->where('user_id', $request->user()->id);
        }

        if ($request->has('volunteering_activity_id')) {
            $query->where('volunteering_activity_id', $request->volunteering_activity_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        return VolunteeringResource::collection($query->latest()->get());
    }

    public function add(Request $request)
    {
        $request->validate([
            'volunteering_activity_id' => 'required|exists:volunteering_activities,id',
            'form_data'                => 'required|array',
        ]);

        $activity = VolunteeringActivity::findOrFail($request->volunteering_activity_id);

        $exists = Volunteering::where('user_id', $request->user()->id)
            ->where('volunteering_activity_id', $activity->id)
            ->whereIn('status', ['pending', 'accepted'])
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'لقد تقدمت مسبقاً لهذه الفعالية'], 422);
        }

        $volunteering = Volunteering::create([
            'user_id'                  => $request->user()->id,
            'volunteering_activity_id' => $activity->id,
            'form_data'                => $request->form_data,
            'status'                   => 'pending',
        ]);

        VolunteeringSubmitted::dispatch($volunteering->load(['user', 'volunteeringActivity']));

        return response()->json([
            'success' => true,
            'message' => 'تم إرسال طلب التطوع',
            'data'    => new VolunteeringResource($volunteering->load('volunteeringActivity')),
        ], 201);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,accepted,rejected',
        ]);

        $volunteering = Volunteering::findOrFail($id);
        $volunteering->update(['status' => $request->status]);

        VolunteeringStatusUpdated::dispatch($volunteering->fresh(['user', 'volunteeringActivity']));

        return response()->json([
            'success' => true,
            'data'    => new VolunteeringResource($volunteering->fresh()->load('volunteeringActivity')),
        ]);
    }
}
