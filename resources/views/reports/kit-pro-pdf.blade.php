@php
    $tl = fn ($v) => '₺'.number_format($v, 0, ',', '.');
    $n1 = fn ($v) => number_format($v, 1, ',', '.');
    $sec = 0;
    $currencies = (array) config('worksitem_bathroom.fx.currencies');
    $issuedAt = $issuedAt ?? now('Europe/Istanbul');
    $equivalents = $equivalents ?? [];
    $expert = $expert ?? [];
    $eqSponsors = collect($equivalents)->unique('id')->values();
    $sheetChecked = $eqSponsors->filter(fn ($s) => $s->tech_sheet_checked_on)->values();
    $eqDemo = ! app()->environment('production') && $eqSponsors->contains(fn ($s) => $s->isDemo());
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
    .head { border-bottom: 1px solid #E5E7EB; }
    .head td { padding: 0 0 12px; }
    .doc-title { font-size: 15px; font-weight: bold; color: #111111; }
    .doc-meta { font-size: 9px; color: #6B7280; margin-top: 2px; }
    .pro { display: inline-block; background: #FFB800; color: #101318; font-weight: bold; font-size: 9px; padding: 2px 6px; border-radius: 4px; margin-left: 6px; vertical-align: middle; }
    .hero { margin-top: 16px; background: #101318; color: #fff; border-radius: 10px; padding: 12px 16px; text-align: right; }
    .hero .lbl { color: #C9CED6; font-size: 10px; }
    .hero .range { color: #FFB800; font-size: 25px; font-weight: bold; margin: 3px 0 4px; }
    .hero .typ { color: #DFE3E8; }
    .hero .fx { color: #AAB1BC; font-size: 9px; margin-top: 6px; }
    .sp-badge { display: inline-block; font-size: 7.5px; text-transform: uppercase; letter-spacing: .05em; color: #5B6472; border: 0.6px solid #8B93A0; border-radius: 3px; padding: 0 3px; }
    .eq { font-size: 8.5px; color: #6B7280; margin-top: 2px; }
    .notes td { padding: 5px 7px; border-bottom: 1px solid #E3E6EA; vertical-align: top; }
    .notes td.t { width: 24%; font-weight: bold; }
    .next { margin-top: 10px; border: 1px solid #E3E6EA; border-radius: 8px; padding: 9px 12px; }
    .next ol { margin: 4px 0 0 16px; padding: 0; }
    .sheet { margin-top: 6px; border: 1px solid #E3E6EA; border-radius: 6px; padding: 6px 9px; }
    .testenv { margin-top: 6px; background: #F5F6F8; border: 1px dashed #A0A7B2; border-radius: 6px; padding: 6px 9px; color: #5B6472; font-size: 8.5px; }
    .ads { margin-top: 14px; border-top: 1px solid #D5D9DF; padding-top: 6px; }
    .ads h3 { font-size: 11.5px; margin: 0 0 3px; color: #5B6472; }
    .ads .sp td { background: #F5F6F8; border: 1px solid #E3E6EA; }
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
    .keep { page-break-inside: avoid; }
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

<div class="foot">worksitem.com · Pro rapor {{ $reportNo }} · {{ $issuedAt->format('d.m.Y H:i') }} · Yaklaşık hesaptır, teklif değildir. Sponsorlu içerikler “Sponsorlu” etiketiyle işaretlidir.</div>

<table class="head">
    <tr>
        <td style="width:40%">@if ($logo)<img src="{{ $logo }}" style="height:52px">@else<b style="font-size:18px">worksitem</b>@endif</td>
        <td class="r" style="width:60%">
            <div class="doc-title">{{ $page['title'] }} Pro Raporu<span class="pro">PRO</span></div>
            <div class="doc-meta">Rapor no: {{ $reportNo }} · {{ $issuedAt->locale('tr')->translatedFormat('d F Y') }}, saat {{ $issuedAt->format('H:i') }}</div>
        </td>
    </tr>
</table>

<div class="hero">
    <div class="lbl">Tahmini maliyet aralığı (malzeme + işçilik, KDV dahil)</div>
    <div class="range">{{ $tl($est['low']) }} – {{ $tl($est['high']) }}</div>
    <div class="typ">En olası tutar {{ $tl($est['mid']) }}@if ($est['per_unit'] && $est['per_unit_label']) · {{ $est['per_unit_label'] }} yaklaşık {{ $tl($est['per_unit']) }}@endif @if ($est['days'])· {{ $est['days'][0] }}–{{ $est['days'][1] }} iş günü@endif</div>
    @if ($fx)
        <div class="fx">
            {!! collect($currencies)->filter(fn ($sym, $code) => ! empty($fx['rates'][$code]))->map(fn ($sym, $code) => '≈&nbsp;'.e($sym).number_format(round($est['low'] / $fx['rates'][$code], -1), 0, ',', '.').'&nbsp;–&nbsp;'.e($sym).number_format(round($est['high'] / $fx['rates'][$code], -1), 0, ',', '.'))->join('&nbsp;&nbsp;&nbsp; ') !!}&nbsp;&nbsp;&nbsp; (TCMB döviz satış kuru, {{ $fx['date'] }})
        </div>
    @endif
</div>

<h2>{{ ++$sec }}. Proje bilgileri</h2>
<table class="grid">
    @foreach ($inputs as [$k, $v])<tr><td class="k">{{ $k }}</td><td>{{ $v }}</td></tr>@endforeach
    <tr><td class="k">Fiyatların güncellendiği tarih</td><td>{{ $pricesLabel }}<br><span class="muted" style="font-size:8.5px">{{ \App\Support\PriceDate::note($e->slug()) }}</span></td></tr>
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
<div class="keep">
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
</div>
@endif

@if ($materials)
<h2>{{ ++$sec }}. Yaklaşık malzeme listesi</h2>
<table class="tbl">
    <tr><th>Malzeme</th><th class="n">Miktar</th><th>Birim</th><th>Not</th></tr>
    @foreach ($materials as $mi => [$name, $qty, $unit, $note])
        @php $q = $equivalents[$mi] ?? null; @endphp
        <tr><td>{{ $name }}
            @if ($q)<div class="eq"><span class="sp-badge">Sponsorlu</span> Bu kalemde sponsor marka: {{ $q->brand }} — {{ \Illuminate\Support\Str::after((string) $q->headline, $q->brand.' · ') }}</div>@endif
        </td><td class="n"><b>{{ $qty }}</b></td><td>{{ $unit }}</td><td class="muted">{{ $note }}</td></tr>
    @endforeach
</table>
<p class="muted" style="margin:4px 0 0">Miktarlar yaygın sarfiyat oranlarıyla yaklaşık hesaplanmıştır; ürün ve uygulamaya göre değişir. Kesin metraj keşifte çıkarılır.</p>
@if ($equivalents)
<p class="muted" style="margin:3px 0 0;font-size:8.5px">“Sponsorlu” işaretli satırlar, o kalemde ürünü bulunan ve Worksitem'e sponsor olan markayı gösterir. Ürünün listedeki teknik sınıfa (ör. C2TE) uygunluğu Worksitem tarafından doğrulanmamıştır; miktar, tutar ve sıralama sponsordan bağımsızdır.</p>
@endif
@if ($sheetChecked->isNotEmpty())
<p class="sheet"><b>Teknik föy kontrolü:</b> Yaklaşık malzeme listesindeki sarfiyat oranları {{ $sheetChecked->pluck('brand')->unique()->join(', ') }} teknik föyüyle karşılaştırılmıştır ({{ $sheetChecked->max('tech_sheet_checked_on')->format('d.m.Y') }}). Kalem kalem maliyet piyasa fiyatlarıyla hesaplanır; sponsor tutarı etkilemez.</p>
@elseif ($eqDemo)
<p class="testenv"><b>TEST ORTAMI ·</b> Teknik föy karşılaştırması yapıldığında burada “Yaklaşık malzeme listesindeki sarfiyat oranları {{ $eqSponsors->pluck('brand')->unique()->join(', ') }} teknik föyüyle karşılaştırılmıştır” ibaresi yer alır. Canlı raporlarda bu kutu görünmez.</p>
@endif
@endif

@if ($expert)
<h2>{{ ++$sec }}. Uzman notları</h2>
<table class="notes">
    @foreach ($expert as [$t, $txt])<tr><td class="t">{{ $t }}</td><td>{{ $txt }}</td></tr>@endforeach
</table>
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

<div style="page-break-inside: avoid">
<div class="next">
    <b>Bu raporla ne yapmalı?</b>
    <ol>
        <li>Malzeme listesi ve kalem kalem maliyet tablosunu ustanıza gönderin; teklifi aynı kalemlerle, marka ve modeli yazılı isteyin.</li>
        <li>Uygulama sırasında “Uzman notları” bölümünü kontrol listesi olarak kullanın; aşama bitmeden bir sonrakine geçilmesin.</li>
        <li>Kesin fiyat için yerinde keşif isteyin (aşağıda).</li>
    </ol>
</div>
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
@if ($sponsors->isNotEmpty())
<div class="ads keep">
    <h3>Reklam · Sponsorlu markalar</h3>
    <p class="muted" style="margin:0 0 5px">Bu bölüm reklamdır. Aşağıdaki markalar Worksitem'e sponsordur; hesap sonucunu, miktarları ve sıralamayı etkilemez. Markalar alfabetik sıralıdır.</p>
    <table class="sp" style="border-collapse:separate;border-spacing:6px">
        @foreach ($sponsors->sortBy(fn ($s) => mb_strtolower((string) $s->brand.' '.$s->headline))->values()->chunk(2) as $row)
            <tr>
                @foreach ($row as $s)
                    <td><span class="sp-badge">Sponsorlu</span> <b>{{ $s->brand }}</b> — {{ \Illuminate\Support\Str::after((string) $s->headline, $s->brand.' · ') }}<br><span class="muted">{{ \Illuminate\Support\Str::limit((string) $s->body, 110) }}</span><br><span class="muted">{{ parse_url($s->target_url, PHP_URL_HOST) }}</span></td>
                @endforeach
                @if ($row->count() < 2)<td style="border:0;background:none"></td>@endif
            </tr>
        @endforeach
    </table>
</div>
@endif
</body>
</html>
