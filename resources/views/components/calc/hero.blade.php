@props(['title', 'crumb' => null, 'facts' => []])
@php
    $fx = app(\App\Services\FxRates::class)->current();
    $crumb = $crumb ?? $title;
    $url = url()->current();
    $breadcrumb = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Ana sayfa', 'item' => 'https://worksitem.com/'],
            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Araçlar', 'item' => 'https://worksitem.com/araclar'],
            ['@type' => 'ListItem', 'position' => 3, 'name' => $crumb, 'item' => 'https://worksitem.com/'.ltrim(request()->path(), '/')],
        ],
    ];
@endphp
<nav class="wc-crumb" aria-label="Sayfa yolu">
    <a href="/">Ana sayfa</a> / <a href="/araclar">Araçlar</a> / <span>{{ $crumb }}</span>
</nav>
<header class="wc-head">
    <h1>{{ $title }}</h1>
    @if (trim($slot) !== '')
        <p class="wc-lede">{{ $slot }}</p>
    @endif
    <div class="wc-facts">
        <span>Ücretsiz, kayıt gerekmez</span>
        @foreach ($facts as $fact)
            <span>{{ $fact }}</span>
        @endforeach
        @if ($fx)<span>Döviz: TCMB {{ $fx['date'] }}</span>@endif
    </div>
</header>
<script type="application/ld+json">{!! json_encode($breadcrumb, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
