<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ToolEventController extends Controller
{
    private const EVENTS = [
        'tool_view',
        'calculator_start',
        'calculator_complete',
        'cost_cta_click',
        'kainta_cta_click',
        'sponsor_click',
    ];

    public function store(Request $request)
    {
        $data = $request->validate([
            'tool_slug'   => ['required', 'string', 'max:120'],
            'event_type'  => ['required', Rule::in(self::EVENTS)],
            'session_key' => ['nullable', 'string', 'max:64'],
            'sponsor_key' => ['nullable', 'string', 'max:120'],
            'target_url'  => ['nullable', 'string', 'max:2048'],
            'metadata'    => ['nullable', 'array'],
        ]);

        DB::table('tool_events')->insert([
            'tool_slug'   => $data['tool_slug'],
            'event_type'  => $data['event_type'],
            'session_key' => $data['session_key'] ?? null,
            'sponsor_key' => $data['sponsor_key'] ?? null,
            'target_url'  => $data['target_url'] ?? null,
            'metadata'    => isset($data['metadata'])
                ? json_encode($data['metadata'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                : null,
            'created_at'  => now(),
        ]);

        return response()->json(['ok' => true], 201);
    }
}
