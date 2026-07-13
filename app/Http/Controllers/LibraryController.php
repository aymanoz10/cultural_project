<?php

namespace App\Http\Controllers;

use App\Models\Library;
use App\Http\Resources\LibraryResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LibraryController extends Controller
{
    public function index() { return LibraryResource::collection(Library::all()); }

    public function add(Request $request)
    {
        $request->validate(['cultural_center_id' => 'required|exists:cultural_centers,id', 'name' => 'required']);
        $data = $request->only(['cultural_center_id', 'name']);
        if ($request->hasFile('image')) $data['image'] = $request->file('image')->store('libraries', 'public');
        return response()->json(['data' => Library::create($data)], 201);
    }

    public function edit(Request $request, $id)
    {
        $lib = Library::findOrFail($id);
        $data = $request->only(['name']);
        if ($request->hasFile('image')) {
            if ($lib->image) Storage::disk('public')->delete($lib->image);
            $data['image'] = $request->file('image')->store('libraries', 'public');
        }
        $lib->update($data);
        return response()->json(['data' => $lib], 200);
    }

    public function remove($id)
    {
        $lib = Library::findOrFail($id);
        if ($lib->image) Storage::disk('public')->delete($lib->image);
        $lib->delete();
        return response()->json(['success' => true], 200);
    }
    
}