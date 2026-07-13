<?php

namespace App\Http\Controllers;

use App\Events\SuggestionSubmitted;
use App\Models\Suggestion;
use App\Http\Resources\SuggestionResource;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SuggestionController extends Controller
{
    public function index(Request $request)
    {
        $query = Suggestion::with('user');

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->user()) {
            $query->where('user_id', $request->user()->id);
        }

        return SuggestionResource::collection($query->latest()->get());
    }

    public function add(Request $request)
    {
        $request->validate([
            'type'    => ['required', Rule::in(Suggestion::TYPES)],
            'content' => 'required|string|max:5000',
        ]);

        $suggestion = Suggestion::create([
            'user_id' => $request->user()->id,
            'type'    => $request->type,
            'content' => $request->content,
        ]);

        SuggestionSubmitted::dispatch($suggestion->load('user'));

        return response()->json([
            'success' => true,
            'data'    => new SuggestionResource($suggestion->load('user')),
        ], 201);
    }

    public function edit(Request $request, $id)
    {
        $suggestion = Suggestion::where('user_id', $request->user()->id)->findOrFail($id);

        $request->validate([
            'type'    => ['sometimes', 'required', Rule::in(Suggestion::TYPES)],
            'content' => 'sometimes|required|string|max:5000',
        ]);

        $suggestion->update($request->only(['type', 'content']));

        return response()->json([
            'success' => true,
            'data'    => new SuggestionResource($suggestion->fresh()->load('user')),
        ]);
    }

    public function remove(Request $request, $id)
    {
        $suggestion = Suggestion::where('user_id', $request->user()->id)->findOrFail($id);
        $suggestion->delete();

        return response()->json(['success' => true, 'message' => 'تم حذف الاقتراح']);
    }
}
