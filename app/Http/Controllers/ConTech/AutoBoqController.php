<?php

namespace App\Http\Controllers\ConTech;

use App\Http\Controllers\Controller;
use App\Services\ConTech\AutoBoqService;
use App\Services\Leads\PricingEngine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AutoBoqController extends Controller
{
    public function __construct(
        private readonly AutoBoqService $aiService,
        private readonly PricingEngine $pricingEngine
    ) {}

    public function index()
    {
        return view('tools.auto-boq');
    }

    /**
     * AI dosya/metin analizi + Zero-Trust calculation_id üretimi
     */
    public function calculate(Request $request)
    {
        $request->validate([
            'project_file' => 'nullable|file|mimes:pdf,csv,xlsx,xls,jpg,jpeg,png|max:10240',
            'text_input' => 'nullable|string|max:50000',
            'project_type' => 'nullable|string|max:100',
            'tip' => 'nullable|string|max:50',
            'quality' => 'nullable|string|max:30',
        ]);

        if (!$request->hasFile('project_file') && !$request->filled('text_input')) {
            return response()->json([
                'success' => false,
                'message' => 'Lütfen bir dosya yükleyin veya metin girin.',
            ], 422);
        }

        try {
            // 1) AI analiz — dosya VEYA text
            if ($request->hasFile('project_file')) {
                $result = $this->aiService->analyse(
                    $request->file('project_file'),
                    $request->input('project_type', 'genel'),
                    $request->input('quality', 'standart')
                );
            } else {
                $result = $this->aiService->analyseText(
                    (string) $request->input('text_input'),
                    $request->input('project_type', 'genel'),
                    $request->input('quality', 'standart')
                );
            }

            // 2) Zero-Trust: calculation_id + cache
            $calculationId = (string) Str::uuid();
            $sessionHash = hash_hmac('sha256', session()->getId(), config('app.key'));
            $cacheKey = 'calc_' . $calculationId . '_' . $sessionHash;

            $pricingSnapshot = $this->pricingEngine->getPricingSnapshot();

            Cache::put($cacheKey, [
                'raw_total' => $result['raw_total'] ?? null,
                'items' => $result['items'] ?? [],
                'missing_price_count' => $result['missing_price_count'] ?? 0,
                'quality' => mb_strtolower((string) $request->input('quality', 'standart')),
                'tip' => mb_strtolower((string) $request->input('tip', 'insaat')),
                'project_type' => $request->input('project_type', 'genel'),
                'price_version' => $pricingSnapshot['version'],
                'created_at' => now()->toIso8601String(),
            ], now()->addHours(2));

            // 3) Response
            $result['calculation_id'] = $calculationId;
            $result['price_version'] = $pricingSnapshot['version'];
            $result['success'] = $result['success'] ?? true;

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('AutoBOQ Hatası: ' . $e->getMessage(), [
                'file' => $request->file('project_file')?->getClientOriginalName(),
                'tip' => $request->input('tip'),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Analiz sırasında bir hata oluştu: ' . $e->getMessage(),
            ], 422);
        }
    }
}
