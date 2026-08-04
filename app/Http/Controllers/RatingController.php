<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Http\Resources\RatingResource;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RatingController extends Controller
{
    private const RATEABLE_TYPES = [
        'activity' => \App\Models\Activity::class,
        'venue'    => \App\Models\Venue::class,
        'center'   => \App\Models\CulturalCenter::class,
    ];

    public function index(Request $request)
    {
        $query = Rating::with('user');

        if ($request->has('rateable_type') && $request->has('rateable_id')) {
            $type = self::RATEABLE_TYPES[$request->rateable_type] ?? null;
            if ($type) {
                $query->where('rateable_type', $type)
                    ->where('rateable_id', $request->rateable_id);
            }
        }

        return RatingResource::collection($query->latest()->get());
    }

    public function add(Request $request)
    {
        $request->validate([
            'rateable_type' => ['required', Rule::in(array_keys(self::RATEABLE_TYPES))],
            'rateable_id'   => 'required|integer',
            'value'         => 'required|integer|min:1|max:5',
            'comment'       => 'nullable|string|max:1000',
        ]);

        $modelClass = self::RATEABLE_TYPES[$request->rateable_type];
        $modelClass::findOrFail($request->rateable_id);

        $existing = Rating::where('user_id', $request->user()->id)
            ->where('rateable_type', $modelClass)
            ->where('rateable_id', $request->rateable_id)
            ->exists();

        if ($existing) {
            return response()->json(['message' => 'لقد قيّمت هذا العنصر مسبقاً'], 422);
        }

        $rating = Rating::create([
            'user_id'       => $request->user()->id,
            'value'         => $request->value,
            'comment'       => $request->comment,
            'rateable_type' => $modelClass,
            'rateable_id'   => $request->rateable_id,
        ]);

        return response()->json([
            'success' => true,
            'data'    => new RatingResource($rating->load('user')),
        ], 201);
    }

    public function edit(Request $request, $id)
    {
        $rating = Rating::where('user_id', $request->user()->id)->findOrFail($id);

        $request->validate([
            'value'   => 'sometimes|required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $rating->update($request->only(['value', 'comment']));

        return response()->json([
            'success' => true,
            'data'    => new RatingResource($rating->fresh()->load('user')),
        ]);
    }

    public function remove(Request $request, $id)
    {
        $rating = Rating::where('user_id', $request->user()->id)->findOrFail($id);
        $rating->delete();

        return response()->json(['success' => true, 'message' => 'تم حذف التقييم']);
    }
}
