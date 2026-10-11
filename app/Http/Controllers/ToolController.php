<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Lead;
use App\Models\TenderPoz;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\View;

class ToolController extends Controller
{
    public function __construct()
    {
        View::share('pricing', config('pricing'));
    }

    public function index() { return view('tools.index'); }
    public function paintCalculator(
        \App\Services\Pricing\PricingSettingsService $settings,
        \App\Services\ToolPriceService $toolPrices
    ) {
        return view('tools.paint-calculator', [
            'paintSettings' => [
                'labor_standard_m2' =>
                    $settings->requireDecimal('paint_labor_standard_m2'),
                'labor_large_threshold' =>
                    $settings->requireDecimal('paint_labor_large_threshold'),
                'labor_large_min_m2' =>
                    $settings->requireDecimal('paint_labor_large_min_m2'),
                'labor_large_base_m2' =>
                    $settings->requireDecimal('paint_labor_large_base_m2'),
                'labor_large_max_m2' =>
                    $settings->requireDecimal('paint_labor_large_max_m2'),
                'budget_tolerance' =>
                    $settings->requireDecimal('paint_budget_tolerance'),
            ],
            'paintPrices' =>
                $toolPrices->forTool('boya-hesaplayici'),
        ]);
    }
    public function paintVisualizer()
    {
        $brands = \Illuminate\Support\Facades\DB::table('paint_color_brands')
            ->where('is_active', 1)
            ->where('rights_status', '!=', 'unverified')
            ->orderByDesc('is_sponsored')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'slug',
                'is_sponsored',
                'sponsor_label',
                'logo_path',
                'website_url',
                'product_url',
                'dealer_url',
                'catalog_source',
                'catalog_version',
                'rights_status',
            ]);

        $colors = collect();

        if ($brands->isNotEmpty()) {
            $colors = \Illuminate\Support\Facades\DB::table('paint_colors as c')
                ->join('paint_color_brands as b', 'b.id', '=', 'c.brand_id')
                ->where('c.is_active', 1)
                ->where('b.is_active', 1)
                ->where('b.rights_status', '!=', 'unverified')
                ->orderByDesc('b.is_sponsored')
                ->orderBy('b.sort_order')
                ->orderBy('c.sort_order')
                ->orderBy('c.name')
                ->get([
                    'c.id',
                    'c.brand_id',
                    'c.series',
                    'c.code',
                    'c.name',
                    'c.hex',
                    'c.rgb_r',
                    'c.rgb_g',
                    'c.rgb_b',
                    'c.lab_l',
                    'c.lab_a',
                    'c.lab_b',
                    'c.category',
                    'c.product_url',
                    'c.dealer_url',
                    'b.name as brand',
                    'b.slug as brand_slug',
                    'b.is_sponsored',
                    'b.sponsor_label',
                    'b.product_url as brand_product_url',
                    'b.dealer_url as brand_dealer_url',
                ]);
        }

        return view('tools.paint-visualizer', [
            'paintColorBrands' => $brands,
            'paintColorCatalog' => $colors,
        ]);
    }
    public function tileCalculator(
        \App\Services\ToolPriceService $toolPrices
    ) {
        $marketKeys = [
            'tile_ceramic_wall_30x60_m2',
            'tile_ceramic_floor_60x60_m2',
            'tile_porcelain_matt_60x60_m2',
            'tile_porcelain_matt_60x120_m2',
        ];

        $analyzer = app(\App\Services\Pricing\MaterialBenchmarkAnalyzer::class);
        $tileMarket = [];

        foreach ($marketKeys as $key) {
            $rows = \App\Models\MaterialPriceObservation::query()
                ->where('material_key', $key)
                ->where('observed_at', '>=', now()->subDays(7))
                ->orderByDesc('observed_at')
                ->get()
                ->unique('source_name')
                ->values();

            if ($rows->isEmpty()) {
                continue;
            }

            $analysis = $analyzer->analyze($key, 7);

            $tileMarket[$key] = [
                'status' => $analysis['status'] ?? 'unknown',
                'min' => round((float) $rows->min('price'), 2),
                'max' => round((float) $rows->max('price'), 2),
                'sources' => $rows->pluck('source_name')->filter()->unique()->values()->all(),
                'last_checked' => optional($rows->first()->observed_at)
                    ? \Illuminate\Support\Carbon::parse($rows->first()->observed_at)
                        ->timezone('Europe/Istanbul')->format('d.m.Y H:i')
                    : null,
                'benchmark' => $analysis['benchmark']['benchmark_price'] ?? null,
            ];
        }

        $tileSettings = \Illuminate\Support\Facades\DB::table('pricing_settings')
            ->whereIn('key', [
                'tile_labor_standard_m2',
                'tile_large_threshold',
                'tile_large_official_multiplier',
            ])
            ->pluck('value', 'key')
            ->all();

        $tileAccessoryPrices = \Illuminate\Support\Facades\DB::table('unit_prices')
            ->whereIn('key', [
                'tile_adhesive_standard_25kg',
                'tile_grout_5kg',
                'tile_profile_outside_aluminum_8mm_270cm',
                'tile_profile_outside_aluminum_10mm_270cm',
                'tile_profile_outside_aluminum_12mm_270cm',
            ])
            ->where('is_active', 1)
            ->where('review_status', 'approved')
            ->pluck('price', 'key')
            ->all();
        return view('tools.tile-calculator', [
            'tilePrices' => $toolPrices->forTool('seramik-maliyet-hesaplayici'),
            'tileMarket' => $tileMarket,
            'tileSettings' => $tileSettings,
            'tilePackaging' => config('tile_packaging', []),
            'tileAccessoryPrices' => $tileAccessoryPrices,
        ]);
    }
    public function documentBuilder() { return view('tools.document-builder'); }
    public function drywallCalculator() { return view('tools.drywall-calculator'); }
    public function screedCalculator() { return view('tools.screed-calculator'); }
    public function roomPlanner() { return view('tools.room-planner'); }

    public function generateRoomDesign(Request $request)
    {
        if (! config('services.ai_spend_enabled')) {
            return response()->json([
                'success' => false,
                'error' => 'AI oda görselleştirme geçici olarak devre dışı.',
            ], 503);
        }

        $v = $request->validate([
            'room_type' => 'required|string|max:50',
            'style' => 'required|string|max:50',
            'w' => 'required|numeric|min:1|max:100',
            'l' => 'required|numeric|min:1|max:100',
        ]);

        $roomType = strip_tags($v['room_type']);
        $style = strip_tags($v['style']);

        $prompt = "A highly detailed, photorealistic interior design of a {$roomType}, sized {$v['w']} meters by {$v['l']} meters. The interior design style is {$style}. Cinematic lighting, architectural digest quality, 8k resolution, ray tracing, ultra realistic materials.";
        $apiKey = config('services.gemini.api_key');

        if(empty($apiKey)) {
            \Illuminate\Support\Facades\Log::error('generateRoomDesign: GEMINI_API_KEY tanımlı değil.');
            return response()->json(['success' => false, 'error' => 'Görsel servisi şu an kullanılamıyor.'], 503);
        }

        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-3-pro-image:generateContent';

        try {
            $response = Http::withHeaders(['x-goog-api-key' => $apiKey])->connectTimeout(5)->timeout(45)->post($url, [
                'instances' => [['prompt' => $prompt]],
                'parameters' => ['sampleCount' => 1]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $imageBytes = $data['predictions'][0]['bytesBase64Encoded'] ?? null;
                if ($imageBytes) {
                    return response()->json([
                        'success' => true,
                        'image' => 'data:image/jpeg;base64,' . $imageBytes
                    ]);
                }
            }

            // Dış API gövdesi kullanıcıya DÖNÜLMEZ (iç model adı, kota, anahtar bilgisi sızabilir)
            \Illuminate\Support\Facades\Log::warning('generateRoomDesign API hatası', [
                'status' => $response->status(),
                'body' => mb_substr($response->body(), 0, 2000),
            ]);
            return response()->json(['success' => false, 'error' => 'Görsel üretilemedi, lütfen tekrar deneyin.'], 502);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['success' => false, 'error' => 'Görsel servisine bağlanılamadı.'], 502);
        }
    }

    public function concreteCalculator() { return view('tools.concrete-calculator'); }
    public function bathroomCalculator() { return view('tools.bathroom-calculator'); }
    public function rebarCalculator() { $rebarPricing = \Illuminate\Support\Facades\DB::table('tool_price_bindings as b')->join('unit_prices as p', 'p.key', '=', 'b.price_key')->where('b.tool_slug', 'demir-metraji-hesaplayici')->where('b.role', 'material')->where('p.price_pool', 'market')->where('p.is_active', true)->where('p.review_status', 'approved')->select('p.price', 'p.unit', 'p.source_name', 'p.source_date', 'p.review_status', 'b.multiplier')->first(); return view('tools.rebar-calculator', compact('rebarPricing')); }
    public function pvcCalculator() { return view('tools.pvc-calculator'); }
    public function stairCalculator() { return view('tools.stair-calculator'); }

    public function generateDocument(Request $request)
    {
        $data = $request->all();
        return view('tools.pdf-contract', compact('data'));
    }

    public function buildingCostCalculator() { return view('tools.building-cost-calculator'); }
    public function zoningCalculator() { return view('tools.zoning-calculator'); }
    public function mapScaleCalculator() { return view('tools.map-scale-calculator'); }
    public function flatForLandCalculator() { return view('tools.flat-for-land-calculator'); }
    public function gardenWallCalculator() { return view('tools.garden-wall-calculator'); }
    public function isolatorCalculator() { return view('tools.isolator-calculator'); }
    public function salaryCalculator() { return view('tools.salary-calculator'); }
    public function wcCalculator() { return view('tools.wc-calculator'); }
    public function rainpipeCalculator() { return view('tools.rainpipe-calculator'); }
    public function mortgageCalculator() { return view('tools.mortgage-calculator'); }
    public function laborCalculator() { return view('tools.labor-calculator'); }
    public function unitPriceCalculator() { return view('tools.unit-price-calculator'); }
    public function scaffoldingCalculator() { return view('tools.scaffolding-calculator'); }
    public function formworkCalculator() { return view('tools.formwork-calculator'); }
    public function cableCalculator() { return view('tools.cable-calculator'); }
    public function elevatorCalculator() { return view('tools.elevator-calculator'); }
    public function insulationCalculator() { return view('tools.insulation-calculator'); }
    public function excavationCalculator() { return view('tools.excavation-calculator'); }
    public function roofCalculator() { return view('tools.roof-calculator'); }
    public function stairsCalculator() { return view('tools.stairs-calculator'); }
    public function kitchenCalculator() { return view('tools.kitchen-calculator'); }
    public function radiatorCalculator() { return view('tools.radiator-calculator'); }
    public function rebarEquivalentCalculator() { return view('tools.rebar-equivalent-calculator'); }
    public function wallCalculator() { return view('tools.wall-calculator'); }
    public function parquetCalculator() { return view('tools.parquet-calculator'); }
    public function acCalculator() { return view('tools.ac-calculator'); }
    public function solarCalculator() { return view('tools.solar-calculator'); }
    public function generatorCalculator() { return view('tools.generator-calculator'); }
    public function plasterCalculator() { return view('tools.plaster-calculator'); }
    public function cementCalculator() { return view('tools.cement-calculator'); }
    public function volumeCalculator() { return view('tools.volume-calculator'); }
    public function energyCalculator() { return view('tools.energy-calculator'); }
    public function plumbingCalculator() { return view('tools.plumbing-calculator'); }
    public function roughPlasterCalculator() { return view('tools.rough-plaster-calculator'); }

    public function structuralDynamics() { return view('tools.structural-dynamics'); }
    public function columnCapacity() { return view('tools.column-capacity'); }
    public function simpleBeam() { return view('tools.simple-beam'); }
    public function buildingWeight() { return view('tools.building-weight'); }
    public function coreStrength() { return view('tools.core-strength'); }
    public function columnStarterBar() { return view('tools.column-starter-bar'); }
    public function domedTank() { return view('tools.domed-tank'); }
    public function pumpPower() { return view('tools.pump-power'); }
    public function ventilationDuct() { return view('tools.ventilation-duct'); }
    public function shelterRegulation() { return view('tools.shelter-regulation'); }
    public function cuttingOptimization() { return view('tools.cutting-optimization'); }
    public function installmentVsCash() { return view('tools.installment-vs-cash'); }
    public function materialWeight() { return view('tools.material-weight'); }
    public function logisticsCost() { return view('tools.logistics-cost'); }
    public function machineCost() { return view('tools.machine-cost'); }
    public function rentValue() { return view('tools.rent-value'); }
    public function staticProjectFee() { return view('tools.static-project-fee'); }
    public function sgkLabor() { return view('tools.sgk-labor'); }

    public function tenderModule() {
        return view('tools.tender-module');
    }


    public function storeLead(
        Request $request,
        \App\Services\Leads\LeadCaptureService $captureService,
        \App\Services\Leads\SalesLeadNotifier $salesLeadNotifier
    ) {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'phone' => ['required', 'string', 'min:10', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'location' => ['required', 'string', 'max:150'],
            'calculated_service' => ['nullable', 'string', 'max:150'],
            'calculated_area' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:3000'],
            'contact_consent' => ['required', 'accepted'],
            'marketing_consent' => ['nullable', 'boolean'],
            'consent_version' => ['required', 'string', 'max:50'],
            'utm_source' => ['nullable', 'string', 'max:150'],
            'utm_medium' => ['nullable', 'string', 'max:150'],
            'utm_campaign' => ['nullable', 'string', 'max:150'],
            'utm_content' => ['nullable', 'string', 'max:150'],
            'utm_term' => ['nullable', 'string', 'max:150'],
            'landing_page' => ['nullable', 'string', 'max:2000'],
            'referrer' => ['nullable', 'string', 'max:2000'],
        ]);

        $toolName = $validated['calculated_service'] ?? 'Belirtilmedi';

        $notes = "🛠️ NİŞ ARAÇTAN GELDİ: ".mb_strtoupper($toolName)."\n";
        $notes .= "📍 KONUM: ".$validated['location']."\n";
        $notes .= "📏 DEĞER/ALAN: ".($validated['calculated_area'] ?? '-');

        if (! empty($validated['notes'])) {
            $notes .= "\n📝 MÜŞTERİ NOTU: ".$validated['notes'];
        }

        $validated['source'] = 'worksitem_tools_'.Str::slug($toolName);
        $validated['project_type'] = 'Araçlar';
        $validated['notes'] = $notes;

        $result = $captureService->capture(
            input: $validated,
            ip: $request->ip(),
            landingPage: $request->fullUrl(),
            referrer: $request->headers->get('referer')
        );

        $lead = $result['lead'];

        if (! $result['duplicate'] && $lead->sales_ready) {
            try {
                $salesLeadNotifier->notify($lead);
            } catch (\Throwable $exception) {
                report($exception);
            }
        }

        return redirect()
            ->route('lead.thanks')
            ->with('status', $lead->lead_quality)
            ->with('lead_public_id', $lead->public_id)
            ->with('google_ads_conversion', ! $result['duplicate']);
    }

}
