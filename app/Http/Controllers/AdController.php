<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use App\Models\Activity;
use App\Models\VolunteeringActivity;
use App\Http\Resources\AdResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AdController extends Controller
{
    private const ADVERTABLE_TYPES = [
        'volunteering_activity' => VolunteeringActivity::class,
        'activity'              => Activity::class,
    ];

    public function index(Request $request)
    {
        $query = Ad::with('advertable');

        if ($request->has('advertable_type') && $request->has('advertable_id')) {
            $type = self::ADVERTABLE_TYPES[$request->advertable_type] ?? null;
            if ($type) {
                $query->where('advertable_type', $type)
                    ->where('advertable_id', $request->advertable_id);
            }
        }

        return AdResource::collection($query->latest()->get());
    }

    public function add(Request $request)
    {
        $request->validate([
            'title'           => 'required|string|max:255',
            'description'     => 'nullable|string',
            'advertable_type' => ['required', Rule::in(array_keys(self::ADVERTABLE_TYPES))],
            'advertable_id'   => 'required|integer',
            'image'           => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $modelClass = self::ADVERTABLE_TYPES[$request->advertable_type];
        $modelClass::findOrFail($request->advertable_id);

        $data = [
            'title'           => $request->title,
            'description'     => $request->description,
            'advertable_type' => $modelClass,
            'advertable_id'   => $request->advertable_id,
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('ads', 'public');
        }

        $ad = Ad::create($data);

        return response()->json([
            'success' => true,
            'data'    => new AdResource($ad->load('advertable')),
        ], 201);
    }

    public function edit(Request $request, $id)
    {
        $ad = Ad::findOrFail($id);

        $request->validate([
            'title'       => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->only(['title', 'description']);

        if ($request->hasFile('image')) {
            if ($ad->image) {
                Storage::disk('public')->delete($ad->image);
            }
            $data['image'] = $request->file('image')->store('ads', 'public');
        }

        $ad->update($data);

        return response()->json([
            'success' => true,
            'data'    => new AdResource($ad->fresh()->load('advertable')),
        ]);
    }

    public function remove($id)
    {
        $ad = Ad::findOrFail($id);

        if ($ad->image) {
            Storage::disk('public')->delete($ad->image);
        }

        $ad->delete();

        return response()->json(['success' => true, 'message' => 'تم حذف الإعلان']);
    }
}
