@php
    $tl = fn ($v) => '₺'.number_format($v, 0, ',', '.');
    $n1 = fn ($v) => number_format($v, 1, ',', '.');
    $sec = 0;
    $currencies = (array) config('worksitem_bathroom.fx.currencies');
@endphp
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<title>{{ $page['title'] }} Pro Rapor</title>
<style>
    @page { margin: 22px 32px 46px; }
    * { font-family: 'DejaVu Sans', sans-serif; }
    body { color: #101318; font-size: 10px; line-height: 1.42; }
    .muted { color: #5B6472; }
    table { width: 100%; border-collapse: collapse; }
    .head td { vertical-align: middle; }
    .head .r { text-align: right; }
    .doc-title { font-size: 17px; font-weight: bold; }
    .pro { display: inline-block; background: #FFB800; color: #101318; font-weight: bold; font-size: 9px; padding: 2px 7px; border-radius: 8px; }
    .hero { margin-top: 8px; background: #101318; color: #fff; border-radius: 10px; padding: 12px 16px; }
    .hero .lbl { color: #C9CED6; font-size: 10px; }
    .hero .range { color: #FFB800; font-size: 25px; font-weight: bold; margin: 3px 0 4px; }
    .hero .typ { color: #DFE3E8; }
    .hero .fx { color: #AAB1BC; font-size: 9px; margin-top: 6px; }
    h2 { font-size: 12.5px; margin: 14px 0 5px; padding-bottom: 3px; border-bottom: 2px solid #FFB800; }
    .grid td { padding: 3px 8px; border-bottom: 1px solid #E3E6EA; }
    .grid td.k { color: #5B6472; width: 38%; }
    .tbl th { background: #F5F6F8; text-align: left; font-size: 9.2px; color: #5B6472; padding: 5px 7px; border-bottom: 1px solid #D5D9DF; }
    .tbl td { padding: 4px 7px; border-bottom: 1px solid #E3E6EA; vertical-align: top; }
    .tbl td.n, .tbl th.n { text-align: right; white-space: nowrap; }
    .tbl tr.sel td { background: #FFF8E1; font-weight: bold; }
    .gantt td.bar { padding: 4px 0; }
    .gbar { height: 9px; background: #FFB800; border-radius: 4px; }
    .ck td { padding: 4px 6px; border-bottom: 1px dashed #E3E6EA; vertical-align: top; }
    .box { display: inline-block; width: 9px; height: 9px; border: 1.2px solid #101318; border-radius: 2px; }
    .sp td { width: 50%; border: 1px solid #E3E6EA; border-radius: 6px; padding: 7px 9px; vertical-align: top; }
    .sptag { font-size: 8px; color: #8b93a0; text-transform: uppercase; letter-spacing: .05em; }
    .warn { margin-top: 10px; border-left: 4px solid #FFB800; background: #FFF8E1; padding: 9px 12px; border-radius: 6px; }
    .warn strong { display: block; margin-bottom: 3px; }
    .cta { margin-top: 10px; border: 1.5px solid #101318; border-radius: 8px; padding: 10px 12px; }
    .cta .fee { font-size: 12.5px; font-weight: bold; }
    .pb { page-break-before: always; }
    .cta, .warn, .sp tr, .tbl tr { page-break-inside: avoid; }
    .foot { position: fixed; bottom: -26px; left: 0; right: 0; font-size: 8.3px; color: #8b93a0; border-top: 1px solid #E3E6EA; padding-top: 5px; }
</style>
</head>
<body>

<div class="foot">worksitem.com · Kainta İnşaat · Pro rapor {{ $reportNo }} · {{ now()->format('d.m.Y H:i') }} · Yaklaşık hesaptır, teklif değildir.</div>

<table class="head">
    <tr>
        <td>@if ($logo)<img src="{{ $logo }}" style="height:34px">@else<b style="font-size:18px">worksitem</b>@endif</td>
        <td class="r">
            <div class="doc-title">{{ $page['title'] }} Pro Raporu <span class="pro">PRO</span></div>
            <div class="muted">Rapor no: {{ $reportNo }} · {{ now()->locale('tr')->translatedFormat('d F Y') }}</div>
        </td>
    </tr>
</table>

<div class="hero">
    <div class="lbl">Tahmini maliyet aralığı (malzeme + işçilik, KDV dahil)</div>
    <div class="range">{{ $tl($est['low']) }} – {{ $tl($est['high']) }}</div>
    <div class="typ">En olası tutar {{ $tl($est['mid']) }}@if ($est['per_unit'] && $est['per_unit_label']) · {{ $est['per_unit_label'] }} yaklaşık {{ $tl($est['per_unit']) }}@endif @if ($est['days'])· {{ $est['days'][0] }}–{{ $est['days'][1] }} iş günü@endif</div>
    @if ($fx)
        <div class="fx">
            @foreach ($currencies as $code => $sym)
                @if (! empty($fx['rates'][$code]))
                    ≈ {{ $sym }}{{ number_format(round($est['low'] / $fx['rates'][$code], -1), 0, ',', '.') }} – {{ $sym }}{{ number_format(round($est['high'] / $fx['rates'][$code], -1), 0, ',', '.') }}&nbsp;&nbsp;
                @endif
            @endforeach
            (TCMB döviz satış kuru, {{ $fx['date'] }})
        </div>
    @endif
</div>

<h2>{{ ++$sec }}. Proje bilgileri</h2>
<table class="grid">
    @foreach ($inputs as [$k, $v])<tr><td class="k">{{ $k }}</td><td>{{ $v }}</td></tr>@endforeach
    <tr><td class="k">Fiyat tarihi</td><td>{{ $pricesLabel }}</td></tr>
</table>

<h2>{{ ++$sec }}. Kalem kalem maliyet</h2>
<table class="tbl">
    <tr><th>Kalem</th><th class="n">Alt</th><th class="n">En olası</th><th class="n">Üst</th></tr>
    @foreach ($est['items'] as $it)
        <tr><td><span style="display:inline-block;width:8px;height:8px;border-radius:2px;margin-right:6px;background:{{ $it['color'] }}"></span>{{ $it['label'] }}</td><td class="n">{{ $tl($it['low']) }}</td><td class="n"><b>{{ $tl($it['mid']) }}</b></td><td class="n">{{ $tl($it['high']) }}</td></tr>
    @endforeach
    <tr class="sel"><td>Toplam (yuvarlanmış)</td><td class="n">{{ $tl($est['low']) }}</td><td class="n">{{ $tl($est['mid']) }}</td><td class="n">{{ $tl($est['high']) }}</td></tr>
</table>

@if ($compare)
<h2>{{ ++$sec }}. Üç kalite seviyesinde karşılaştırma</h2>
<table class="tbl">
    <tr><th>Kalite</th><th>Açıklama</th><th class="n">Alt</th><th class="n">En olası</th><th class="n">Üst</th></tr>
    @foreach ($compare as $c)
        <tr class="{{ $c['selected'] ? 'sel' : '' }}">
            <td>{{ $c['label'] }}@if ($c['selected']) (seçiminiz)@endif</td>
            <td class="muted">{{ $c['hint'] }}</td>
            <td class="n">{{ $tl($c['low']) }}</td><td class="n">{{ $tl($c['mid']) }}</td><td class="n">{{ $tl($c['high']) }}</td>
        </tr>
    @endforeach
</table>
<p class="muted" style="margin:4px 0 0">Diğer seçimler aynı tutularak yalnızca kalite değiştirilmiştir.</p>
@endif

@if ($materials)
<h2>{{ ++$sec }}. Yaklaşık malzeme listesi</h2>
<table class="tbl">
    <tr><th>Malzeme</th><th class="n">Miktar</th><th>Birim</th><th>Not</th></tr>
    @foreach ($materials as [$name, $qty, $unit, $note])
        <tr><td>{{ $name }}</td><td class="n"><b>{{ $qty }}</b></td><td>{{ $unit }}</td><td class="muted">{{ $note }}</td></tr>
    @endforeach
</table>
<p class="muted" style="margin:4px 0 0">Miktarlar yaygın sarfiyat oranlarıyla yaklaşık hesaplanmıştır; ürün ve uygulamaya göre değişir. Kesin metraj keşifte çıkarılır.</p>
@endif

@if ($program)
@php $pu = $programUnit ?? 'gün'; @endphp
<h2 class="pb">{{ ++$sec }}. İş programı (yaklaşık {{ $totalDays }} {{ $pu === 'ay' ? 'ay' : 'iş günü' }})</h2>
<table class="tbl gantt">
    <tr><th style="width:38%">Aşama</th><th style="width:14%">{{ $pu === 'ay' ? 'Ay' : 'Gün' }}</th><th>Takvim</th></tr>
    @foreach ($program as $p)
        @php $left = ($p['from'] - 1) / max(1, $totalDays) * 100; $w = ($p['to'] - $p['from'] + 1) / max(1, $totalDays) * 100; @endphp
        <tr>
            <td>{{ $p['name'] }}</td>
            <td>{{ $p['from'] === $p['to'] ? $p['from'].'. '.$pu : $p['from'].'–'.$p['to'].'. '.$pu }}</td>
            <td class="bar"><table><tr>
                @if ($left > 0)<td style="width:{{ round($left, 2) }}%"></td>@endif
                <td style="width:{{ round($w, 2) }}%"><div class="gbar"></div></td>
                @if ($left + $w < 99.9)<td></td>@endif
            </tr></table></td>
        </tr>
    @endforeach
</table>
<p class="muted" style="margin:4px 0 0">Kuruma ve bekleme süreleri dahildir. Malzeme tedariki ve bina koşulları süreyi değiştirebilir.</p>
@endif

<h2>{{ ++$sec }}. Aşamalı ödeme planı önerisi (en olası tutar üzerinden)</h2>
<table class="tbl">
    <tr><th>Aşama</th><th class="n">Oran</th><th class="n">Yaklaşık tutar</th></tr>
    @foreach ($payments as [$label, $pct, $amount])
        <tr><td>{{ $label }}</td><td class="n">%{{ $pct }}</td><td class="n"><b>{{ $tl($amount) }}</b></td></tr>
    @endforeach
</table>
<p class="muted" style="margin:4px 0 0">Örnek bir plandır; ödeme koşullarını ustanızla yazılı olarak kararlaştırın.</p>

<h2>{{ ++$sec }}. Usta seçerken kontrol listesi</h2>
<table class="ck">
    @foreach ($checklist as $item)
        <tr><td style="width:16px"><span class="box"></span></td><td>{{ $item }}</td></tr>
    @endforeach
</table>

@if ($sponsors->isNotEmpty())
    <h2>Sponsorlu markalar</h2>
    <p class="muted" style="margin:0 0 5px">Aşağıdaki markalar Worksitem'e sponsor olarak destek vermektedir. Sıralama hesap sonucunuzu etkilemez; seçim tamamen size aittir.</p>
    <table class="sp" style="border-collapse:separate;border-spacing:6px">
        @foreach ($sponsors->chunk(2) as $row)
            <tr>
                @foreach ($row as $s)
                    <td><div class="sptag">Sponsorlu</div><b>{{ $s->brand }}</b> — {{ $s->headline }}<br><span class="muted">{{ \Illuminate\Support\Str::limit((string) $s->body, 110) }}</span><br><span class="muted">{{ parse_url($s->target_url, PHP_URL_HOST) }}</span></td>
                @endforeach
                @if ($row->count() < 2)<td style="border:0"></td>@endif
            </tr>
        @endforeach
    </table>
@endif

<div style="page-break-inside: avoid">
<div class="warn">
    <strong>Bu rapor yaklaşık bir hesaplamadır.</strong>
    {{ config('worksitem_legal.result_short') }} Otomatik hesaplama ve yapay zekâ araçları hata yapabilir. Bu rapor yerinde keşif, kesin metraj ve yazılı teklif yerine geçmez.
</div>

<div class="cta">
    <div class="fee">Kesin fiyat için yerinde keşif isteyin — keşif bedeli {{ \App\Support\InspectionOffer::feeLabel() }}</div>
    <div class="muted"><x-inspection-fee-note /></div>
    <div class="muted" style="margin-top:3px">worksitem.com/araclar/{{ $e->slug() }} · WhatsApp / Çağrı Merkezi: {{ config('worksitem_inspection.phone') }} · info@worksitem.com</div>
</div>
</div>
</body>
</html>
