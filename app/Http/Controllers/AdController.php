<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use App\Http\Resources\AdResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdController extends Controller
{
    public function index()
    {
        return AdResource::collection(Ad::latest()->get());
    }

    public function add(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'image'       => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = [
            'title'       => $request->title,
            'description' => $request->description,
            'image'       => $request->file('image')->store('ads', 'public'),
        ];

        $ad = Ad::create($data);

        return response()->json([
            'success' => true,
            'data'    => new AdResource($ad),
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
            'data'    => new AdResource($ad->fresh()),
        ]);
    }

    public function remove($id)
    {
        $ad = Ad::findOrFail($id);

        if ($ad->image) {
            Storage::disk('public')->delete($ad->image);
        }

        $ad->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الإعلان',
        ]);
    }
}
