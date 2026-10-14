{{--
    Worksitem yasal belge şablonu (sözleşme / politika / aydınlatma metni).
    Tüm /legal/* sayfaları bu bileşeni kullanır: logolu belge başlığı, sürüm künyesi,
    içindekiler, yasal merkez menüsü, hizmet sağlayıcı künyesi ve yazdırma/PDF görünümü.
--}}
@props([
    'title',
    'description' => '',
    'path',
    'type' => 'Yasal metin',
    'version' => null,
    'effective' => null,
    'robots' => 'index, follow',
    'docNo' => null,
    'pdf' => null,
])
@php
    $html = (string) $slot;
    $toc = [];
    $html = preg_replace_callback('#<h2([^>]*)>(.*?)</h2>#su', function ($m) use (&$toc) {
        $text = trim(strip_tags($m[2]));
        $id = \Illuminate\Support\Str::slug(preg_replace('/^[\d.\s—-]+/u', '', $text) ?: $text) ?: 'b'.count($toc);
        $toc[] = ['id' => $id, 'text' => $text];
        return '<h2 id="'.$id.'"'.$m[1].'>'.$m[2].'</h2>';
    }, $html);

    $docs = [
        '/legal/kullanim-kosullari' => ['Kullanım Koşulları', 'Sözleşme'],
        '/legal/kesif-on-bilgilendirme-formu' => ['Keşif Ön Bilgilendirme Formu', 'Mesafeli satış'],
        '/legal/kesif-mesafeli-hizmet-sozlesmesi' => ['Keşif Mesafeli Hizmet Sözleşmesi', 'Mesafeli satış'],
        '/legal/kvkk-aydinlatma' => ['KVKK Aydınlatma Metni', 'KVKK'],
        '/legal/acik-riza' => ['Açık Rıza Metni', 'KVKK'],
        '/legal/kvkk-basvuru' => ['KVKK Başvuru Formu', 'KVKK'],
        '/legal/gizlilik-politikasi' => ['Gizlilik Politikası', 'Politika'],
        '/legal/cerez-politikasi' => ['Çerez Politikası', 'Politika'],
    ];
    $co = config('worksitem_company');
    $identity = array_filter([
        'Hizmet sağlayıcı' => $co['legal_name'] ?? null,
        'MERSİS no' => $co['mersis'] ?? null,
        'Vergi dairesi / no' => trim(($co['tax_office'] ?? '').' / '.($co['tax_no'] ?? ''), ' /') ?: null,
        'Adres' => $co['address'] ?? null,
        'Telefon' => $co['phone'] ?? null,
        'E-posta' => $co['email'] ?? null,
        'KEP' => $co['kep'] ?? null,
    ]);
    $docNo = $docNo ?: 'WS-'.strtoupper(\Illuminate\Support\Str::of($path)->afterLast('/')->explode('-')->map(fn ($p) => mb_substr($p, 0, 1))->implode('')).'-'.str_replace(['-', '.'], '', (string) $version);
@endphp
<!DOCTYPE html>
<html lang="tr">
<head>
<script id="worksitem-legal-theme-init">
(function(){var t=null;try{t=localStorage.getItem("worksitem-theme");}catch(e){}
if(t!=="light"&&t!=="dark"){t=window.matchMedia&&window.matchMedia("(prefers-color-scheme: dark)").matches?"dark":"light";}
document.documentElement.setAttribute("data-theme",t);})();
</script>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $title }} | Worksitem</title>
@if ($description)<meta name="description" content="{{ $description }}">@endif
<meta name="robots" content="{{ $robots }}">
<link rel="canonical" href="{{ url($path) }}">
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<script type="application/ld+json">{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'WebPage',
    'name' => $title,
    'description' => $description,
    'url' => url($path),
    'inLanguage' => 'tr-TR',
    'dateModified' => $effective ? \Illuminate\Support\Carbon::createFromFormat('d.m.Y', $effective)->toDateString() : null,
    'publisher' => ['@type' => 'Organization', 'name' => $co['legal_name'] ?? 'Kainta İnşaat', 'url' => url('/')],
    'breadcrumb' => ['@type' => 'BreadcrumbList', 'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Worksitem', 'item' => url('/')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Yasal merkez', 'item' => url('/legal/kullanim-kosullari')],
        ['@type' => 'ListItem', 'position' => 3, 'name' => $title, 'item' => url($path)],
    ]],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
<style>
:root{--ink:#101318;--ink-2:#1B2029;--y:#FFB800;--y-50:#FFF8E1;--muted:#5B6472;--line:#E3E6EA;--bg:#F4F5F7;--paper:#fff;--txt:#1F2430}
html[data-theme="dark"]{--bg:#0B0F17;--paper:#141A24;--line:#232C3A;--txt:#D9DEE6;--muted:#97A1AF;--y-50:#2A2410}
*{box-sizing:border-box}
body{margin:0;background:var(--bg);color:var(--txt);font-family:Inter,-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;line-height:1.7;-webkit-font-smoothing:antialiased}
.lg-hero{background:var(--ink);color:#fff;position:relative;overflow:hidden}
.lg-hero:before{content:"";position:absolute;inset:0;background-image:linear-gradient(rgba(255,255,255,.05) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.05) 1px,transparent 1px);background-size:32px 32px;mask-image:linear-gradient(90deg,transparent,#000 60%)}
.lg-hero-in{position:relative;max-width:1180px;margin:0 auto;padding:34px 20px 30px}
.lg-crumb{font-size:13px;color:#AAB1BC;margin:0 0 14px}
.lg-crumb a{color:#DFE3E8;text-decoration:none}
.lg-crumb a:hover{color:var(--y)}
.lg-chip{display:inline-flex;align-items:center;gap:6px;background:var(--y);color:var(--ink);font-size:12px;font-weight:800;padding:4px 10px;border-radius:999px;letter-spacing:.02em;text-transform:uppercase}
.lg-hero h1{font-size:clamp(28px,4vw,42px);line-height:1.15;margin:12px 0 10px;letter-spacing:-.02em;font-weight:900;color:#fff}
.lg-desc{color:#C9CED6;max-width:720px;margin:0 0 18px;font-size:15.5px}
.lg-meta{display:flex;flex-wrap:wrap;gap:8px;margin:0 0 18px;padding:0;list-style:none}
.lg-meta li{background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.12);border-radius:10px;padding:6px 12px;font-size:13px;color:#DFE3E8}
.lg-meta b{color:#fff;font-weight:700}
.lg-actions{display:flex;flex-wrap:wrap;gap:10px}
.lg-btn{display:inline-flex;align-items:center;gap:8px;border-radius:10px;padding:10px 16px;font:700 14px Inter,sans-serif;text-decoration:none;cursor:pointer;border:1px solid transparent}
.lg-btn-y{background:var(--y);color:var(--ink)}
.lg-btn-y:hover{background:#E5A500}
.lg-btn-g{background:transparent;color:#fff;border-color:rgba(255,255,255,.28)}
.lg-btn-g:hover{border-color:var(--y);color:var(--y)}
.lg-wrap{max-width:1180px;margin:0 auto;padding:28px 20px 56px;display:grid;grid-template-columns:270px minmax(0,1fr);gap:28px;align-items:start}
.lg-aside{position:sticky;top:84px;display:flex;flex-direction:column;gap:16px}
.lg-card{background:var(--paper);border:1px solid var(--line);border-radius:14px;padding:16px}
.lg-card h3{margin:0 0 8px;font-size:12px;letter-spacing:.06em;text-transform:uppercase;color:var(--muted);font-weight:800}
.lg-toc{list-style:none;margin:0;padding:0;max-height:46vh;overflow:auto}
.lg-toc a{display:block;padding:5px 8px;border-radius:8px;color:var(--txt);text-decoration:none;font-size:13.5px;line-height:1.4;border-left:2px solid transparent}
.lg-toc a:hover,.lg-toc a.on{background:var(--y-50);border-left-color:var(--y)}
.lg-docs{list-style:none;margin:0;padding:0}
.lg-docs a{display:flex;justify-content:space-between;gap:8px;padding:7px 8px;border-radius:8px;color:var(--txt);text-decoration:none;font-size:13.5px;line-height:1.35}
.lg-docs a:hover{background:var(--bg)}
.lg-docs a[aria-current]{background:var(--ink);color:#fff}
.lg-docs small{color:var(--muted);font-size:11px;white-space:nowrap}
.lg-docs a[aria-current] small{color:var(--y)}
.lg-help p{margin:0 0 6px;font-size:13.5px}
.lg-help a{color:var(--txt);font-weight:700}
.lg-paper{background:var(--paper);border:1px solid var(--line);border-radius:18px;box-shadow:0 18px 40px rgba(16,19,24,.06);overflow:hidden}
.lg-paper-top{display:flex;justify-content:space-between;align-items:center;gap:12px;padding:16px 28px;border-bottom:1px solid var(--line);background:linear-gradient(90deg,var(--y) 0 6px,transparent 6px)}
.lg-paper-top img{height:30px;width:auto}
html[data-theme="dark"] .lg-paper-top img{background:#fff;border-radius:8px;padding:3px 6px}
.lg-paper-top span{font-size:12px;color:var(--muted);text-align:right;line-height:1.4}
.lg-body{padding:8px 36px 32px;font-size:15.5px}
.lg-body h2{font-size:19px;line-height:1.35;margin:30px 0 10px;padding-top:6px;color:var(--ink);font-weight:800;scroll-margin-top:90px}
html[data-theme="dark"] .lg-body h2,html[data-theme="dark"] .lg-body h3,html[data-theme="dark"] .lg-body strong{color:#fff}
.lg-body h3{font-size:16px;margin:20px 0 6px;color:var(--ink)}
.lg-body p,.lg-body li{color:var(--txt)}
.lg-body ul,.lg-body ol{padding-left:22px}
.lg-body li{margin-bottom:6px}
.lg-body a{color:var(--ink);text-decoration:underline;text-decoration-color:var(--y);text-decoration-thickness:2px;text-underline-offset:3px}
html[data-theme="dark"] .lg-body a{color:#FFC94B}
.lg-body table{width:100%;border-collapse:collapse;margin:14px 0;font-size:14px;border:1px solid var(--line);border-radius:10px;overflow:hidden;display:block;overflow-x:auto}
.lg-body th,.lg-body td{padding:10px 12px;text-align:left;border-bottom:1px solid var(--line);vertical-align:top}
.lg-body th{background:var(--bg);font-weight:700;white-space:nowrap}
.lg-body .summary,.lg-body .info-box,.lg-body .cookie-manage,.lg-body .lg-note{padding:14px 16px;border-left:4px solid var(--y);background:var(--y-50);border-radius:10px;margin:16px 0}
.lg-body .lg-warn{padding:14px 16px;border-left:4px solid #DC2626;background:#FEF2F2;border-radius:10px;margin:16px 0;color:#7F1D1D}
html[data-theme="dark"] .lg-body .lg-warn{background:#2A1414;color:#FCA5A5}
.lg-body .cookie-manage button{background:var(--ink);color:#fff;padding:10px 18px;border:0;border-radius:10px;font:700 14px Inter,sans-serif;cursor:pointer}
.lg-body .version{display:none}
.lg-field{display:inline-block;min-width:120px;padding:0 6px;border-bottom:1.5px dashed var(--muted);color:var(--muted);font-style:italic}
.lg-filled{font-weight:700;color:var(--ink)}
html[data-theme="dark"] .lg-filled{color:#fff}
.lg-kv{width:100%}
.lg-kv td:first-child{width:36%;color:var(--muted);font-weight:600}
.lg-sign{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:12px;margin:24px 0 0}
.lg-sign div{border:1px dashed var(--line);border-radius:12px;padding:14px;font-size:14px}
.lg-sign b{display:block;font-size:12px;color:var(--muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px}
.lg-id{margin:0;padding:22px 36px 28px;border-top:1px solid var(--line);background:var(--bg)}
.lg-id h2{margin:0 0 10px;font-size:13px;letter-spacing:.06em;text-transform:uppercase;color:var(--muted)}
.lg-id dl{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:10px 24px;margin:0}
.lg-id dt{font-size:12px;color:var(--muted);font-weight:600}
.lg-id dd{margin:0;font-size:14px;font-weight:600}
.lg-print-head,.lg-print-foot{display:none}
.lg-toast{position:fixed;left:50%;bottom:24px;transform:translateX(-50%);background:var(--ink);color:#fff;padding:10px 16px;border-radius:10px;font-size:14px;opacity:0;transition:.2s;pointer-events:none;z-index:50}
.lg-toast.on{opacity:1}
.lg-toc-m{display:none}
@media (max-width:960px){
  .lg-wrap{grid-template-columns:1fr;padding:18px 16px 40px}
  .lg-aside{position:static;order:2}
  .lg-toc-m{display:block;margin:0 0 14px}
  .lg-toc-m summary{cursor:pointer;font-weight:700;padding:12px 16px;background:var(--paper);border:1px solid var(--line);border-radius:12px}
  .lg-toc-m .lg-toc{max-height:none;padding:8px;background:var(--paper);border:1px solid var(--line);border-top:0;border-radius:0 0 12px 12px}
  .lg-aside .lg-toc-card{display:none}
  .lg-body{padding:4px 18px 24px;font-size:15px}
  .lg-paper-top,.lg-id{padding-left:18px;padding-right:18px}
}
@media print{
  @page{margin:16mm 14mm 18mm}
  body{background:#fff;color:#111}
  .wks-header,.wks-footer,.lg-hero,.lg-aside,.lg-toc-m,#ws-legal-notice,.cookie-manage button,.lg-toast,#wsCookieBanner,[data-cookie-banner]{display:none!important}
  .lg-wrap{display:block;padding:0;max-width:none}
  .lg-paper{border:0;box-shadow:none;border-radius:0}
  .lg-paper-top{padding:0 0 10px;background:none;border-bottom:2px solid #111}
  .lg-print-head{display:block;margin:14px 0 4px}
  .lg-print-head .lg-print-title{font-size:22px;font-weight:800;margin:0 0 4px;color:#101318}
  .lg-print-head p{margin:0;font-size:11px;color:#555}
  .lg-body{padding:0;font-size:11.5pt}
  .lg-body h2{break-after:avoid;font-size:14pt}
  .lg-body a{color:#111;text-decoration:none}
  .lg-id{background:none;padding:12px 0 0;break-inside:avoid}
  .lg-print-foot{display:block;position:fixed;bottom:-12mm;left:0;right:0;font-size:9px;color:#777;border-top:1px solid #ccc;padding-top:4px}
}
</style>
@stack('legal-head')
</head>
<body>
<x-site.contech-header />

<section class="lg-hero" data-kainta-auto-link="off">
    <div class="lg-hero-in">
        <p class="lg-crumb"><a href="/">Worksitem</a> › <a href="/legal/kullanim-kosullari">Yasal merkez</a> › {{ $title }}</p>
        <span class="lg-chip">{{ $type }}</span>
        <h1>{{ $title }}</h1>
        @if ($description)<p class="lg-desc">{{ $description }}</p>@endif
        <ul class="lg-meta">
            @if ($version)<li>Sürüm <b>{{ $version }}</b></li>@endif
            @if ($effective)<li>Yürürlük <b>{{ $effective }}</b></li>@endif
            <li>Belge no <b>{{ $docNo }}</b></li>
            <li>Hazırlayan <b>{{ $co['legal_name'] ?? 'Kainta İnşaat' }}</b></li>
            @if (config('worksitem_legal.approved_at'))<li>Hukuki inceleme <b>✓ {{ config('worksitem_legal.approved_at') }}</b></li>@endif
        </ul>
        <div class="lg-actions">
            @if ($pdf)
                <a class="lg-btn lg-btn-y" href="{{ $pdf }}">📄 PDF indir</a>
                <button type="button" class="lg-btn lg-btn-g" data-lg-print>🖨️ Yazdır</button>
            @else
                <button type="button" class="lg-btn lg-btn-y" data-lg-print>📄 PDF olarak kaydet / yazdır</button>
            @endif
            <button type="button" class="lg-btn lg-btn-g" data-lg-copy>🔗 Bağlantıyı kopyala</button>
        </div>
    </div>
</section>

<div class="lg-wrap">
    <aside class="lg-aside" aria-label="Belge menüsü" data-kainta-auto-link="off">
        @if (count($toc) > 1)
        <nav class="lg-card lg-toc-card" aria-label="İçindekiler">
            <h3>Bu belgede</h3>
            <ol class="lg-toc">@foreach ($toc as $t)<li><a href="#{{ $t['id'] }}">{{ $t['text'] }}</a></li>@endforeach</ol>
        </nav>
        @endif
        <nav class="lg-card" aria-label="Yasal merkez">
            <h3>Yasal merkez</h3>
            <ul class="lg-docs">
                @foreach ($docs as $href => [$name, $kind])
                    <li><a href="{{ $href }}" @if ($href === $path) aria-current="page" @endif>{{ $name }} <small>{{ $kind }}</small></a></li>
                @endforeach
            </ul>
        </nav>
        <div class="lg-card lg-help">
            <h3>Sorunuz mu var?</h3>
            <p>{{ config('worksitem_inspection.call_center_label', 'Çağrı merkezi') }}</p>
            <p><a href="tel:{{ preg_replace('/[^\d+]/', '', $co['phone'] ?? '') }}">{{ $co['phone'] ?? '' }}</a></p>
            <p><a href="mailto:{{ $co['email'] ?? 'info@worksitem.com' }}">{{ $co['email'] ?? 'info@worksitem.com' }}</a></p>
        </div>
    </aside>

    <div>
        @if (count($toc) > 1)
        <details class="lg-toc-m"><summary>İçindekiler ({{ count($toc) }} bölüm)</summary>
            <ol class="lg-toc">@foreach ($toc as $t)<li><a href="#{{ $t['id'] }}">{{ $t['text'] }}</a></li>@endforeach</ol>
        </details>
        @endif
        <article class="lg-paper" data-kainta-auto-link="off">
            <div class="lg-paper-top">
                <img src="{{ asset('brand/worksitem-logo.png') }}" alt="Worksitem" width="140" height="30">
                <span>{{ $type }} · {{ $docNo }}@if ($version)<br>Sürüm {{ $version }}@endif</span>
            </div>
            <div class="lg-print-head">
                <p class="lg-print-title">{{ $title }}</p>
                <p>@if ($version)Sürüm {{ $version }} · @endif @if ($effective)Yürürlük {{ $effective }} · @endif{{ url($path) }}</p>
            </div>
            <div class="lg-body">
                {!! $html !!}
            </div>
            @if ($identity)
            <section class="lg-id" aria-label="Hizmet sağlayıcı künyesi">
                <h2>Künye</h2>
                <dl>@foreach ($identity as $k => $v)<div><dt>{{ $k }}</dt><dd>{{ $v }}</dd></div>@endforeach</dl>
            </section>
            @endif
        </article>
    </div>
</div>
<div class="lg-print-foot">worksitem.com · {{ $title }} · {{ $docNo }} · Bu belge elektronik ortamda yayımlanmıştır.</div>
<div class="lg-toast" role="status" aria-live="polite" data-lg-toast></div>

<x-site.contech-footer />
<script>
(function () {
    var toast = document.querySelector('[data-lg-toast]');
    function say(t) { toast.textContent = t; toast.classList.add('on'); clearTimeout(say.t); say.t = setTimeout(function () { toast.classList.remove('on'); }, 2400); }
    document.querySelectorAll('[data-lg-print]').forEach(function (b) { b.addEventListener('click', function () { window.print(); }); });
    var c = document.querySelector('[data-lg-copy]');
    if (c) c.addEventListener('click', function () {
        var u = location.href.split('#')[0];
        (navigator.clipboard && window.isSecureContext ? navigator.clipboard.writeText(u) : Promise.reject()).then(
            function () { say('✓ Bağlantı kopyalandı'); }, function () { window.prompt('Bağlantıyı kopyalayın:', u); });
    });
    // İçindekiler: okunan bölümü işaretle
    var links = document.querySelectorAll('.lg-toc-card .lg-toc a');
    if ('IntersectionObserver' in window && links.length) {
        var map = {};
        links.forEach(function (a) { map[a.getAttribute('href').slice(1)] = a; });
        var io = new IntersectionObserver(function (es) {
            es.forEach(function (e) {
                if (!e.isIntersecting || !map[e.target.id]) return;
                links.forEach(function (a) { a.classList.remove('on'); });
                map[e.target.id].classList.add('on');
            });
        }, { rootMargin: '-20% 0px -70% 0px' });
        document.querySelectorAll('.lg-body h2[id]').forEach(function (h) { io.observe(h); });
    }
})();
</script>
</body>
</html>
