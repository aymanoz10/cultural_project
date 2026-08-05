<?php

namespace App\Http\Controllers;

use App\Services\AiAssistantService;
use Illuminate\Http\Request;

class AiAssistantController extends Controller
{
    protected AiAssistantService $assistant;

    public function __construct(AiAssistantService $assistant)
    {
        $this->assistant = $assistant;
    }

    public function ask(Request $request)
    {
        $validated = $request->validate([
            'message'           => ['required', 'string', 'max:1000'],
            'history'           => ['sometimes', 'array'],
            'history.*.role'    => ['required_with:history', 'string', 'in:user,assistant'],
            'history.*.content' => ['required_with:history', 'string'],
        ]);

        $result = $this->assistant->ask(
            $validated['message'],
            $validated['history'] ?? []
        );

        // result = ['message' => string, 'plan' => array|null]
        return response()->json($result);
    }
}
