<?php

namespace App\Http\Controllers\Kit;

use App\Estimators\KitRegistry;
use App\Http\Controllers\Controller;
use App\Services\FxRates;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/** Banyo tarzı hesaplayıcı sayfası + canlı hesap uç noktası */
class KitController extends Controller
{
    public function show(Request $request, FxRates $fx)
    {
        $slug = (string) $request->route('kit');
        $e = KitRegistry::get($slug);
        $in = $e->normalize($request->query());
        $est = $e->estimate($in);

        return view('tools.kit', [
            'e' => $e,
            'page' => $e->page(),
            'steps' => $e->steps(),
            'input' => $in,
            'query' => $request->query(),
            'est' => $est,
            'sponsorLines' => $est['ready'] ? \App\Services\Sponsors\SponsorLines::for($slug, array_keys($est['items']), $in) : [],
            'fx' => $fx->current(),
            'slug' => $slug,
        ]);
    }

    public function calc(Request $request, FxRates $fx): JsonResponse
    {
        $slug = (string) $request->route('kit');
        $e = KitRegistry::get($slug);
        $in = $e->normalize($request->query());
        $est = $e->estimate($in);
        if ($est['ready']) {
            $est['sponsor_lines'] = (object) \App\Services\Sponsors\SponsorLines::for($slug, array_keys($est['items']), $in);
        }

        if ($est['ready'] && ($rates = $fx->current())) {
            $est['fx'] = [];
            foreach ((array) config('worksitem_bathroom.fx.currencies') as $code => $sym) {
                if (! empty($rates['rates'][$code])) {
                    $est['fx'][] = [$sym, round($est['low'] / $rates['rates'][$code], -1), round($est['high'] / $rates['rates'][$code], -1)];
                }
            }
            $est['fx_date'] = $rates['date'] ?? null;
        }

        return response()->json($est)->header('Cache-Control', 'no-store');
    }
}
