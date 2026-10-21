<?php

namespace App\Http\Controllers\Tools;

use App\Estimators\SolarEstimator;
use App\Http\Controllers\Controller;
use App\Services\Solar\SolarPlanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** GES planlayıcı: adres → çatı → panel yerleşimi (tarayıcıda) → üretim ve maliyet (sunucuda) */
class SolarPlannerController extends Controller
{
    public function show(): View
    {
        return view('tools.ges-planlayici', [
            'cfg' => (array) config('worksitem_solar'),
            'three' => (array) config('worksitem_planner.three'),
            'kwhPrice' => SolarEstimator::P['kwh_price'],
        ]);
    }

    public function geocode(Request $request, SolarPlanService $s): JsonResponse
    {
        // v88: il / ilçe / mahalle / sokak alanlarıyla adım adım arama
        if ($request->filled('il')) {
            $v = $request->validate([
                'il' => ['required', 'string', \Illuminate\Validation\Rule::in((array) config('turkey.provinces'))],
                'ilce' => ['nullable', 'string', 'max:60'],
                'mahalle' => ['nullable', 'string', 'max:80'],
                'sokak' => ['nullable', 'string', 'max:100'],
                'no' => ['nullable', 'string', 'max:10'],
            ]);
            if (! empty($v['ilce']) && ! in_array($v['ilce'], (array) config('turkey_districts.'.$v['il'], []), true)) {
                return response()->json(['message' => 'İlçe bu ile ait değil.', 'errors' => ['ilce' => ['İlçe bu ile ait değil.']]], 422);
            }

            return response()->json($s->geocodeParts($v['il'], $v['ilce'] ?? '', $v['mahalle'] ?? '', $v['sokak'] ?? '', $v['no'] ?? ''));
        }

        $request->validate(['q' => ['required', 'string', 'min:3', 'max:200']]);

        return response()->json(['results' => $s->geocode((string) $request->query('q'))]);
    }

    public function building(Request $request, SolarPlanService $s): JsonResponse
    {
        $v = $request->validate(['lat' => ['required', 'numeric', 'between:35,43'], 'lon' => ['required', 'numeric', 'between:25,45']]);
        $b = $s->building((float) $v['lat'], (float) $v['lon']);

        return response()->json(['found' => (bool) $b] + ($b ?: []));
    }

    public function calculate(Request $request, SolarPlanService $s): JsonResponse
    {
        $v = $request->validate([
            'lat' => ['required', 'numeric', 'between:35,43'], 'lon' => ['required', 'numeric', 'between:25,45'],
            'use' => ['required', 'in:konut,fabrika,arazi'],
            'site.area' => ['nullable', 'numeric', 'min:0', 'max:5000000'],
            'site.perimeter' => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'flat' => ['nullable', 'boolean'],
            'faces' => ['required', 'array', 'min:1', 'max:12'],
            'faces.*.kwp' => ['required', 'numeric', 'min:0', 'max:5000'],
            'faces.*.angle' => ['required', 'numeric', 'min:0', 'max:70'],
            'faces.*.azimuth' => ['required', 'numeric', 'min:0', 'max:360'],
            'monthly_kwh' => ['nullable', 'numeric', 'min:0', 'max:5000000'],
            'price' => ['nullable', 'numeric', 'min:0.5', 'max:50'],
            'battery' => ['nullable', 'boolean'],
        ]);
        $kwp = array_sum(array_column($v['faces'], 'kwp'));
        if ($kwp <= 0) {
            return response()->json(['ok' => false, 'message' => 'Çatıya panel yerleşmedi; çatı çizimini veya kenar boşluğunu kontrol edin.'], 422);
        }
        $y = $s->yield((float) $v['lat'], (float) $v['lon'], $v['faces'], $v['use'] === 'arazi');
        $price = (float) ($v['price'] ?? SolarEstimator::P['kwh_price']);
        $eco = $s->economics($kwp, $v['use'], (bool) ($v['flat'] ?? false), (float) $y['yearly'], (float) ($v['monthly_kwh'] ?? 0), $price, (bool) ($v['battery'] ?? false), (array) ($v['site'] ?? []));

        return response()->json(['ok' => true, 'kwp' => round($kwp, 2), 'yield' => $y, 'eco' => $eco, 'specific' => round($y['yearly'] / $kwp)])
            ->header('Cache-Control', 'no-store');
    }
}
