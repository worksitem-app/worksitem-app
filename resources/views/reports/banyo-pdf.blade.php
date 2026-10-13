@php
    $tl = fn ($v) => '₺'.number_format($v, 0, ',', '.');
    $n1 = fn ($v) => number_format($v, 1, ',', '.');
    $labels = [
        'sokum' => 'Söküm ve moloz',
        'tesisat' => 'Su ve gider tesisatı',
        'yalitim' => 'Su yalıtımı',
        'seramik' => 'Seramik (zemin + duvar)',
        'vitrifiye' => 'Vitrifiye ve armatürler',
        'dus' => $shower['label'] ?? 'Duş',
        'ekstra' => 'Seçilen ekstralar',
        'beklenmedik' => 'Beklenmedik gider payı (%'.round($cfg['contingency_rate'] * 100).')',
    ];
    $colors = ['sokum' => '#5B6472', 'tesisat' => '#2F6F8F', 'yalitim' => '#7FB3C8', 'seramik' => '#FFB800', 'vitrifiye' => '#101318', 'dus' => '#8A6200', 'ekstra' => '#C9A55A', 'beklenmedik' => '#D5D9DF'];
    $total = array_sum(array_column($est['items'], 'mid')) ?: 1;
@endphp
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<title>Banyo Tadilat Maliyet Raporu</title>
<style>
    @page { margin: 22px 32px 44px; }
    * { font-family: 'DejaVu Sans', sans-serif; }
    body { color: #101318; font-size: 10.2px; line-height: 1.4; }
    .muted { color: #5B6472; }
    table { width: 100%; border-collapse: collapse; }
    .head td { vertical-align: middle; }
    .head .r { text-align: right; }
    .doc-title { font-size: 17px; font-weight: bold; }
    .hero { margin-top: 8px; background: #101318; color: #fff; border-radius: 10px; padding: 12px 16px; }
    .hero .lbl { color: #C9CED6; font-size: 10px; }
    .hero .range { color: #FFB800; font-size: 26px; font-weight: bold; margin: 3px 0 4px; }
    .hero .typ { color: #DFE3E8; }
    .hero .fx { color: #AAB1BC; font-size: 9px; margin-top: 6px; }
    h2 { font-size: 12px; margin: 12px 0 4px; }
    .grid td { padding: 3px 8px; border-bottom: 1px solid #E3E6EA; }
    .grid td.k { color: #5B6472; width: 38%; }
    .bar { height: 9px; border-radius: 5px; overflow: hidden; margin: 4px 0 8px; }
    .bar td { height: 9px; padding: 0; }
    .lines td { padding: 3px 8px; border-bottom: 1px dashed #E3E6EA; }
    .lines td.v { text-align: right; font-weight: bold; white-space: nowrap; }
    .dot { display: inline-block; width: 8px; height: 8px; border-radius: 2px; margin-right: 6px; }
    .lines tr.total td { border-bottom: 0; border-top: 2px solid #101318; font-size: 12px; font-weight: bold; }
    .meta td { width: 33%; background: #F5F6F8; padding: 6px 10px; border: 3px solid #fff; }
    .meta b { display: block; font-size: 12px; margin-top: 2px; }
    .warn { margin-top: 10px; border-left: 4px solid #FFB800; background: #FFF8E1; padding: 10px 12px; border-radius: 6px; }
    .warn strong { display: block; margin-bottom: 3px; }
    .cta { margin-top: 10px; border: 1.5px solid #101318; border-radius: 8px; padding: 10px 12px; }
    .cta .fee { font-size: 13px; font-weight: bold; }
    .foot { position: fixed; bottom: -24px; left: 0; right: 0; font-size: 8.5px; color: #8b93a0; border-top: 1px solid #E3E6EA; padding-top: 5px; }
</style>
</head>
<body>

<table class="head">
    <tr>
        <td>@if ($logo)<img src="{{ $logo }}" style="height:34px">@else<b style="font-size:18px">worksitem</b>@endif</td>
        <td class="r">
            <div class="doc-title">Banyo Tadilat Maliyet Raporu</div>
            <div class="muted">Rapor no: {{ $reportNo }} · {{ now()->locale('tr')->translatedFormat('d F Y') }}</div>
        </td>
    </tr>
</table>

<div class="hero">
    <div class="lbl">Tahmini maliyet aralığı (malzeme + işçilik, KDV dahil)</div>
    <div class="range">{{ $tl($est['low']) }} – {{ $tl($est['high']) }}</div>
    <div class="typ">En olası tutar {{ $tl($est['mid']) }} · zemin m² başına yaklaşık {{ $tl($est['per_m2']) }}</div>
    @if ($fx)
        <div class="fx">
            @foreach ($cfg['fx']['currencies'] as $code => $sym)
                @if (! empty($fx['rates'][$code]))
                    ≈ {{ $sym }}{{ number_format(round($est['low'] / $fx['rates'][$code], -1), 0, ',', '.') }} – {{ $sym }}{{ number_format(round($est['high'] / $fx['rates'][$code], -1), 0, ',', '.') }}&nbsp;&nbsp;
                @endif
            @endforeach
            (TCMB döviz satış kuru, {{ $fx['date'] }})
        </div>
    @endif
</div>

<h2>Proje bilgileri</h2>
<table class="grid">
    <tr><td class="k">Banyo tipi</td><td>{{ $type['label'] }}</td></tr>
    <tr><td class="k">Ölçüler (en × boy × tavan)</td><td>{{ $n1($input['w']) }} × {{ $n1($input['l']) }} × {{ $n1($input['h']) }} m</td></tr>
    <tr><td class="k">Zemin alanı / kaplanacak alan</td><td>{{ $n1($est['floor']) }} m² / {{ $n1($est['tile_area']) }} m² (zemin + duvar, kapı payı düşülmüş)</td></tr>
    <tr><td class="k">Kapsam</td><td>{{ $scope['label'] }} — {{ $scope['hint'] }}</td></tr>
    <tr><td class="k">Malzeme kalitesi</td><td>{{ $quality['label'] }}</td></tr>
    @if ($shower)<tr><td class="k">Duş tercihi</td><td>{{ $shower['label'] }}</td></tr>@endif
    <tr><td class="k">Ekstralar</td><td>{{ $extraLabels ? implode(', ', $extraLabels) : 'Seçilmedi' }}</td></tr>
</table>

<h2>Kalem kalem maliyet</h2>
<table class="bar"><tr>
    @foreach ($est['items'] as $k => $it)
        @if ($it['mid'] > 0)<td style="width:{{ round($it['mid'] / $total * 100, 2) }}%;background:{{ $colors[$k] }}"></td>@endif
    @endforeach
</tr></table>
<table class="lines">
    @foreach ($est['items'] as $k => $it)
        @if ($it['mid'] > 0)
            <tr><td><span class="dot" style="background:{{ $colors[$k] }}"></span>{{ $labels[$k] }}</td><td class="v">{{ $tl($it['low']) }} – {{ $tl($it['high']) }}</td></tr>
        @endif
    @endforeach
    <tr class="total"><td>Toplam (yuvarlanmış)</td><td class="v">{{ $tl($est['low']) }} – {{ $tl($est['high']) }}</td></tr>
</table>

<table class="meta" style="margin-top:10px"><tr>
    <td>Tahmini süre<b>{{ $est['days'][0] }}–{{ $est['days'][1] }} iş günü</b></td>
    <td>Fiyat tarihi<b>{{ $pricesLabel }}</b></td>
    <td>Aralık bandı<b>−%15 / +%20</b></td>
</tr></table>

<div class="warn">
    <strong>Bu rapor yaklaşık bir hesaplamadır.</strong>
    Worksitem'in otomatik hesaplama aracıyla, girdiğiniz bilgilere göre hazırlanmıştır. Teklif değildir ve geçerlilik süresi yoktur. Otomatik hesaplama ve yapay zekâ araçları hata yapabilir; fiyatlar günlük olarak ve ülkeye, şehre, markaya, ustaya göre değişir. Girdiğiniz bilgilerin doğruluğu ve bu rapora dayanarak vereceğiniz kararlar sizin sorumluluğunuzdadır. Bu rapor yerinde keşif, kesin metraj ve yazılı teklif yerine geçmez.
</div>

<div class="cta">
    <div class="fee">Kesin fiyat için yerinde keşif isteyin — keşif bedeli {{ \App\Support\InspectionOffer::feeLabel() }}</div>
    <div class="muted"><x-inspection-fee-note /></div>
    <div class="muted" style="margin-top:3px">Keşif talebi: worksitem.com/araclar/banyo-maliyet-hesaplayici · WhatsApp / Çağrı Merkezi: {{ config('worksitem_inspection.phone') }} · info@worksitem.com</div>
</div>

<div class="foot">
    worksitem.com · Kainta İnşaat · Bu rapor {{ now()->format('d.m.Y H:i') }} tarihinde oluşturuldu. Aynı hesabı açmak için: {{ \Illuminate\Support\Str::limit($shareUrl, 120) }}
</div>
</body>
</html>
