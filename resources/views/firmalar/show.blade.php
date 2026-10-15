@php
    $tools = collect((array) config('worksitem_market.tools'))->filter(fn ($m) => in_array($m[0], (array) $firm->trades, true))->keys()
        ->filter(fn ($s) => config("worksitem_tools.tools.$s"))->take(4);
    $desc = \Illuminate\Support\Str::limit($firm->description ?: $firm->name.': '.$firm->typeLabel().', '.implode(', ', (array) $firm->cities).'. '.implode(', ', $firm->serviceLabels()).'.', 155);
    $ld = array_filter([
        '@context' => 'https://schema.org', '@type' => in_array($firm->type, ['tedarikci', 'uretici'], true) ? 'Store' : 'HomeAndConstructionBusiness',
        'name' => $firm->name, 'description' => $desc, 'url' => route('firms.show', $firm),
        'areaServed' => array_map(fn ($c) => ['@type' => 'City', 'name' => $c], (array) $firm->cities),
        'foundingDate' => $firm->founded_year ? (string) $firm->founded_year : null,
        'sameAs' => $firm->isPro() && $firm->website ? [$firm->website] : null,
    ]);
@endphp
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ \Illuminate\Support\Str::limit($firm->name, 45) }} · {{ $firm->cities[0] ?? '' }} | WORKSİTEM</title>
    <meta name="description" content="{{ $desc }}">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ route('firms.show', $firm) }}">
    <script type="application/ld+json">{!! json_encode($ld, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
    @include('firmalar._style')
</head>
<body>
<x-site.contech-header />
<header class="fm-hero" data-kainta-auto-link="off"><div class="fm-in">
    <a href="{{ route('firms.index') }}" style="color:#C9CED6;font-size:13px">← Firma rehberi</a><br>
    <span class="fm-chip" style="margin-top:12px">{{ $firm->typeLabel() }}</span>
    @if ($firm->isPro())<span class="fm-badge">Öne çıkan</span>@endif @if ($firm->tax_no)<span class="fm-badge v">Vergi no doğrulandı</span>@endif
    <h1>{{ $firm->name }}</h1>
    <p>{{ implode(', ', (array) $firm->cities) }}{{ $firm->district ? ' · '.$firm->district : '' }}{{ $firm->founded_year ? ' · '.$firm->founded_year.'’den beri' : '' }}</p>
    <a class="fm-btn" href="{{ route('firms.contact', [$firm, 'whatsapp']) }}" rel="nofollow">WhatsApp'tan yazın</a>
    <a class="fm-btn o" style="color:#fff;margin-left:8px" href="{{ route('firms.contact', [$firm, 'ara']) }}" rel="nofollow">Arayın ({{ $firm->maskedPhone() }})</a>
</div></header>
<section class="fm"><div class="fm-in" style="padding-bottom:48px">
    <div class="fm-grid" style="grid-template-columns:2fr 1fr">
        <div class="fm-card">
            <h2 style="font-size:20px">Firma hakkında</h2>
            <p style="white-space:pre-line">{{ $firm->description ?: 'Firma henüz tanıtım yazısı eklemedi.' }}</p>
            <div class="fm-tags" style="margin-top:14px">@foreach ($firm->serviceLabels() as $l)<span>{{ $l }}</span>@endforeach</div>
            @if ($firm->isPro() && $firm->website)<p style="margin-top:14px"><a href="{{ $firm->website }}" target="_blank" rel="noopener nofollow" style="color:inherit;font-weight:700">Web sitesi ↗</a></p>@endif
        </div>
        <div class="fm-card">
            <h3>Teklif almadan önce</h3>
            <p>Maliyetinizi hesaplayıp firmaya hazırlıklı gidin:</p>
            <ul style="padding-left:18px;line-height:1.8;margin:8px 0 0">
                @forelse ($tools as $s)<li><a href="/araclar/{{ $s }}" style="color:inherit;font-weight:600">{{ config("worksitem_tools.tools.$s.title") }}</a></li>@empty<li><a href="/araclar" style="color:inherit">Tüm hesaplayıcılar</a></li>@endforelse
            </ul>
        </div>
    </div>
    <p class="lead" style="margin-top:22px;font-size:13px">Firma bilgileri firmanın beyanıdır; Worksitem kayıt sırasında kontrol eder ancak firmanın işçiliği ve teklifleri için garanti vermez. Sözleşme yapmadan önce yazılı teklif, fatura ve garanti koşullarını isteyin.</p>
</div></section>
<x-site.contech-footer />
</body>
</html>
