<?php

namespace App\Http\Controllers\ConTech;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ConTech\AutoBoqService;
use Illuminate\Support\Facades\Log;

class AutoBoqController extends Controller
{
    protected $aiService;

    public function __construct(AutoBoqService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function index()
    {
        return view('tools.auto-boq');
    }

    public function calculate(Request $request)
    {
        $request->validate([
            'project_file' => 'required|file|mimes:pdf,csv,jpg,jpeg,png|max:10240',
            'project_type' => 'nullable|string',
            'quality'      => 'nullable|string'
        ]);

        try {
            // Gerçek Analiz Motorunu Çağır
            $result = $this->aiService->analyse(
                $request->file('project_file'),
                $request->input('project_type', 'genel'),
                $request->input('quality', 'standard')
            );

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('AutoBOQ Hatası: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
