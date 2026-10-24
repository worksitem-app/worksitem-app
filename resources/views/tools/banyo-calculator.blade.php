@inject('estimator', 'App\Services\BathroomEstimator')
@inject('fxRates', 'App\Services\FxRates')
@php
    $cfg = $estimator->config();
    $default = [
        'type' => 'standart', 'w' => 1.8, 'l' => 2.4, 'h' => 2.6,
        'scope' => 'komple', 'quality' => 'orta', 'shower' => 'kabin', 'extras' => ['ex_dolap'],
    ];
    $est = $estimator->estimate($default);
    $table = $estimator->sizeTable();
    $ref = collect($table)->firstWhere('m2', 4) ?? $table[0];
    $fx = $fxRates->current();
    $tl = fn ($v) => '₺'.number_format($v, 0, ',', '.');
    $bin = fn ($v) => '₺'.number_format($v / 1000, 0, ',', '.').' bin';
    $year = now()->year;
    $labels = [
        'sokum' => 'Söküm ve moloz',
        'tesisat' => 'Su ve gider tesisatı',
        'yalitim' => 'Su yalıtımı',
        'seramik' => 'Seramik (zemin + duvar)',
        'vitrifiye' => 'Vitrifiye ve armatürler',
        'dus' => 'Duşakabin',
        'ekstra' => 'Seçilen ekstralar',
        'beklenmedik' => 'Beklenmedik gider payı',
    ];
    $title = "Banyo Tadilat Maliyeti Hesaplama $year | Worksitem";
    $desc = "$year banyo tadilat fiyatları: 4 m² banyo komple yenileme orta kalitede yaklaşık {$bin($ref['orta']['low'])} – {$bin($ref['orta']['high'])}. m², kalite ve kapsama göre ücretsiz hesaplayın.";
    $url = 'https://worksitem.com/araclar/banyo-maliyet-hesaplayici';
    $jsonFlags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP;
    $schema = [
        [
            '@context' => 'https://schema.org',
            '@type' => 'WebApplication',
            'name' => 'Banyo Tadilat Maliyeti Hesaplayıcı',
            'url' => $url,
            'applicationCategory' => 'BusinessApplication',
            'operatingSystem' => 'Web',
            'inLanguage' => 'tr-TR',
            'description' => 'Banyo tipine, ölçülere, kapsama ve kaliteye göre yaklaşık banyo tadilat maliyet aralığını kalem kalem hesaplar.',
            'offers' => ['@type' => 'Offer', 'price' => '0', 'priceCurrency' => 'TRY'],
            'publisher' => ['@type' => 'Organization', 'name' => 'Worksitem', 'url' => 'https://worksitem.com'],
        ],
        [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Ana sayfa', 'item' => 'https://worksitem.com/'],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Araçlar', 'item' => 'https://worksitem.com/araclar'],
                ['@type' => 'ListItem', 'position' => 3, 'name' => 'Banyo tadilat maliyeti', 'item' => $url],
            ],
        ],
    ];
    $jsCfg = [
        'types' => $cfg['types'], 'scopes' => $cfg['scopes'], 'qualities' => $cfg['qualities'],
        'showers' => $cfg['showers'], 'unit' => $cfg['unit'], 'extras' => $cfg['extras'],
        'band' => $cfg['band'], 'contingency_rate' => $cfg['contingency_rate'], 'door_area' => $cfg['door_area'],
        'labels' => $labels, 'fx' => $fx, 'symbols' => $cfg['fx']['currencies'],
    ];
@endphp
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <meta name="description" content="{{ $desc }}">
    <link rel="canonical" href="{{ $url }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $desc }}">
    <meta property="og:url" content="{{ $url }}">
    <meta property="og:image" content="https://worksitem.com/brand/og-image.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="https://worksitem.com/brand/og-image.png">
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="manifest" href="/site.webmanifest">
    <meta name="theme-color" content="#ffffff">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @foreach ($schema as $s)
        <script type="application/ld+json">{!! json_encode($s, $jsonFlags) !!}</script>
    @endforeach
    <style>
        :root{
            --ink:#101318; --yellow:#FFB800; --yellow-50:#FFF8E1; --amber:#8A6200;
            --page:#F5F6F8; --line:#E3E6EA; --muted:#5B6472; --white:#fff;
            --r-lg:18px; --r-md:12px; --r-sm:8px;
        }
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Inter',system-ui,-apple-system,'Segoe UI',sans-serif;background:var(--page);color:var(--ink);line-height:1.5;-webkit-text-size-adjust:100%}
        a{color:inherit}
        button,input,select,textarea{font:inherit;color:inherit}
        :focus-visible{outline:3px solid var(--yellow);outline-offset:2px}

        .bt-wrap{max-width:1160px;margin:0 auto;padding:20px 16px 40px}
        .bt-crumb{font-size:13px;color:var(--muted);margin-bottom:14px}
        .bt-crumb a{text-decoration:none}
        .bt-crumb a:hover{text-decoration:underline}
        .bt-head h1{font-size:clamp(26px,5vw,40px);line-height:1.12;letter-spacing:-.02em;font-weight:900;max-width:22ch}
        .bt-lede{margin-top:10px;font-size:16px;color:#363c46;max-width:62ch}
        .bt-lede strong{color:var(--ink)}
        .bt-facts{display:flex;flex-wrap:wrap;gap:8px;margin-top:14px;font-size:13px;color:var(--muted)}
        .bt-facts span{background:var(--white);border:1px solid var(--line);border-radius:999px;padding:5px 11px}

        .bt-grid{display:grid;grid-template-columns:minmax(0,7fr) minmax(0,5fr);gap:24px;margin-top:24px;align-items:start}

        /* Form */
        .bt-form{background:var(--white);border:1px solid var(--line);border-radius:var(--r-lg);padding:8px 22px 22px}
        .bt-step{padding:18px 0;border-bottom:1px solid var(--line)}
        .bt-step:last-of-type{border-bottom:0}
        .bt-step h2{display:flex;align-items:center;gap:10px;font-size:16px;font-weight:800;margin-bottom:12px}
        .bt-step h2 i{font-style:normal;display:inline-grid;place-items:center;width:24px;height:24px;border-radius:50%;background:var(--ink);color:var(--white);font-size:12px;font-weight:800;flex:0 0 24px}
        .bt-note{font-size:13px;color:var(--muted);margin-top:8px}

        .bt-opts{display:grid;gap:8px}
        .bt-opts.c3{grid-template-columns:repeat(3,minmax(0,1fr))}
        .bt-opt{position:relative}
        .bt-opt input{position:absolute;opacity:0;pointer-events:none}
        .bt-opt label{display:block;height:100%;cursor:pointer;border:1.5px solid var(--line);border-radius:var(--r-md);padding:11px 12px;background:var(--white);transition:border-color .15s,background .15s}
        .bt-opt label b{display:block;font-size:14px;font-weight:800;line-height:1.25}
        .bt-opt label small{display:block;font-size:12px;color:var(--muted);margin-top:3px;line-height:1.35}
        .bt-opt-note{font-size:12px;font-weight:600;color:var(--muted);margin-left:6px;letter-spacing:0;text-transform:none}
        .bt-step.is-missing{outline:2px solid #F04438;outline-offset:6px;border-radius:14px}
        .bt-opt input:checked + label{border-color:var(--ink);background:var(--yellow-50);box-shadow:inset 0 0 0 1px var(--ink)}
        .bt-opt input:focus-visible + label{outline:3px solid var(--yellow);outline-offset:2px}
        .bt-opt label:hover{border-color:#9aa1ab}

        .bt-dims{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px}
        .bt-field label{display:block;font-size:13px;font-weight:700;margin-bottom:6px}
        .bt-input{display:flex;align-items:center;border:1.5px solid var(--line);border-radius:var(--r-md);background:var(--white);height:48px;padding:0 12px}
        .bt-input:focus-within{border-color:var(--ink)}
        .bt-input input{border:0;outline:0;width:100%;font-size:17px;font-weight:700;background:transparent;min-width:0}
        .bt-input span{font-size:13px;color:var(--muted);font-weight:600}
        .bt-area{margin-top:10px;font-size:13px;color:var(--muted)}
        .bt-area b{color:var(--ink)}

        .bt-checks{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px}
        .bt-check{display:flex;gap:10px;align-items:center;border:1.5px solid var(--line);border-radius:var(--r-md);padding:11px 12px;cursor:pointer;font-size:14px;font-weight:600;background:var(--white)}
        .bt-check input{width:20px;height:20px;accent-color:var(--ink);flex:0 0 20px}
        .bt-check:has(input:checked){border-color:var(--ink);background:var(--yellow-50)}

        .bt-cta{display:block;width:100%;margin-top:6px;min-height:52px;border:0;border-radius:var(--r-md);background:var(--ink);color:var(--white);font-weight:800;font-size:16px;cursor:pointer}
        .bt-cta:hover{background:#000}

        /* Result: the tile slip */
        .bt-result{position:sticky;top:84px;border-radius:var(--r-lg);overflow:hidden;background:var(--ink);color:var(--white);
            background-image:linear-gradient(rgba(255,255,255,.045) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.045) 1px,transparent 1px);
            background-size:28px 28px}
        .bt-r-top{padding:22px 22px 18px}
        .bt-r-label{font-size:14px;color:#c9ced6;font-weight:600}
        .bt-range{margin-top:6px;font-size:clamp(30px,6.4vw,40px);line-height:1.08;font-weight:900;letter-spacing:-.025em;color:var(--yellow);font-variant-numeric:tabular-nums}
        .bt-range .sep{color:#8b93a0;font-weight:700;padding:0 .12em}
        .bt-typ{margin-top:8px;font-size:14px;color:#dfe3e8}
        .bt-range.is-waiting{color:#dfe3e8;font-size:clamp(24px,5vw,30px)}
        .bt-quote:disabled{opacity:.45;cursor:not-allowed}
        .bt-input.is-missing{border-color:#DC2626;box-shadow:0 0 0 3px rgba(220,38,38,.12)}
        .bt-typ b{color:var(--white)}
        .bt-fx{display:flex;flex-wrap:wrap;gap:6px 14px;margin-top:10px;font-size:12.5px;color:#aab1bc;font-variant-numeric:tabular-nums}
        .bt-fx[hidden]{display:none}
        .bt-fx em{font-style:normal;color:#7f8794}
        .bt-ruler{position:relative;height:10px;margin:18px 0 6px;border-radius:999px;background:linear-gradient(90deg,#3b414c,var(--yellow) 50%,#3b414c)}
        .bt-ruler i{position:absolute;top:-4px;left:50%;width:2px;height:18px;background:var(--white);transform:translateX(-1px)}
        .bt-ruler-l{display:flex;justify-content:space-between;font-size:12px;color:#9aa1ab}

        .bt-r-body{background:var(--white);color:var(--ink);padding:18px 22px 20px}
        .bt-bar{display:flex;height:12px;border-radius:999px;overflow:hidden;background:var(--line);margin-bottom:14px}
        .bt-bar span{display:block;height:100%}
        .bt-lines{list-style:none}
        .bt-lines li{display:grid;grid-template-columns:12px 1fr auto;gap:10px;align-items:center;padding:8px 0;border-bottom:1px dashed var(--line);font-size:14px}
        .bt-lines li:last-child{border-bottom:0}
        .bt-lines li i{width:10px;height:10px;border-radius:3px}
        .bt-lines li b{font-weight:700;font-variant-numeric:tabular-nums;white-space:nowrap;font-size:13.5px}
        .bt-meta{display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:14px}
        .bt-meta div{background:var(--page);border-radius:var(--r-sm);padding:10px 12px;font-size:12.5px;color:var(--muted)}
        .bt-meta strong{display:block;color:var(--ink);font-size:15px;margin-top:2px}
        .bt-disc{margin-top:12px;font-size:12.5px;color:var(--muted);line-height:1.5}
        .bt-quote{display:block;width:100%;margin-top:14px;min-height:52px;border:0;border-radius:var(--r-md);background:var(--yellow);color:var(--ink);font-weight:800;font-size:16px;cursor:pointer}
        .bt-quote:hover{background:#E5A500}

        /* Lead flow (mevcut eleme kapısı korunur) */
        .bt-lead-area{max-width:760px;margin:24px auto 0}
        .bt-sponsors{max-width:1100px;margin:0 auto}
        .bt-pro{display:block;margin-top:10px;padding:12px 14px;border:1.5px dashed #FFB800;border-radius:12px;background:#FFF8E1;color:#101318;text-decoration:none;line-height:1.35}
        .bt-pro:hover{border-style:solid}
        .bt-pro strong{display:block;font-size:14px}
        .bt-pro small{display:block;font-size:12px;color:#5B6472;margin-top:3px}
        .bt-pro-tag{display:inline-block;font-size:10.5px;font-weight:800;background:#101318;color:#FFB800;border-radius:999px;padding:2px 8px;margin-bottom:5px;text-transform:uppercase;letter-spacing:.04em}
        html[data-theme="dark"] .bt-pro{background:#2A2410;color:#fff}
        html[data-theme="dark"] .bt-pro small{color:#C9CED6}
        .bt-plan-note{margin-top:10px;padding:10px 12px;border-radius:10px;background:#FFF8E1;border:1px solid #F5D98A;font-size:13px;line-height:1.45}
        .bt-plan-note b{display:block;font-size:14px}
        html[data-theme="dark"] .bt-plan-note{background:#2A2410;border-color:#5a4a1a}
        .bt-plan{display:flex;align-items:center;gap:12px;margin-top:10px;padding:12px 14px;border:1.5px solid #101318;border-radius:12px;background:#fff;color:#101318;text-decoration:none;line-height:1.35}
        .bt-plan:hover{background:#F5F6F8}
        .bt-plan svg{flex:0 0 34px}
        .bt-plan strong{display:block;font-size:14px}
        .bt-plan small{display:block;font-size:12px;color:#5B6472;margin-top:2px}
        html[data-theme="dark"] .bt-plan{background:#141A24;color:#fff;border-color:#E6E9EE}
        html[data-theme="dark"] .bt-plan small{color:#C9CED6}
        .budget-confirm-box{padding:22px;border:1px solid var(--line);border-radius:var(--r-lg);background:var(--white);display:none;text-align:center}
        .budget-confirm-title{font-size:18px;font-weight:900;margin-bottom:6px}
        .budget-confirm-text{color:var(--muted);font-size:14px;margin-bottom:16px}
        .budget-confirm-actions{display:grid;grid-template-columns:1fr 1fr;gap:10px}
        .budget-btn{min-height:52px;border-radius:var(--r-md);padding:10px 14px;font-size:14px;font-weight:800;border:1.5px solid var(--line);cursor:pointer;background:var(--white)}
        .budget-yes:hover{border-color:var(--ink);background:var(--yellow-50)}
        .qualified-message{display:none;margin-top:16px;padding:16px;border-radius:var(--r-md);background:var(--yellow-50);border:1px solid #fde68a;text-align:center;font-size:14px}
        .bt-errors{background:#FEF2F2;border:1px solid #FCA5A5;color:#7F1D1D;border-radius:var(--r-md);padding:14px 16px;margin-bottom:14px;font-size:14px}
        .bt-errors ul{margin:6px 0 0 18px}
        .lead-box{background:var(--white);border:1px solid var(--line);border-radius:var(--r-lg);padding:22px;margin-top:14px;display:none}
        .lead-box h3{font-size:19px;font-weight:900;margin-bottom:6px}
        .lead-box p{font-size:14px;color:var(--muted);margin-bottom:16px}
        .lead-box .form-group{margin-bottom:14px}
        .lead-box label{display:block;font-size:13px;font-weight:700;margin-bottom:6px}
        .form-control{width:100%;min-height:48px;border:1.5px solid var(--line);border-radius:var(--r-md);padding:10px 12px;font-size:16px;background:var(--white)}
        .form-control:focus{outline:0;border-color:var(--ink)}
        .btn-submit{width:100%;min-height:52px;border:0;border-radius:var(--r-md);background:var(--yellow);font-weight:800;font-size:16px;cursor:pointer;margin-top:6px}

        /* SEO içerik */
        .bt-sec{max-width:860px;margin:40px auto 0}
        .bt-sec h2{font-size:clamp(21px,3.4vw,26px);font-weight:900;letter-spacing:-.015em;margin-bottom:10px}
        .bt-sec > p{color:#363c46;margin-bottom:14px;max-width:70ch}
        .bt-table-wrap{overflow-x:auto;border:1px solid var(--line);border-radius:var(--r-md);background:var(--white)}
        .bt-table{width:100%;border-collapse:collapse;font-size:14px;font-variant-numeric:tabular-nums;min-width:520px}
        .bt-table th,.bt-table td{white-space:nowrap;padding:11px 14px;text-align:left;border-bottom:1px solid var(--line)}
        .bt-table thead th{background:var(--page);font-weight:800;font-size:13px}
        .bt-table tbody tr:last-child td,.bt-table tbody tr:last-child th{border-bottom:0}
        .bt-table th[scope=row]{font-weight:800}
        .bt-small{font-size:13px;color:var(--muted);margin-top:8px}
        .bt-factors{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;list-style:none}
        .bt-factors li{background:var(--white);border:1px solid var(--line);border-radius:var(--r-md);padding:14px}
        .bt-factors b{display:block;margin-bottom:3px}
        .bt-factors span{font-size:14px;color:var(--muted)}
        .bt-related{display:flex;flex-wrap:wrap;gap:8px}
        .bt-related a{text-decoration:none;font-weight:700;font-size:14px;border:1.5px solid var(--line);background:var(--white);border-radius:999px;padding:9px 14px}
        .bt-related a:hover{border-color:var(--ink)}

        /* Mobil sabit sonuç çubuğu */
        .bt-dock{display:none}

        @media (max-width:900px){
            .bt-grid{grid-template-columns:1fr}
            .bt-result{position:relative;top:0}
            .bt-dock{display:flex;position:fixed;left:0;right:0;bottom:0;z-index:40;align-items:center;justify-content:space-between;gap:12px;
                background:var(--ink);color:var(--white);padding:10px 16px calc(10px + env(safe-area-inset-bottom));box-shadow:0 -6px 20px rgba(16,19,24,.18);transition:transform .2s}
            .bt-dock.is-hidden{transform:translateY(110%)}
            .bt-dock small{display:block;font-size:12px;color:#aab1bc}
            .bt-dock strong{display:block;font-size:18px;font-weight:900;color:var(--yellow);font-variant-numeric:tabular-nums;white-space:nowrap}
            .bt-dock button{border:0;border-radius:10px;background:var(--yellow);color:var(--ink);font-weight:800;font-size:14px;padding:11px 14px;min-height:44px;cursor:pointer;white-space:nowrap}
            body{padding-bottom:72px}
        }
        @media (max-width:560px){
            .bt-form{padding:4px 14px 16px}
            .bt-opts.c3{grid-template-columns:1fr}
            .bt-opts.c3 .bt-opt label{display:flex;align-items:baseline;gap:8px;flex-wrap:wrap}
            .bt-dims{gap:8px}
            .bt-input{padding:0 9px}
            .bt-checks,.bt-factors,.budget-confirm-actions{grid-template-columns:1fr}
            .bt-r-top,.bt-r-body{padding-left:16px;padding-right:16px}
        }
        @media (prefers-reduced-motion:reduce){*{transition:none!important;scroll-behavior:auto!important}}
        @media print{.bt-dock,.bt-cta,.bt-quote,.bt-lead-area,.bt-related{display:none!important}.bt-result{position:static}.bt-grid{grid-template-columns:1fr}}
    </style>
</head>
<body data-tool-slug="banyo-calculator">

<x-site.contech-header />

<main class="bt-wrap">
    <nav class="bt-crumb" aria-label="Sayfa yolu">
        <a href="/">Ana sayfa</a> / <a href="/araclar">Araçlar</a> / <span>Banyo tadilat maliyeti</span>
    </nav>

    <header class="bt-head">
        <h1>Banyo tadilat maliyeti hesaplama ({{ $year }})</h1>
        <p class="bt-lede">
            {{ $year }} fiyatlarıyla 4 m² standart bir banyonun komple yenilemesi orta kalitede
            <strong>yaklaşık {{ $tl($ref['orta']['low']) }} – {{ $tl($ref['orta']['high']) }}</strong> tutar
            (malzeme ve işçilik, KDV dahil). Kendi banyonuz için tipi, ölçüleri ve kaliteyi seçin; sonuç kesin teklif değil, yaklaşık bir aralıktır.
        </p>
        <div class="bt-facts">
            <span>Ücretsiz, kayıt gerekmez</span>
            <span>Fiyatlar: {{ $estimator->pricesLabel() }}</span>
            @if ($fx)<span>Döviz: TCMB {{ $fx['date'] }}</span>@endif
        </div>
    </header>

    <div class="bt-grid">
        <form class="bt-form" id="btForm" onsubmit="return false" aria-label="Banyo bilgileri">
            {{-- PDF rapor ölçüsüz istenirse (BathroomReportController) hata teklif formunda değil, ölçülerin yanında görünür --}}
            <div class="bt-errors" id="bPdfErr" role="alert" @unless ($errors->has('width')) hidden @endunless>{{ $errors->first('width') ?: 'PDF rapor için önce banyonun en ve boy ölçülerini girin.' }}</div>
            <input type="hidden" id="bathType" value="standart">
            <input type="hidden" id="scope" value="">
            <input type="hidden" id="quality" value="">
            <input type="hidden" id="shower" value="">

            <section class="bt-step">
                <h2><i>1</i>Banyo tipi</h2>
                <div class="bt-opts c3" data-group="bathType">
                    @foreach ($cfg['types'] as $k => $t)
                        <div class="bt-opt">
                            <input type="radio" name="r_bathType" id="t_{{ $k }}" value="{{ $k }}" @checked($k === 'standart')>
                            <label for="t_{{ $k }}"><b>{{ $t['label'] }}</b><small>{{ $t['hint'] }}</small></label>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="bt-step">
                <h2><i>2</i>Ölçüler</h2>
                <div class="bt-dims">
                    <div class="bt-field"><label for="width">En</label><div class="bt-input"><input type="number" id="width" inputmode="decimal" min="0.5" max="10" step="0.1" placeholder="Örn. 1,8" required><span>m</span></div></div>
                    <div class="bt-field"><label for="length">Boy</label><div class="bt-input"><input type="number" id="length" inputmode="decimal" min="0.5" max="10" step="0.1" placeholder="Örn. 2,4" required><span>m</span></div></div>
                    <div class="bt-field"><label for="height">Yükseklik</label><div class="bt-input"><input type="number" id="height" inputmode="decimal" min="2" max="4" step="0.1" placeholder="2,6"><span>m</span></div></div>
                </div>
                <p class="bt-area" id="areaInfo">Banyonun en ve boyunu metre olarak yazın. Duvar yüksekliğini bilmiyorsanız boş bırakın; 2,6 m alınır.</p>
            </section>

            <section class="bt-step">
                <h2><i>3</i>Ne kadar değişecek?</h2>
                <div class="bt-opts" data-group="scope">
                    @foreach ($cfg['scopes'] as $k => $s)
                        <div class="bt-opt">
                            <input type="radio" name="r_scope" id="s_{{ $k }}" value="{{ $k }}">
                            <label for="s_{{ $k }}"><b>{{ $s['label'] }}</b><small>{{ $s['hint'] }}</small></label>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="bt-step">
                <h2><i>4</i>Malzeme kalitesi</h2>
                <div class="bt-opts c3" data-group="quality">
                    @foreach ($cfg['qualities'] as $k => $q)
                        <div class="bt-opt">
                            <input type="radio" name="r_quality" id="q_{{ $k }}" value="{{ $k }}">
                            <label for="q_{{ $k }}"><b>{{ $q['label'] }}</b><small>{{ $q['hint'] }}</small></label>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="bt-step" id="showerStep">
                <h2><i>5</i>Duş tercihi <small class="bt-opt-note">isteğe bağlı</small></h2>
                <div class="bt-opts c3" data-group="shower">
                    @foreach ($cfg['showers'] as $k => $s)
                        <div class="bt-opt">
                            <input type="radio" name="r_shower" id="d_{{ $k }}" value="{{ $k }}">
                            <label for="d_{{ $k }}"><b>{{ $s['label'] }}</b></label>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="bt-step">
                <h2><i id="extrasNo">6</i>Ekstralar <small class="bt-opt-note">isteğe bağlı</small></h2>
                <div class="bt-checks">
                    @foreach ($cfg['extras'] as $k => $e)
                        <label class="bt-check"><input type="checkbox" id="{{ $k }}"> {{ $e['label'] }}</label>
                    @endforeach
                </div>
            </section>

            <button type="button" class="bt-cta" id="calculateBtn">İlerle</button>
        </form>

        <aside class="bt-result" id="resultCard" aria-live="polite" aria-label="Tahmini maliyet">
            <div class="bt-r-top">
                <div class="bt-r-label">Tahmini maliyet aralığı</div>
                <div class="bt-range is-waiting" id="bTotal">Ölçüler bekleniyor</div>
                <div class="bt-typ" id="bTyp">Sol taraftan banyonun en ve boy ölçülerini girin; maliyet aralığı burada anında hesaplanır.</div>
                <div class="bt-fx" id="bFx" @if (! $fx) hidden @endif></div>
                <div class="bt-ruler" aria-hidden="true"><i></i></div>
                <div class="bt-ruler-l" aria-hidden="true"><span>Uygun usta / yerel ürün</span><span>Tasarım ürünü / büyük şehir</span></div>
            </div>
            <div class="bt-r-body" id="resultBody">
                <div class="bt-bar" id="bBar" aria-hidden="true"></div>
                <ul class="bt-lines" id="bLines">
                    @foreach (array_keys($est['items']) as $k)
                        @if ($est['items'][$k]['mid'] > 0)
                            <li><i></i><span>{{ $labels[$k] }}</span><b>—</b></li>
                        @endif
                    @endforeach
                </ul>
                <p class="bt-plan-note" id="bPlan" hidden></p>
                <div class="bt-meta">
                    <div>Tahmini süre<strong id="bDays">{{ $est['days'][0] }}–{{ $est['days'][1] }} iş günü</strong></div>
                    <div>Kaplama alanı<strong id="bTile">—</strong></div>
                </div>
                <p class="bt-disc">{{ config('worksitem_legal.result_short') }} Beklenmedik gider payı eski tesisat, şap veya nem hasarı gibi sürprizler içindir. <a href="/legal/kullanim-kosullari">Koşullar</a></p>
                <button type="button" class="bt-quote" id="quoteBtn">Bu aralıkla teklif iste</button>
                <a class="bt-pro" id="proBtn" href="{{ route('tools.banyo_pro_report') }}" data-kainta-auto-link="off">
                    <span class="bt-pro-tag">Ücretsiz</span>
                    <strong>Pro rapor: malzeme listesi, iş programı, 3 kalite karşılaştırması</strong>
                    <small>Telefonunuza gelen kodla 1 dakikada indirin</small>
                </a>
                <a class="bt-plan" id="planBtn" href="{{ route('tools.banyo_planner') }}" data-kainta-auto-link="off">
                    <svg width="34" height="34" viewBox="0 0 34 34" aria-hidden="true"><rect x="3" y="3" width="28" height="28" rx="4" fill="none" stroke="currentColor" stroke-width="2"/><rect x="6" y="6" width="10" height="10" rx="1.5" fill="#FFB800"/><ellipse cx="25" cy="10" rx="4" ry="3" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M6 24h8M24 20v8" stroke="currentColor" stroke-width="1.6"/></svg>
                    <span><strong>Banyomu 2D / 3D gör</strong><small>Ölçüleriniz plana otomatik aktarılır; ücretsiz</small></span>
                </a>
            </div>
        </aside>
    </div>

    <div class="bt-sponsors"><x-sponsor.slot tool="banyo-maliyet-hesaplayici" title="Banyonuz için markalar" /></div>

    <div class="bt-lead-area" id="teklif">
        @php($leadErrors = collect($errors->getMessages())->except('width')->flatten())
        @if ($leadErrors->isNotEmpty())
            <div class="bt-errors" role="alert">
                <strong>Teklif talebiniz gönderilemedi:</strong>
                <ul>@foreach ($leadErrors as $err)<li>{{ $err }}</li>@endforeach</ul>
            </div>
        @endif
        <x-market.choice tool="banyo-maliyet-hesaplayici" />
        <div class="budget-confirm-box" id="budgetConfirmBox">
            <div class="budget-confirm-title">Bu bütçe aralığı projeniz için gerçekçi mi?</div>
            <div class="budget-confirm-text">Kainta İnşaat'a başvurmadan önce bütçenizin bu seviyede olduğunu onaylayın. Böylece yalnızca gerçekten uygulanabilir projeleri değerlendirmeye alabiliriz.</div>
            <div class="budget-confirm-actions">
                <button type="button" class="budget-btn budget-yes" id="btnYes">Evet, bu bütçeyi karşılayabilirim</button>
                <button type="button" class="budget-btn budget-no" id="btnNo">Henüz bütçemi netleştirmedim</button>
            </div>
        </div>

        <div class="qualified-message" id="qualifiedMessage">
            <strong>Projeniz Kainta İnşaat değerlendirmesine uygun görünüyor.</strong><br>
            Formu doldurduğunuzda keşif ve teklif süreci için sizinle iletişime geçilecek.
        </div>

        <div class="lead-box" id="leadBox" @if ($errors->any()) style="display:block" @endif>
            <h3>Banyo tadilatı için teklif alın</h3>
            <p>Hesapladığınız bilgiler forma eklenir. Kainta İnşaat uzmanları yerinde ölçü alıp anahtar teslim teklif hazırlar.</p>
            <p class="bt-small" style="margin-top:-8px"><x-inspection-fee-note /></p>
            <form action="{{ route('lead.store') }}" method="POST">
                @csrf
                <input type="hidden" name="calculated_service" value="Banyo Tadilatı">
                <input type="hidden" name="calculated_area" id="leadCalculatedData" value="">
                <input type="hidden" name="estimated_min" id="leadMin" value="">
                <input type="hidden" name="estimated_max" id="leadMax" value="">
                <input type="hidden" name="estimated_cost" id="leadMid" value="">
                <input type="hidden" name="budget_confirmed" value="1">
                <div class="form-group"><label for="ld_name">Adınız soyadınız</label><input type="text" id="ld_name" name="name" value="{{ old('name') }}" class="form-control" required autocomplete="name"></div>
                <div class="form-group"><label for="ld_phone">Telefon</label><input type="tel" id="ld_phone" name="phone" value="{{ old('phone') }}" class="form-control" required autocomplete="tel" placeholder="05XX XXX XX XX"></div>
                <div class="form-group"><label for="ld_loc">Şehir / ilçe</label><input type="text" id="ld_loc" name="location" value="{{ old('location') }}" class="form-control" required placeholder="Örn: İzmir, Karşıyaka"></div>
                <div class="form-group"><label for="ld_notes">Notunuz (isteğe bağlı)</label><textarea id="ld_notes" name="notes" class="form-control" rows="3" placeholder="Örn: gömme rezervuar, özel ölçü duşakabin">{{ old('notes') }}</textarea></div>
                <x-lead-consent-fields />
                <button type="submit" class="btn-submit" {!! \App\Support\Ab::attr('tool_lead_submit') !!}>{{ \App\Support\Ab::text('tool_lead_submit') }}</button>
                <x-site.ab-script />
            </form>
        </div>
    </div>

    <section class="bt-sec" id="boyuta-gore">
        <h2>Boyuta göre banyo tadilat maliyeti ({{ $year }})</h2>
        <p>Standart banyo, komple yenileme (kırım, yeni tesisat, su yalıtımı, seramik, vitrifiye, duşakabin, lavabo dolabı) ve %10 beklenmedik gider payı dahil yaklaşık aralıklar:</p>
        <div class="bt-table-wrap">
            <table class="bt-table">
                <caption class="bt-small" style="caption-side:bottom;text-align:left;padding:10px 14px">Malzeme ve işçilik dahil, KDV dahil. Fiyatlar: {{ $estimator->pricesLabel() }}.</caption>
                <thead>
                    <tr><th scope="col">Zemin alanı</th>@foreach ($cfg['qualities'] as $q)<th scope="col">{{ $q['label'] }}</th>@endforeach</tr>
                </thead>
                <tbody>
                    @foreach ($table as $row)
                        <tr>
                            <th scope="row">{{ $row['m2'] }} m²</th>
                            @foreach (array_keys($cfg['qualities']) as $qk)
                                <td>{{ $bin($row[$qk]['low']) }} – {{ $bin($row[$qk]['high']) }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <section class="bt-sec">
        <h2>Banyo tadilat fiyatını en çok ne etkiler?</h2>
        <ul class="bt-factors">
            <li><b>Kapsam</b><span>Tesisatı yerinde bırakan yüzey yenileme, kırım ve tesisat içeren komple yenilemeden yaklaşık üçte bir daha ucuzdur. Klozet veya duşun yerini değiştirmek tesisat kalemini belirgin artırır.</span></li>
            <li><b>Malzeme kalitesi</b><span>Premium seramik ve vitrifiye seçimi, toplam maliyeti orta kaliteye göre yaklaşık %60–65 yükseltebilir; ekonomik seçim ise yaklaşık %20 düşürür.</span></li>
            <li><b>Kaplanacak alan</b><span>Fiyatı zemin alanından çok duvarlarla birlikte kaplanan toplam alan belirler; tavan yüksekliği bu yüzden önemlidir.</span></li>
            <li><b>Sürprizler</b><span>Eski borular, çürük şap veya alt kata sızıntı ancak kırımdan sonra görülür. Bütçenize %10 civarı pay ayırın.</span></li>
        </ul>
    </section>

    <x-sponsor.brand-cta tool="banyo-maliyet-hesaplayici" />

    <section class="bt-sec">
        <h2>İlgili hesaplayıcılar</h2>
        <div class="bt-related">
            <a href="/araclar/seramik-maliyet-hesaplayici" data-ws-internal-link data-ws-target-tool="seramik_v4">Banyo seramik metrajı</a>
            <a href="/araclar/mutfak-maliyet-hesaplayici">Mutfak tadilat maliyeti</a>
            <a href="/araclar/su-yalitimi-hesaplayici">Su yalıtımı maliyeti</a>
            <a href="/araclar/boya-hesaplayici">Boya hesaplayıcı</a>
        </div>
    </section>
</main>

<div class="bt-dock is-hidden" id="bDock" aria-hidden="true">
    <div><small>Tahmini maliyet</small><strong id="bDockRange">Ölçüler bekleniyor</strong></div>
    <button type="button" id="bDockBtn" tabindex="-1">Dökümü gör</button>
</div>

<script type="application/json" id="btCfg">{!! json_encode($jsCfg, $jsonFlags) !!}</script>
<script>
(function () {
    'use strict';
    var C = JSON.parse(document.getElementById('btCfg').textContent);
    var $ = function (id) { return document.getElementById(id); };
    var tl = function (v) { return '₺' + new Intl.NumberFormat('tr-TR', { maximumFractionDigits: 0 }).format(v); };
    var r = function (v, step) { return Math.round(v / step) * step; };
    var clamp = function (v, a, b) { return Math.min(b, Math.max(a, v)); };
    var COLORS = { sokum: '#5B6472', tesisat: '#2F6F8F', yalitim: '#7FB3C8', seramik: '#FFB800', vitrifiye: '#101318', dus: '#8A6200', ekstra: '#C9A55A', beklenmedik: '#D5D9DF' };
    var last = null;

    // PHP App\Services\BathroomEstimator::estimate ile birebir aynı formül
    function estimate(inp) {
        var type = C.types[inp.type] || C.types.standart;
        var scope = C.scopes[inp.scope] ? inp.scope : 'komple';
        var qk = C.qualities[inp.quality] ? inp.quality : 'orta';
        var q = C.qualities[qk].i, u = C.unit;
        var w = clamp(isFinite(inp.w) ? inp.w : type.w, 0.5, 10);
        var l = clamp(isFinite(inp.l) ? inp.l : type.l, 0.5, 10);
        var h = clamp(isFinite(inp.h) ? inp.h : type.h, 2, 4);
        var floor = w * l;
        var wall = Math.max(0, 2 * (w + l) * h - C.door_area);
        var tile = floor + wall;
        var full = scope !== 'yuzey';
        var it = {};
        it.sokum = full ? tile * u.demolition_m2 : u.surface_removal;
        var plumb = 0;
        if (full) {
            plumb = u.plumbing_base + floor * u.plumbing_m2;
            if (scope === 'tasima') plumb = plumb * u.relocation_factor + u.relocation_fixed;
        }
        it.tesisat = plumb;
        it.yalitim = full ? floor * u.waterproof_m2 + (type.shower ? u.waterproof_shower_zone : 0) : 0;
        it.seramik = tile * u.tile_m2[q];
        it.vitrifiye = u.fixtures[q] * type.fixture_factor;
        var sh = C.showers[inp.shower];
        it.dus = type.shower && sh ? sh.price[q] : 0;
        var ex = 0;
        (inp.extras || []).forEach(function (k) {
            var e = C.extras[k]; if (!e) return;
            ex += e.price ? e.price[q] : (e.per_m2 || 0) * floor + (e.fixed || 0);
        });
        it.ekstra = ex;
        var sub = 0;
        Object.keys(it).forEach(function (k) { it[k] = r(it[k], 100); sub += it[k]; });
        it.beklenmedik = r(sub * C.contingency_rate, 100);
        var mid = sub + it.beklenmedik;
        var lines = {};
        Object.keys(it).forEach(function (k) { lines[k] = { low: r(it[k] * C.band.low, 100), mid: it[k], high: r(it[k] * C.band.high, 100) }; });
        return {
            floor: floor, tile_area: tile, items: lines,
            low: r(mid * C.band.low, 1000), mid: r(mid, 1000), high: r(mid * C.band.high, 1000),
            per_m2: floor > 0 ? r(mid / floor, 100) : 0, days: C.scopes[scope].days
        };
    }
    window.wsBathEstimate = estimate; // test için

    function readInput() {
        var extras = Object.keys(C.extras).filter(function (k) { return $(k).checked; });
        return {
            type: $('bathType').value, w: parseFloat(String($('width').value).replace(',', '.')), l: parseFloat(String($('length').value).replace(',', '.')),
            h: parseFloat(String($('height').value).replace(',', '.')) || 2.6,
            scope: $('scope').value, quality: $('quality').value, shower: $('shower').value, extras: extras
        };
    }

    function fxLine(res) {
        var box = $('bFx');
        if (!C.fx || !C.fx.rates) { box.hidden = true; return; }
        var nf = new Intl.NumberFormat('tr-TR', { maximumFractionDigits: 0 });
        var html = '';
        Object.keys(C.symbols).forEach(function (code) {
            var rate = C.fx.rates[code]; if (!rate) return;
            var lo = r(res.low / rate, 10), hi = r(res.high / rate, 10);
            html += '<span>≈ ' + C.symbols[code] + nf.format(lo) + ' – ' + C.symbols[code] + nf.format(hi) + '</span>';
        });
        html += '<span><em>TCMB satış kuru, ' + C.fx.date + '</em></span>';
        box.innerHTML = html; box.hidden = false;
    }

    function render() {
        var inp = readInput();
        var type = C.types[inp.type] || C.types.standart;
        $('showerStep').hidden = !type.shower;
        $('extrasNo').textContent = type.shower ? '6' : '5';
        var nf1 = new Intl.NumberFormat('tr-TR', { maximumFractionDigits: 1 });
        var dimsOk = inp.w >= 0.5 && inp.l >= 0.5;
        var missing = [];
        if (!dimsOk) missing.push('en ve boy ölçüsü');
        if (!C.scopes[inp.scope]) missing.push('kapsam (3. adım)');
        if (!C.qualities[inp.quality]) missing.push('malzeme kalitesi (4. adım)');
        var ready = missing.length === 0;
        $('quoteBtn').disabled = !ready;
        $('proBtn').hidden = !ready;
        if (!ready) {
            last = null;
            var waitTitle = !dimsOk ? 'Ölçüler bekleniyor' : 'Seçim bekleniyor';
            $('bTotal').textContent = waitTitle; $('bTotal').classList.add('is-waiting');
            $('bTyp').textContent = 'Sonuç için eksik: ' + missing.join(', ') + '. Seçtikçe maliyet burada anında hesaplanır.';
            $('bFx').hidden = true; $('bBar').innerHTML = ''; $('bTile').textContent = '—';
            $('bDockRange').textContent = waitTitle;
            if (!dimsOk) $('areaInfo').textContent = 'Banyonun en ve boyunu metre olarak yazın. Duvar yüksekliğini bilmiyorsanız boş bırakın; 2,6 m alınır.';
            $('bLines').querySelectorAll('b').forEach(function (b) { b.textContent = '—'; });
            $('leadMin').value = ''; $('leadMax').value = ''; $('leadMid').value = '';
            if (window.wsBathDock) window.wsBathDock();
            return;
        }
        $('bTotal').classList.remove('is-waiting');
        $('bPdfErr').hidden = true;
        document.querySelectorAll('.bt-step.is-missing').forEach(function (el) { el.classList.remove('is-missing'); });
        ['width', 'length'].forEach(function (id) { $(id).parentNode.classList.remove('is-missing'); });
        var res = estimate(inp); last = res;

        $('bTotal').innerHTML = tl(res.low) + '<span class="sep">–</span>' + tl(res.high);
        $('bTyp').innerHTML = 'En olası tutar <b>' + tl(res.mid) + '</b> · zemin m² başına yaklaşık <b>' + tl(res.per_m2) + '</b>'
            + (type.shower && !C.showers[inp.shower] ? '<br><small>Duş seçilmedi; duş kabini/küvet hesaba katılmadı.</small>' : '');
        $('bDays').textContent = res.days[0] + '–' + res.days[1] + ' iş günü';
        $('bTile').textContent = nf1.format(res.tile_area) + ' m²';
        $('bDockRange').textContent = tl(res.low) + ' – ' + tl(res.high);
        $('areaInfo').innerHTML = 'Zemin <b>' + nf1.format(res.floor) + ' m²</b>, kaplanacak alan (zemin + duvar, kapı payı düşülmüş) <b>' + nf1.format(res.tile_area) + ' m²</b>';
        fxLine(res);

        var total = 0, bar = '', lines = '';
        Object.keys(res.items).forEach(function (k) { total += res.items[k].mid; });
        Object.keys(res.items).forEach(function (k) {
            var it = res.items[k]; if (it.mid <= 0) return;
            var label = k === 'dus' ? (C.showers[inp.shower] || { label: C.labels.dus || 'Duş' }).label : C.labels[k];
            if (k === 'beklenmedik') label += ' (%' + Math.round(C.contingency_rate * 100) + ')';
            bar += '<span style="width:' + (it.mid / total * 100).toFixed(2) + '%;background:' + COLORS[k] + '"></span>';
            lines += '<li><i style="background:' + COLORS[k] + '"></i><span>' + label + '</span><b>' + tl(it.low) + ' – ' + tl(it.high) + '</b></li>';
        });
        $('bBar').innerHTML = bar;
        $('bLines').innerHTML = lines;

        var tLabel = C.types[inp.type] ? C.types[inp.type].label : '';
        $('leadCalculatedData').value = (tLabel + ' ' + nf1.format(res.floor) + 'm2 | ' + C.scopes[inp.scope].label + ' | ' + C.qualities[inp.quality].label).slice(0, 100);
        $('leadMin').value = res.low; $('leadMax').value = res.high; $('leadMid').value = res.mid;
        if (window.wsBathDock) window.wsBathDock();
    }

    // Seçim kartları ↔ gizli alanlar (paylaş linki gizli alanları doldurur)
    document.querySelectorAll('[data-group]').forEach(function (g) {
        var key = g.getAttribute('data-group'), hidden = $(key);
        g.addEventListener('change', function (e) {
            if (e.target.name !== 'r_' + key) return;
            hidden.value = e.target.value;
            if (key === 'bathType') {
                // Ölçü otomatik doldurulmaz; yalnızca tipik ölçü örnek olarak gösterilir
                var t = C.types[hidden.value], f = function (v) { return String(v).replace('.', ','); };
                $('width').placeholder = 'Örn. ' + f(t.w); $('length').placeholder = 'Örn. ' + f(t.l);
            }
            render();
        });
        hidden.addEventListener('change', function () {
            var radio = g.querySelector('input[value="' + hidden.value + '"]');
            if (radio) radio.checked = true;
            render();
        });
    });
    ['width', 'length', 'height'].forEach(function (id) { $(id).addEventListener('input', render); });
    Object.keys(C.extras).forEach(function (k) { $(k).addEventListener('change', render); });

    function showConfirm() {
        var b = $('budgetConfirmBox');
        if ($('leadBox').style.display !== 'block') b.style.display = 'block';
    }
    function needDims() {
        if (last) return false;
        var miss = ['width', 'length'].filter(function (id) { return !(parseFloat(String($(id).value).replace(',', '.')) >= 0.5); });
        miss.forEach(function (id) { $(id).parentNode.classList.add('is-missing'); });
        if (miss.length) { $(miss[0]).focus(); $(miss[0]).scrollIntoView({ behavior: 'smooth', block: 'center' }); return true; }
        var step = null;
        ['scope', 'quality'].forEach(function (key) {
            if (!$(key).value) {
                var g = document.querySelector('[data-group="' + key + '"]').closest('.bt-step');
                g.classList.add('is-missing'); step = step || g;
            }
        });
        if (step) step.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return true;
    }
    $('calculateBtn').addEventListener('click', function () {
        render(); if (needDims()) return; showConfirm();
        $('resultCard').scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
    $('quoteBtn').addEventListener('click', function () {
        showConfirm();
        $('budgetConfirmBox').scrollIntoView({ behavior: 'smooth', block: 'center' });
    });
    // "PDF rapor indir": ölçü girilmeden basılırsa sunucuya gitmeden ölçülerin yanında uyarı (yakalama aşamasında, ortak araç çubuğundan önce)
    document.addEventListener('click', function (ev) {
        var b = ev.target && ev.target.closest ? ev.target.closest('[data-ws-print]') : null;
        if (!b || !$('bTotal').classList.contains('is-waiting')) return;
        ev.preventDefault(); ev.stopImmediatePropagation();
        var box = $('bPdfErr');
        box.textContent = 'PDF rapor için önce banyonun en ve boy ölçülerini girin ve seçimleri tamamlayın.';
        box.hidden = false;
        box.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }, true);
    // Pro rapor ve planlayıcı bağlantıları: güncel girdileri (paylaş linkiyle aynı alanlar) taşır
    ['proBtn', 'planBtn'].forEach(function (btnId) {
        var pro = document.getElementById(btnId);
        if (!pro) return;
        pro.addEventListener('click', function () {
            var cfgEl = document.getElementById('ws-tool-guide-config'), fields = [];
            try { fields = JSON.parse(cfgEl.textContent).fields || []; } catch (e) {}
            var q = new URLSearchParams();
            fields.forEach(function (id) {
                var el = document.getElementById(id);
                if (!el) return;
                if (el.type === 'checkbox') { if (el.checked) q.set(id, '1'); } else if (el.value !== '') { q.set(id, el.value); }
            });
            pro.href = pro.href.split('?')[0] + '?' + q.toString();
        });
    });
    // Planlayıcıdan gelindiyse: seçilen sponsor ürünlerinin marka fiyatları bilgi olarak gösterilir
    (function () {
        var q = new URLSearchParams(location.search), lo = parseInt(q.get('plan_low'), 10), hi = parseInt(q.get('plan_high'), 10), n = parseInt(q.get('plan_n'), 10);
        var box = document.getElementById('bPlan');
        if (!box || !(lo > 0) || !(hi >= lo) || hi > 5000000) return;
        box.innerHTML = '<b>Planınızdaki marka ürünleri: ' + tl(lo) + ' – ' + tl(hi) + '</b>'
            + (n > 0 ? n + ' ürün, ' : '') + 'markaların bildirdiği fiyatlar. Yukarıdaki dökümde vitrifiye ve duş kalemleri ortalama fiyatla hesaplanır; seçtiğiniz ürünlerle kesin tutar yerinde keşifte netleşir.';
        box.hidden = false;
    })();
    // Planlayıcıdan "Bu plan için teklif iste" ile gelindiyse plan bağlantısı nota eklenir
    (function () {
        var plan = null;
        try { plan = sessionStorage.getItem('ws_bath_plan'); } catch (e) {}
        var notes = document.getElementById('ld_notes');
        if (!plan || !notes || notes.value) return;
        if (!/^https?:\/\/[^\s]+\/araclar\/banyo-planlayici/.test(plan)) return;
        notes.value = 'Banyo planım: ' + plan;
        try { sessionStorage.removeItem('ws_bath_plan'); } catch (e) {}
        if (location.hash === '#teklif') { setTimeout(function () { showConfirm(); }, 50); }
    })();
    $('bDockBtn').addEventListener('click', function () { $('resultCard').scrollIntoView({ behavior: 'smooth', block: 'start' }); });

    $('btnYes').addEventListener('click', function () {
        $('budgetConfirmBox').style.display = 'none';
        $('qualifiedMessage').style.display = 'block';
        $('leadBox').style.display = 'block';
        setTimeout(function () { $('qualifiedMessage').scrollIntoView({ behavior: 'smooth', block: 'center' }); }, 100);
    });
    $('btnNo').addEventListener('click', function () {
        $('budgetConfirmBox').innerHTML = '<div class="budget-confirm-title">Sorun değil.</div><div class="budget-confirm-text" style="margin-bottom:0">Bu aşamada iletişim bilgilerinizi almıyoruz. Bütçeniz netleştiğinde tekrar teklif isteyebilirsiniz.</div>';
        $('leadBox').style.display = 'none';
        $('qualifiedMessage').style.display = 'none';
    });

    // Mobil sabit çubuk: sonuç kartı veya teklif alanı görünürken gizlenir
    if ('IntersectionObserver' in window) {
        var dock = $('bDock'), seen = new Set();
        window.wsBathDock = function () { dock.classList.toggle('is-hidden', seen.size > 0 || !last); };
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (e) { e.isIntersecting ? seen.add(e.target) : seen.delete(e.target); });
            window.wsBathDock();
        }, { threshold: 0.15 });
        [$('resultCard'), document.querySelector('.bt-lead-area')].forEach(function (el) { io.observe(el); });
    }

    render();
})();
</script>

<x-tools.tool-guide slug="banyo-maliyet-hesaplayici" />

<x-site.contech-footer />
<script src="/js/tool-events.js" defer></script>
<script src="/js/p1-product-events.js" defer></script>
</body>
</html>
