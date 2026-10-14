@php
    $svcLabel = ! empty($filter['hizmet']) ? ($services[$filter['hizmet']][0] ?? null) : null;
    $title = trim(($filter['il'] ?? '').' '.($svcLabel ?: 'Tadilat ve İnşaat')).' Firmaları';
@endphp
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} | Onaylı Firma Rehberi | WORKSİTEM</title>
    <meta name="description" content="{{ $title }}: Kainta İnşaat Uzmanları tarafından onaylanmış usta, tadilat firması, müteahhit ve tedarikçiler. Maliyetinizi hesaplayın, firmayla doğrudan iletişime geçin.">
    <meta name="robots" content="{{ $firms->total() > 0 ? 'index, follow' : 'noindex, follow' }}">
    <link rel="canonical" href="{{ $firms->currentPage() > 1 ? $firms->url($firms->currentPage()) : request()->fullUrlWithQuery(['page' => null]) }}">
    @include('firmalar._style')
</head>
<body>
<x-site.contech-header />
<header class="fm-hero" data-kainta-auto-link="off"><div class="fm-in">
    <span class="fm-chip">Onaylı firma rehberi</span>
    <h1>{{ $title }}</h1>
    <p>Listelenen her firma Kainta İnşaat Uzmanları tarafından incelenip onaylanmıştır. Önce maliyetinizi <a href="/araclar" style="color:var(--y)">hesaplayın</a>, sonra firmalardan teklif alın.</p>
</div></header>
<section class="fm"><div class="fm-in" style="padding-bottom:48px">
    <form class="fm-filter" method="GET" action="{{ route('firms.index') }}">
        <select name="hizmet" onchange="this.form.submit()" aria-label="Hizmet">
            <option value="">Tüm hizmetler</option>
            @foreach ($services as $k => $s)<option value="{{ $k }}" @selected(($filter['hizmet'] ?? '') === $k)>{{ $s[0] }}</option>@endforeach
        </select>
        <select name="il" onchange="this.form.submit()" aria-label="İl">
            <option value="">Tüm iller</option>
            @foreach ($cities as $c)<option value="{{ $c }}" @selected(($filter['il'] ?? '') === $c)>{{ $c }}</option>@endforeach
        </select>
        <noscript><button class="fm-btn" type="submit">Filtrele</button></noscript>
    </form>
    @if ($firms->isEmpty())
        <div class="fm-card"><h3>Bu seçimde henüz onaylı firma yok</h3><p>Bu alanda hizmet veriyorsanız <a href="{{ route('firms.page') }}" style="color:inherit;font-weight:700">ücretsiz kaydolun</a>; ilk listelenen siz olun.</p></div>
    @else
        <div class="fm-list">
            @foreach ($firms as $f)
                <a class="fm-firm {{ $f->isPro() ? 'pro' : '' }}" href="{{ route('firms.show', $f) }}">
                    <strong style="font-size:16.5px">{{ $f->name }}</strong>@if ($f->isPro())<span class="fm-badge">Öne çıkan</span>@endif @if ($f->tax_no)<span class="fm-badge v">Vergi no doğrulandı</span>@endif
                    <div><small>{{ $f->typeLabel() }} · {{ implode(', ', array_slice((array) $f->cities, 0, 3)) }}{{ count((array) $f->cities) > 3 ? '…' : '' }}</small></div>
                    <div class="fm-tags">@foreach (array_slice($f->serviceLabels(), 0, 4) as $l)<span>{{ $l }}</span>@endforeach</div>
                </a>
            @endforeach
        </div>
        <div style="margin-top:18px">{{ $firms->links() }}</div>
    @endif
    <p class="lead" style="margin-top:24px">Firma mısınız? <a href="{{ route('firms.page') }}" style="color:inherit;font-weight:800">Ücretsiz firma üyeliği →</a></p>
</div></section>
<x-site.contech-footer />
</body>
</html>
