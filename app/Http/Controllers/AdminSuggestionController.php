<?php

namespace App\Http\Controllers;

use App\Models\Suggestion;
use App\Http\Resources\SuggestionResource;
use Illuminate\Http\Request;

class AdminSuggestionController extends Controller
{
    public function index(Request $request)
    {
        $query = Suggestion::with('user');

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        return SuggestionResource::collection($query->latest()->get());
    }
}
