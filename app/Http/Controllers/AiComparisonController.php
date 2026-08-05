<?php

namespace App\Http\Controllers;

use App\Services\AiComparisonService;
use Illuminate\Http\Request;

class AiComparisonController extends Controller
{
    protected AiComparisonService $comparison;

    public function __construct(AiComparisonService $comparison)
    {
        $this->comparison = $comparison;
    }

    public function compare(Request $request)
    {
        $validated = $request->validate([
            'type'    => ['required', 'string', 'in:activity,center'],
            'ids'     => ['required', 'array', 'size:2'],
            'ids.*'   => ['required', 'integer', 'distinct'],
        ]);

        $result = $validated['type'] === 'activity'
            ? $this->comparison->compareActivities($validated['ids'][0], $validated['ids'][1])
            : $this->comparison->compareCenters($validated['ids'][0], $validated['ids'][1]);

        return response()->json($result);
    }
}
