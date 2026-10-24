<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Services\BathroomEstimator;
use App\Services\FxRates;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

/**
 * Banyo hesaplayıcısının logolu PDF özet raporu.
 * Girdiler paylaş linkindeki parametrelerle aynıdır; tutarlar sunucuda yeniden hesaplanır
 * (tarayıcıdan gelen fiyata güvenilmez).
 */
class BathroomReportController extends Controller
{
    /** Paylaş linkindeki parametrelerden hesap girdisi; ölçü yoksa null */
    public static function input(Request $request, array $cfg): ?array
    {
        $num = fn ($k) => (float) str_replace(',', '.', (string) $request->input($k, ''));
        $w = $num('width');
        $l = $num('length');
        // Hesap motoru en/boyu 10 m'ye kırpıyor: PDF'te yazan ölçü ile hesaplanan alan aynı olsun (v119)
        if ($w < 0.5 || $l < 0.5 || $w > 10 || $l > 10) {
            return null;
        }

        return [
            'type' => (string) $request->input('bathType', 'standart'),
            'w' => $w,
            'l' => $l,
            'h' => min(max($num('height') ?: 2.6, 2.0), 4.0),
            'scope' => (string) $request->input('scope', 'komple'),
            'quality' => (string) $request->input('quality', 'orta'),
            'shower' => (string) $request->input('shower', ''),
            'extras' => array_values(array_filter(array_keys($cfg['extras']), fn ($k) => (string) $request->input($k) === '1')),
        ];
    }

    public function pdf(Request $request, BathroomEstimator $estimator, FxRates $fx)
    {
        $cfg = $estimator->config();
        $input = self::input($request, $cfg);
        if (! $input) {
            return redirect('/araclar/banyo-maliyet-hesaplayici')
                ->withErrors(['width' => 'PDF rapor için önce banyonun en ve boy ölçülerini girin.']);
        }
        $extras = $input['extras'];
        $est = $estimator->estimate($input);

        $type = $cfg['types'][$input['type']] ?? $cfg['types']['standart'];
        $logo = public_path('brand/worksitem-logo.png');

        $pdf = Pdf::loadView('reports.banyo-pdf', [
            'est' => $est,
            'input' => $input,
            'cfg' => $cfg,
            'type' => $type,
            'scope' => $cfg['scopes'][$input['scope']] ?? $cfg['scopes']['komple'],
            'quality' => $cfg['qualities'][$input['quality']] ?? $cfg['qualities']['orta'],
            'shower' => $type['shower'] ? ($cfg['showers'][$input['shower']] ?? null) : null,
            'extraLabels' => array_map(fn ($k) => $cfg['extras'][$k]['label'], $extras),
            'fx' => $fx->forReport(),
            'logo' => is_file($logo) ? 'data:image/png;base64,'.base64_encode(file_get_contents($logo)) : null,
            'pricesLabel' => $estimator->pricesLabel(),
            'reportNo' => 'WS-'.now()->format('ymd').'-'.strtoupper(substr(md5(json_encode($input)), 0, 5)),
            'shareUrl' => url('/araclar/banyo-maliyet-hesaplayici').'?'.http_build_query(array_merge($request->query(), ['hesapla' => '1'])),
            'inspectionFee' => config('worksitem_bathroom.inspection_fee', 2500),
        ])->setPaper('a4')->setOption(['isFontSubsettingEnabled' => true, 'isRemoteEnabled' => false]);

        return $pdf->download('worksitem-banyo-maliyet-raporu-'.now()->format('Y-m-d').'.pdf');
    }
}
