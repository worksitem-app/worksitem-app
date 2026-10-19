@php
    $year = now()->year;
    $url = 'https://worksitem.com/araclar/ges-planlayici';
    $title = \App\Support\ToolSeo::title("GES Planlayıcı $year: Adresten Çatıya Güneş Paneli Yerleşimi (2D / 3D)");
    $desc = \App\Support\ToolSeo::desc('Adresinizi yazın; çatınız uydu haritasında gelsin, güneş panelleri otomatik dizilsin. 2D ve 3D görün; yıllık üretim (PVGIS), kurulu güç, maliyet ve geri dönüş süresini ev ve fabrika için ücretsiz hesaplayın.');
    $jsonFlags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP;
    $importMap = ['imports' => ['three' => $three['module'], 'three/addons/' => $three['addons']]];
    $C = [
        'geo' => route('tools.solar_planner.geocode'), 'bld' => route('tools.solar_planner.building'), 'calc' => route('tools.solar_planner.calc'),
        'panels' => $cfg['panels'], 'panel' => $cfg['default_panel'], 'setback' => $cfg['setback'], 'flatTilt' => $cfg['flat_tilt'],
        'imagery' => $cfg['imagery'], 'kwhPrice' => $kwhPrice, 'quote' => url('/araclar/gunes-paneli-hesaplama'),
    ];
@endphp
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }}</title>
    <meta name="description" content="{{ $desc }}">
    <link rel="canonical" href="{{ $url }}">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $desc }}">
    <meta property="og:url" content="{{ $url }}">
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ $cfg['leaflet']['css'] }}">
    <script type="application/ld+json">{!! json_encode(['@context' => 'https://schema.org', '@type' => 'WebApplication', 'name' => 'GES Planlayıcı', 'url' => $url, 'applicationCategory' => 'BusinessApplication', 'operatingSystem' => 'Web', 'inLanguage' => 'tr-TR', 'description' => $desc, 'offers' => ['@type' => 'Offer', 'price' => '0', 'priceCurrency' => 'TRY']], $jsonFlags) !!}</script>
    <script type="application/ld+json">{!! json_encode(['@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Ana sayfa', 'item' => 'https://worksitem.com/'],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Araçlar', 'item' => 'https://worksitem.com/araclar'],
        ['@type' => 'ListItem', 'position' => 3, 'name' => 'GES Planlayıcı', 'item' => $url]]], $jsonFlags) !!}</script>
    <script type="importmap">{!! json_encode($importMap, $jsonFlags) !!}</script>
    <style>
        :root{--ink:#101318;--y:#FFB800;--y50:#FFF8E1;--page:#F5F6F8;--line:#E3E6EA;--muted:#5B6472;--navy:#0F2747;--ok:#15803D;--r:16px}
        *{box-sizing:border-box}
        [hidden]{display:none!important}
        body{margin:0;font-family:Inter,system-ui,-apple-system,'Segoe UI',sans-serif;background:var(--page);color:var(--ink);line-height:1.5}
        button,input,select{font:inherit;color:inherit}
        :focus-visible{outline:3px solid var(--y);outline-offset:2px}
        .gp-wrap{max-width:1360px;margin:0 auto;padding:18px 16px 40px}
        .gp-crumb{font-size:13px;color:var(--muted)} .gp-crumb a{color:inherit}
        .gp-wrap h1{font-size:clamp(24px,3vw,34px);letter-spacing:-.02em;margin:6px 0 6px;line-height:1.15}
        .gp-lede{color:var(--muted);max-width:860px;margin:0 0 16px}
        .gp-grid{display:grid;grid-template-columns:380px 1fr;gap:16px;align-items:start}
        .gp-side{display:flex;flex-direction:column;gap:12px}
        .gp-card{background:#fff;border:1px solid var(--line);border-radius:var(--r);padding:16px}
        .gp-card h2{font-size:15px;margin:0 0 10px;display:flex;gap:8px;align-items:center}
        .gp-card h2 i{font-style:normal;width:24px;height:24px;border-radius:8px;background:var(--y);display:inline-flex;align-items:center;justify-content:center;font-size:13px;font-weight:900}
        .gp-row{display:flex;gap:8px}
        .gp-in{width:100%;border:1.5px solid var(--line);border-radius:12px;padding:10px 12px;background:#fff}
        .gp-in:focus{border-color:var(--y);outline:none}
        .gp-btn{border:0;border-radius:12px;padding:10px 14px;font-weight:800;cursor:pointer;background:var(--y);color:var(--ink);white-space:nowrap}
        .gp-btn.o{background:#fff;border:1.5px solid var(--line)}
        .gp-btn.s{padding:7px 10px;font-size:13px}
        .gp-btn:disabled{opacity:.5;cursor:not-allowed}
        .gp-res{list-style:none;margin:8px 0 0;padding:0;max-height:180px;overflow:auto}
        .gp-res li button{width:100%;text-align:left;background:none;border:0;border-bottom:1px solid var(--line);padding:8px 4px;font-size:13.5px;cursor:pointer}
        .gp-res li button:hover{background:var(--y50)}
        .gp-msg{font-size:13.5px;color:var(--muted);margin:8px 0 0}
        .gp-msg.warn{color:#8A4B00;background:var(--y50);border-radius:10px;padding:8px 10px}
        .gp-seg{display:grid;grid-template-columns:repeat(2,1fr);gap:6px}
        .gp-seg.c4{grid-template-columns:repeat(4,1fr)}
        .gp-seg input{position:absolute;opacity:0;pointer-events:none}
        .gp-seg label{border:1.5px solid var(--line);border-radius:12px;padding:8px 6px;text-align:center;cursor:pointer;font-size:13px;font-weight:700;line-height:1.25}
        .gp-seg label small{display:block;font-weight:500;color:var(--muted);font-size:11.5px}
        .gp-seg input:checked+label{border-color:var(--ink);background:var(--y50)}
        .gp-f{display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:10px}
        .gp-f label{font-size:12.5px;font-weight:700;display:block;margin-bottom:3px}
        .gp-f .full{grid-column:1/-1}
        .gp-check{display:flex;gap:8px;align-items:center;font-size:13.5px;margin-top:8px}
        .gp-faces{list-style:none;margin:0;padding:0;display:grid;gap:6px}
        .gp-faces li{display:grid;grid-template-columns:auto 1fr auto;gap:8px;align-items:center;border:1px solid var(--line);border-radius:12px;padding:8px 10px;font-size:13px}
        .gp-faces li b{display:block;font-size:13.5px}
        .gp-faces li span{color:var(--muted)}
        .gp-faces li em{font-style:normal;font-weight:800;white-space:nowrap}
        .gp-dot{width:12px;height:12px;border-radius:4px;display:inline-block;vertical-align:-1px;margin-right:4px}
        .gp-main{position:sticky;top:12px}
        .gp-view{background:#fff;border:1px solid var(--line);border-radius:var(--r);overflow:hidden}
        .gp-tabs{display:flex;gap:4px;padding:8px;border-bottom:1px solid var(--line);align-items:center;flex-wrap:wrap}
        .gp-tab{border:0;background:none;padding:8px 14px;border-radius:10px;font-weight:800;cursor:pointer;font-size:14px}
        .gp-tab[aria-selected=true]{background:var(--navy);color:#fff}
        .gp-kpi{margin-left:auto;font-size:13px;color:var(--muted)} .gp-kpi b{color:var(--ink)}
        #gpMap,#gp3d{height:min(72vh,680px);width:100%}
        #gp3d{position:relative;background:#cfd8e3}
        #gp3d canvas{display:block}
        .gp-lab{font:700 11px/1 Inter,sans-serif;color:#fff;background:rgba(16,19,24,.78);padding:3px 6px;border-radius:6px;white-space:nowrap;pointer-events:none}
        .leaflet-tooltip.gp-len{background:rgba(16,19,24,.8);color:#fff;border:0;box-shadow:none;font:700 11px Inter,sans-serif;padding:2px 6px}
        .leaflet-tooltip.gp-len:before{display:none}
        .gp-legend{position:absolute;left:10px;bottom:10px;z-index:500;background:rgba(15,39,71,.9);color:#fff;border-radius:10px;padding:8px 10px;font-size:11.5px}
        .gp-legend i{display:block;width:150px;height:8px;border-radius:4px;background:linear-gradient(90deg,#8B5CF6,#EF4444,#F59E0B,#FDE047);margin:4px 0}
        .gp-legend span{display:flex;justify-content:space-between;opacity:.8}
        .gp-draw{position:absolute;right:10px;top:10px;z-index:600;display:flex;gap:6px;flex-wrap:wrap;justify-content:flex-end}
        .gp-sum{display:grid;grid-template-columns:repeat(2,1fr);gap:8px;margin:0 0 10px}
        .gp-sum div{background:var(--page);border-radius:12px;padding:10px}
        .gp-sum small{display:block;color:var(--muted);font-size:12px}
        .gp-sum b{font-size:18px}
        .gp-big{background:var(--ink);color:#fff;border-radius:14px;padding:14px;margin-bottom:10px}
        .gp-big small{color:#C9CED6} .gp-big b{display:block;font-size:24px;color:var(--y)}
        .gp-items{list-style:none;margin:0 0 10px;padding:0;font-size:13.5px}
        .gp-items li{display:flex;justify-content:space-between;gap:10px;border-bottom:1px dashed var(--line);padding:5px 0}
        .gp-note{font-size:12.5px;color:var(--muted);margin:8px 0 0}
        .gp-legal{background:#EEF4FF;border:1px solid #C7D7FE;border-radius:12px;padding:12px;font-size:13px;margin-top:10px}
        .gp-legal b{display:block;margin-bottom:4px}
        .gp-legal ol{margin:4px 0 0;padding-left:18px}
        .gp-chart{width:100%;height:120px;display:block}
        .gp-ctas{display:flex;gap:8px;flex-wrap:wrap;margin-top:10px}
        .gp-ctas a{text-decoration:none;text-align:center;flex:1}
        .gp-info{margin-top:22px;display:grid;grid-template-columns:repeat(3,1fr);gap:12px}
        .gp-info h3{font-size:15px;margin:0 0 6px}
        .gp-info p{margin:0;font-size:13.5px;color:var(--muted)}
        @media (max-width:980px){.gp-grid{grid-template-columns:1fr}.gp-main{position:static;order:-1}#gpMap,#gp3d{height:60vh}.gp-info{grid-template-columns:1fr}}
        @media print{.gp-side .gp-card:not(.gp-print),.gp-tabs,.gp-draw,.gp-ctas{display:none!important}}
    </style>
</head>
<body data-tool-slug="ges-planlayici">
<x-site.contech-header />
<main class="gp-wrap">
    <nav class="gp-crumb" aria-label="Gezinti yolu"><a href="/">Ana sayfa</a> / <a href="/araclar">Araçlar</a> / GES Planlayıcı</nav>
    <h1>GES planlayıcı: adresinizi yazın, çatınıza panelleri dizelim</h1>
    <p class="gp-lede">Ev, işyeri ya da fabrika: çatınız uydu haritasında gelir, güneş panelleri kenar boşlukları bırakılarak otomatik yerleşir. 2D ve 3D bakın; yıllık üretim, kurulu güç, maliyet ve geri dönüş süresi hesaplansın.</p>

    <div class="gp-grid">
        <div class="gp-side">
            <section class="gp-card" aria-labelledby="s1">
                <h2 id="s1"><i>1</i>Adres</h2>
                <form id="gpSearch" class="gp-row" onsubmit="return false">
                    <input class="gp-in" id="gpQ" type="search" placeholder="Örn. Atatürk Cad. 12, Kadıköy, İstanbul" autocomplete="street-address" aria-label="Adres">
                    <button class="gp-btn" id="gpGo" type="submit">Bul</button>
                </form>
                <ul class="gp-res" id="gpResults"></ul>
                <p class="gp-msg" id="gpMsg">Adresi yazın ya da haritada binanızın üzerine tıklayın.</p>
            </section>

            <section class="gp-card" aria-labelledby="s2">
                <h2 id="s2"><i>2</i>Bina ve çatı</h2>
                <div class="gp-seg" role="radiogroup" aria-label="Kullanım">
                    <input type="radio" name="use" id="u1" value="konut" checked><label for="u1">Konut<small>ev, villa, apartman</small></label>
                    <input type="radio" name="use" id="u2" value="fabrika"><label for="u2">Fabrika / işyeri<small>OSB, depo, AVM</small></label>
                </div>
                <div class="gp-seg c4" role="radiogroup" aria-label="Çatı tipi" style="margin-top:8px">
                    <input type="radio" name="roof" id="r1" value="besik" checked><label for="r1">Beşik<small>iki yüzey</small></label>
                    <input type="radio" name="roof" id="r2" value="kirma"><label for="r2">Kırma<small>dört yüzey</small></label>
                    <input type="radio" name="roof" id="r3" value="duz"><label for="r3">Düz<small>teras</small></label>
                    <input type="radio" name="roof" id="r4" value="trapez"><label for="r4">Trapez<small>sandviç panel</small></label>
                </div>
                <div class="gp-f">
                    <div><label for="gpSlope">Çatı eğimi (°)</label><input class="gp-in" id="gpSlope" type="number" min="0" max="60" step="1" value="30"></div>
                    <div><label for="gpFloors">Kat sayısı</label><input class="gp-in" id="gpFloors" type="number" min="1" max="40" step="1" value="2"></div>
                    <div><label for="gpPanel">Panel</label><select class="gp-in" id="gpPanel"></select></div>
                    <div><label for="gpSetback">Kenar boşluğu (m)</label><input class="gp-in" id="gpSetback" type="number" min="0" max="5" step="0.1" value="0.5"></div>
                </div>
                <div class="gp-row" style="margin-top:10px;flex-wrap:wrap">
                    <button class="gp-btn o s" type="button" id="gpSwap">Mahya yönünü çevir</button>
                    <button class="gp-btn o s" type="button" id="gpDrawBtn">Çatıyı kendim çizeyim</button>
                    <button class="gp-btn o s" type="button" id="gpEditBtn" disabled>Köşeleri düzenle</button>
                </div>
            </section>

            <section class="gp-card" aria-labelledby="s3">
                <h2 id="s3"><i>3</i>Çatı yüzeyleri</h2>
                <ul class="gp-faces" id="gpFaces"><li style="grid-template-columns:1fr"><span>Çatı seçilince yüzeyler burada listelenir. Kuzeye bakan yüzeyler başta kapalıdır; tıklayarak açıp kapatabilirsiniz.</span></li></ul>
            </section>

            <section class="gp-card" aria-labelledby="s4">
                <h2 id="s4"><i>4</i>Tüketim</h2>
                <div class="gp-f" id="gpConsKonut">
                    <div class="full"><label for="gpBill">Aylık ortalama elektrik faturası (TL)</label><input class="gp-in" id="gpBill" type="number" min="0" step="50" placeholder="Örn. 2500"></div>
                </div>
                <div class="gp-f" id="gpConsFab" hidden>
                    <div><label for="gpKwh">Aylık tüketim (kWh)</label><input class="gp-in" id="gpKwh" type="number" min="0" step="100" placeholder="Örn. 40000"></div>
                    <div><label for="gpPrice">Birim fiyat (TL/kWh)</label><input class="gp-in" id="gpPrice" type="number" min="0.5" max="50" step="0.01"></div>
                </div>
                <label class="gp-check"><input type="checkbox" id="gpBattery"> Enerji depolama (batarya) istiyorum</label>
            </section>

            <section class="gp-card gp-print" aria-labelledby="s5" aria-live="polite">
                <h2 id="s5"><i>5</i>Sonuç</h2>
                <div id="gpOut"><p class="gp-msg">Çatı ve paneller hazır olunca üretim ve maliyet burada hesaplanır.</p></div>
            </section>
        </div>

        <div class="gp-main">
            <div class="gp-view">
                <div class="gp-tabs" role="tablist">
                    <button class="gp-tab" role="tab" aria-selected="true" data-tab="2d">2D uydu</button>
                    <button class="gp-tab" role="tab" aria-selected="false" data-tab="3d">3D model</button>
                    <span class="gp-kpi" id="gpKpi"></span>
                </div>
                <div style="position:relative">
                    <div id="gpMap" role="region" aria-label="Uydu haritası"></div>
                    <div id="gp3d" hidden role="region" aria-label="3D çatı modeli"></div>
                    <div class="gp-draw" id="gpDrawBar" hidden>
                        <button class="gp-btn s" type="button" id="gpDrawDone">Bitir</button>
                        <button class="gp-btn o s" type="button" id="gpDrawUndo">Geri al</button>
                        <button class="gp-btn o s" type="button" id="gpDrawCancel">Vazgeç</button>
                    </div>
                    <div class="gp-legend" id="gpLegend" hidden>Yüzey verimi<i></i><span><em>Düşük</em><em>Yüksek</em></span></div>
                </div>
            </div>
        </div>
    </div>

    <section class="gp-info" aria-label="Nasıl hesaplanır">
        <div class="gp-card"><h3>Çatı nereden geliyor?</h3><p>Bina dış hattı OpenStreetMap'ten alınır. Kayıtlı değilse ya da binanız farklıysa haritada köşelere tıklayarak çatıyı kendiniz çizersiniz. Çatı tipi ve eğimini siz seçersiniz.</p></div>
        <div class="gp-card"><h3>Üretim nasıl hesaplanıyor?</h3><p>Her çatı yüzeyi kendi yönü ve eğimiyle Avrupa Komisyonu'nun PVGIS güneş verisiyle hesaplanır; %14 sistem kaybı düşülür. Düz çatıda sıralar kışın öğle saatinde birbirini gölgelemeyecek aralıkla dizilir.</p></div>
        <div class="gp-card"><h3>Ne kadar kesin?</h3><p>Ön tasarımdır. Baca, çatı penceresi, ağaç gölgesi ve çatı taşıma kapasitesi keşifte kontrol edilir. Kesin proje ve teklif için yerinde keşif gerekir.</p></div>
    </section>
</main>
<x-site.contech-footer />

<script src="{{ $cfg['leaflet']['js'] }}"></script>
<script src="{{ \App\Support\Asset::url('js/ges-geometry.js') }}"></script>
<script>window.GES = {!! json_encode($C, $jsonFlags) !!};</script>
<script type="module">
const C = window.GES, G = window.GesGeo, $ = (id) => document.getElementById(id);
const csrf = document.querySelector('meta[name=csrf-token]').content;
const nf0 = new Intl.NumberFormat('tr-TR', { maximumFractionDigits: 0 }), nf1 = new Intl.NumberFormat('tr-TR', { maximumFractionDigits: 1 }), nf2 = new Intl.NumberFormat('tr-TR', { maximumFractionDigits: 2 });
const tl = (v) => '₺' + nf0.format(v);
const esc = (s) => String(s).replace(/[&<>"]/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]));
const S = { lat: null, lon: null, ring: null, proj: null, roof: null, layouts: [], off: new Set(), swap: false, drawing: false, editing: false, draft: [] };

// Panel seçenekleri
Object.entries(C.panels).forEach(([k, p]) => { const o = new Option(p.label, k); if (k === C.panel) o.selected = true; $('gpPanel').add(o); });
$('gpPrice').value = C.kwhPrice;

// ── Harita ──
const map = L.map('gpMap', { zoomControl: true, maxZoom: C.imagery.max_zoom }).setView([39.0, 35.2], 6);
L.tileLayer(C.imagery.url, { maxZoom: C.imagery.max_zoom, maxNativeZoom: 19, attribution: C.imagery.attribution }).addTo(map);
const canvas = L.canvas({ padding: 0.3 });
const lyr = { bld: L.layerGroup().addTo(map), faces: L.layerGroup().addTo(map), panels: L.layerGroup().addTo(map), labels: L.layerGroup().addTo(map), draft: L.layerGroup().addTo(map), edit: L.layerGroup().addTo(map), pin: L.layerGroup().addTo(map) };

function msg(t, warn) { $('gpMsg').textContent = t; $('gpMsg').classList.toggle('warn', !!warn); }
function val(name) { return document.querySelector('input[name=' + name + ']:checked').value; }

// Adres arama
$('gpSearch').addEventListener('submit', async () => {
    const q = $('gpQ').value.trim();
    if (q.length < 3) return msg('Lütfen adresi yazın (en az 3 harf).', true);
    $('gpGo').disabled = true; msg('Aranıyor…');
    try {
        const r = await fetch(C.geo + '?q=' + encodeURIComponent(q), { headers: { Accept: 'application/json' } }).then((x) => x.json());
        const list = r.results || [];
        $('gpResults').innerHTML = list.map((p, i) => '<li><button type="button" data-i="' + i + '">' + esc(p.name) + '</button></li>').join('');
        $('gpResults').querySelectorAll('button').forEach((b) => b.addEventListener('click', () => pick(list[+b.dataset.i])));
        if (list.length === 1) pick(list[0]);
        msg(list.length ? 'Doğru adresi seçin.' : 'Adres bulunamadı. Mahalle ve ilçeyle deneyin ya da haritada binanıza tıklayın.', !list.length);
    } catch (e) { msg('Adres servisine ulaşılamadı; haritada binanıza tıklayabilirsiniz.', true); }
    $('gpGo').disabled = false;
});
function pick(p) { $('gpResults').innerHTML = ''; locate(p.lat, p.lon, true); }

map.on('click', (e) => {
    if (S.drawing) return addDraft(e.latlng);
    if (S.editing) return;
    locate(e.latlng.lat, e.latlng.lng, false);
});

async function locate(lat, lon, fly) {
    S.lat = lat; S.lon = lon;
    lyr.pin.clearLayers(); L.circleMarker([lat, lon], { radius: 6, color: '#fff', weight: 2, fillColor: '#FFB800', fillOpacity: 1 }).addTo(lyr.pin);
    if (fly) map.setView([lat, lon], 19);
    msg('Bina aranıyor…');
    try {
        const r = await fetch(C.bld + '?lat=' + lat + '&lon=' + lon, { headers: { Accept: 'application/json' } });
        const b = r.ok ? await r.json() : { found: false };
        if (b.found) {
            if (b.levels) $('gpFloors').value = Math.min(40, Math.max(1, b.levels));
            if (/industrial|warehouse|factory|commercial|retail/.test(b.type || '')) setUse('fabrika');
            setRing(b.ring);
            map.fitBounds(L.latLngBounds(b.ring).pad(0.6), { maxZoom: 20 });
            msg('Bina bulundu. Farklıysa başka binaya tıklayın ya da "Çatıyı kendim çizeyim" ile çizin.');
        } else {
            msg('Bu noktada kayıtlı bina hattı yok. "Çatıyı kendim çizeyim"e basıp çatının köşelerine sırayla tıklayın.', true);
            if (!S.ring) startDraw();
        }
    } catch (e) { msg('Bina verisi alınamadı; çatıyı kendiniz çizebilirsiniz.', true); }
}

// Çizim
function startDraw() { S.drawing = true; S.editing = false; S.draft = []; lyr.edit.clearLayers(); $('gpDrawBar').hidden = false; map.getContainer().style.cursor = 'crosshair'; drawDraft(); msg('Çatının köşelerine sırayla tıklayın, sonra "Bitir".'); }
function stopDraw() { S.drawing = false; $('gpDrawBar').hidden = true; map.getContainer().style.cursor = ''; lyr.draft.clearLayers(); }
function addDraft(ll) { S.draft.push([ll.lat, ll.lng]); drawDraft(); }
function drawDraft() {
    lyr.draft.clearLayers();
    if (S.draft.length) L.polyline(S.draft.concat(S.draft.length > 2 ? [S.draft[0]] : []), { color: '#FFB800', weight: 3, dashArray: '6 4' }).addTo(lyr.draft);
    S.draft.forEach((p) => L.circleMarker(p, { radius: 5, color: '#101318', weight: 2, fillColor: '#FFB800', fillOpacity: 1 }).addTo(lyr.draft));
}
$('gpDrawBtn').addEventListener('click', startDraw);
$('gpDrawUndo').addEventListener('click', () => { S.draft.pop(); drawDraft(); });
$('gpDrawCancel').addEventListener('click', stopDraw);
$('gpDrawDone').addEventListener('click', () => {
    if (S.draft.length < 3) return msg('En az 3 köşe gerekir.', true);
    const ring = S.draft.slice(); stopDraw(); setRing(ring); msg('Çatı çizildi. Köşeleri düzeltmek için "Köşeleri düzenle".');
});
$('gpEditBtn').addEventListener('click', () => {
    S.editing = !S.editing; $('gpEditBtn').textContent = S.editing ? 'Düzenlemeyi bitir' : 'Köşeleri düzenle'; drawEdit();
});
function drawEdit() {
    lyr.edit.clearLayers();
    if (!S.editing || !S.ring) return;
    S.ring.forEach((p, i) => {
        const m = L.marker(p, { draggable: true, icon: L.divIcon({ className: '', html: '<div style="width:14px;height:14px;border-radius:50%;background:#FFB800;border:2px solid #101318"></div>', iconSize: [14, 14], iconAnchor: [7, 7] }) }).addTo(lyr.edit);
        m.on('drag', (e) => { S.ring[i] = [e.latlng.lat, e.latlng.lng]; update(); });
    });
}

function setRing(ring) { S.ring = ring.map((p) => [+p[0], +p[1]]); S.off.clear(); S.lat = S.lat ?? S.ring[0][0]; S.lon = S.lon ?? S.ring[0][1]; $('gpEditBtn').disabled = false; drawEdit(); update(); }

// Ayarlar
function setUse(u) { document.querySelector('input[name=use][value=' + u + ']').checked = true; onUse(); }
function onUse() {
    const fab = val('use') === 'fabrika';
    $('gpConsKonut').hidden = fab; $('gpConsFab').hidden = !fab;
    $('gpSetback').value = C.setback[fab ? 'fabrika' : 'konut'];
    if (fab && val('roof') === 'besik') { document.querySelector('input[name=roof][value=trapez]').checked = true; onRoof(); }
    update();
}
function onRoof() {
    const r = val('roof');
    $('gpSlope').value = r === 'duz' ? C.flatTilt : (r === 'trapez' ? 7 : 30);
    $('gpSlope').previousElementSibling.textContent = r === 'duz' ? 'Panel eğimi (°)' : 'Çatı eğimi (°)';
    S.off.clear(); update();
}
document.querySelectorAll('input[name=use]').forEach((i) => i.addEventListener('change', onUse));
document.querySelectorAll('input[name=roof]').forEach((i) => i.addEventListener('change', onRoof));
['gpSlope', 'gpFloors', 'gpPanel', 'gpSetback'].forEach((id) => $(id).addEventListener('input', () => update()));
$('gpSwap').addEventListener('click', () => { S.swap = !S.swap; S.off.clear(); update(); });
['gpBill', 'gpKwh', 'gpPrice', 'gpBattery'].forEach((id) => $(id).addEventListener('input', () => schedule()));

// Verim rengi: güneye yakınlık ve eğim (yalnız görsel)
function eff(az, tilt) { const d = Math.abs(((az - 180 + 540) % 360) - 180); return Math.max(0.35, 1 - 0.0028 * d * Math.min(1, tilt / 25) - 0.003 * Math.abs(tilt - 30) * 0.4); }
function effColor(e) { const t = Math.max(0, Math.min(1, (e - 0.5) / 0.5)); const st = [[139, 92, 246], [239, 68, 68], [245, 158, 11], [253, 224, 71]]; const k = t * 3, i = Math.min(2, Math.floor(k)), f = k - i; const c = st[i].map((v, j) => Math.round(v + (st[i + 1][j] - v) * f)); return 'rgb(' + c.join(',') + ')'; }

// ── Hesap ve çizim ──
function params() {
    const panel = C.panels[$('gpPanel').value];
    return { panel, roof: val('roof'), slope: Math.max(0, Math.min(60, +$('gpSlope').value || 0)), floors: Math.max(1, +$('gpFloors').value || 1), setback: Math.max(0, +$('gpSetback').value || 0) };
}
function update() {
    if (!S.ring || S.ring.length < 3) return;
    const P = params();
    S.proj = G.project(S.ring);
    const wallH = P.floors * 3;
    S.roof = G.faces(S.proj.pts, { type: P.roof, slope: P.roof === 'duz' ? 0 : P.slope, swap: S.swap, wallH });
    if (!S.off.size && S.roof.faces.length > 1) S.roof.faces.forEach((f) => { const d = Math.abs(((f.azimuth - 180 + 540) % 360) - 180); if (d > 110) S.off.add(f.id); });   // güneyden 110°'den fazla sapan (kuzey) yüzeyler
    S.layouts = S.roof.faces.map((f) => S.off.has(f.id) ? { panels: [], kwp: 0, tilt: f.slope, azimuth: f.azimuth, down: f.down }
        : G.layout(f, { l: P.panel.l, s: P.panel.s, w: P.panel.w, setback: P.setback, gap: 0.02, flat: { tilt: P.slope, lat: S.proj.origin[0] } }));
    draw2d(); renderFaces(); if (three.on) build3d(); schedule();
}

function ll(x, y) { return G.unproject(S.proj, x, y); }
function draw2d() {
    Object.values(lyr).forEach((g) => g !== lyr.edit && g !== lyr.pin && g !== lyr.draft && g.clearLayers());
    L.polygon(S.ring, { color: '#101318', weight: 2, fill: false }).addTo(lyr.bld);
    S.roof.faces.forEach((f, i) => {
        const lay = S.layouts[i], off = S.off.has(f.id), e = eff(lay.azimuth, lay.tilt);
        L.polygon(f.pts.map((p) => ll(p[0], p[1])), { renderer: canvas, color: '#fff', weight: 1, fillColor: off ? '#6B7280' : effColor(e), fillOpacity: off ? 0.25 : 0.42 }).addTo(lyr.faces);
        lay.panels.forEach((pn) => L.polygon(pn.corners.map((c) => ll(c[0], c[1])), { renderer: canvas, color: '#94A3B8', weight: 0.6, fillColor: '#0F2747', fillOpacity: 0.92 }).addTo(lyr.panels));
    });
    G.edges(S.proj.pts).forEach((ed) => {
        if (ed.len < 1.5) return;
        L.tooltip({ permanent: true, direction: 'center', className: 'gp-len' }).setContent(nf2.format(ed.len) + ' m').setLatLng(ll((ed.a[0] + ed.b[0]) / 2, (ed.a[1] + ed.b[1]) / 2)).addTo(lyr.labels);
    });
    $('gpLegend').hidden = false;
    const n = S.layouts.reduce((a, l) => a + l.panels.length, 0), kwp = S.layouts.reduce((a, l) => a + l.kwp, 0);
    $('gpKpi').innerHTML = '<b>' + nf2.format(kwp) + ' kWp</b> · ' + n + ' × ' + params().panel.w + ' W';
}

function renderFaces() {
    $('gpFaces').innerHTML = S.roof.faces.map((f, i) => {
        const l = S.layouts[i], off = S.off.has(f.id);
        return '<li><input type="checkbox" data-f="' + f.id + '" ' + (off ? '' : 'checked') + ' aria-label="' + f.id + '. yüzey"><div><b><span class="gp-dot" style="background:' + effColor(eff(l.azimuth, l.tilt)) + '"></span>' + f.id + '. ' + G.dirName(l.azimuth) + '</b><span>Yön ' + Math.round(l.azimuth) + '° · eğim ' + Math.round(l.tilt) + '° · ' + nf1.format(G.area(f.pts)) + ' m²</span></div><em>' + (off ? 'kapalı' : l.panels.length + ' panel · ' + nf2.format(l.kwp) + ' kWp') + '</em></li>';
    }).join('');
    $('gpFaces').querySelectorAll('input').forEach((c) => c.addEventListener('change', () => { const id = +c.dataset.f; c.checked ? S.off.delete(id) : S.off.add(id); if (!S.off.size) S.off.add(-1); update(); }));
}

// ── Sunucuda üretim ve maliyet ──
let timer = null, seq = 0;
function schedule() { clearTimeout(timer); timer = setTimeout(calc, 600); }
async function calc() {
    if (!S.roof) return;
    const faces = S.layouts.map((l) => ({ kwp: +l.kwp.toFixed(3), angle: Math.round(l.tilt), azimuth: Math.round(l.azimuth) })).filter((f) => f.kwp > 0);
    if (!faces.length) { $('gpOut').innerHTML = '<p class="gp-msg warn">Çatıya panel sığmadı. Kenar boşluğunu azaltın, çatı tipini ya da mahya yönünü değiştirin.</p>'; return; }
    const fab = val('use') === 'fabrika', price = fab ? (+$('gpPrice').value || C.kwhPrice) : C.kwhPrice;
    const monthly = fab ? (+$('gpKwh').value || 0) : ((+$('gpBill').value || 0) / price);
    const body = { lat: S.proj.origin[0], lon: S.proj.origin[1], use: fab ? 'fabrika' : 'konut', flat: val('roof') === 'duz', faces, monthly_kwh: Math.round(monthly), price, battery: $('gpBattery').checked };
    const my = ++seq;
    $('gpOut').style.opacity = 0.55;
    try {
        const r = await fetch(C.calc, { method: 'POST', headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': csrf }, body: JSON.stringify(body) });
        const d = await r.json();
        if (my !== seq) return;
        $('gpOut').style.opacity = 1;
        if (!r.ok || !d.ok) { $('gpOut').innerHTML = '<p class="gp-msg warn">' + esc(d.message || 'Hesaplanamadı, biraz sonra tekrar deneyin.') + '</p>'; return; }
        renderOut(d, body, monthly);
    } catch (e) { $('gpOut').style.opacity = 1; $('gpOut').innerHTML = '<p class="gp-msg warn">Bağlantı hatası; tekrar deneyin.</p>'; }
}

function chart(m) {
    const max = Math.max(...m, 1), w = 300, h = 110, bw = w / 12;
    const ay = ['O', 'Ş', 'M', 'N', 'M', 'H', 'T', 'A', 'E', 'E', 'K', 'A'];
    return '<svg class="gp-chart" viewBox="0 0 ' + w + ' ' + (h + 14) + '" role="img" aria-label="Aylık üretim">' + m.map((v, i) => {
        const bh = Math.max(2, v / max * h);
        return '<rect x="' + (i * bw + 3) + '" y="' + (h - bh) + '" width="' + (bw - 6) + '" height="' + bh + '" rx="3" fill="#FFB800"><title>' + nf0.format(v) + ' kWh</title></rect><text x="' + (i * bw + bw / 2) + '" y="' + (h + 12) + '" font-size="9" text-anchor="middle" fill="#5B6472">' + ay[i] + '</text>';
    }).join('') + '</svg>';
}

function renderOut(d, body, monthly) {
    const e = d.eco, y = d.yield, n = S.layouts.reduce((a, l) => a + l.panels.length, 0);
    const fab = body.use === 'fabrika';
    const sun = S.proj.origin[0] < 37.5 ? '5.5' : (S.proj.origin[0] < 40 ? '4.5' : '4.0');
    const bill = Math.round((monthly || (y.yearly / 12)) * body.price);
    const quote = C.quote + '?' + new URLSearchParams({ bill: String(Math.max(200, bill)), sun, mount: 'cati', quality: 'orta', ...(body.battery ? { 'extras[]': 'battery' } : {}) }).toString() + '#teklif';
    let h = '<div class="gp-big"><small>Tahmini kurulum maliyeti (KDV dahil)</small><b>' + tl(e.low) + ' – ' + tl(e.high) + '</b><small>' + nf2.format(d.kwp) + ' kWp · ' + n + ' panel</small></div>';
    h += '<div class="gp-sum">'
        + '<div><small>Yıllık üretim</small><b>' + nf0.format(y.yearly) + ' kWh</b></div>'
        + '<div><small>Özgül verim</small><b>' + nf0.format(d.specific) + ' kWh/kWp</b></div>'
        + '<div><small>Yıllık tasarruf</small><b>' + (monthly ? tl(e.saving) : '—') + '</b></div>'
        + '<div><small>Geri dönüş</small><b>' + (monthly && e.payback ? nf1.format(e.payback) + ' yıl' : '—') + '</b></div>'
        + (e.coverage !== null ? '<div><small>Tüketimi karşılama</small><b>%' + e.coverage + '</b></div>' : '')
        + '<div><small>CO₂ azaltımı</small><b>≈' + nf1.format(e.co2) + ' t/yıl</b></div></div>';
    h += chart(y.monthly);
    h += '<ul class="gp-items">' + Object.entries(e.items).map(([k, v]) => '<li><span>' + esc(k) + '</span><b>' + tl(v) + '</b></li>').join('') + '</ul>';
    if (!monthly) h += '<p class="gp-msg">Tasarruf ve geri dönüş için ' + (fab ? 'aylık tüketiminizi' : 'aylık faturanızı') + ' girin.</p>';
    if (e.excess > 0) h += '<p class="gp-note">Tüketiminizden yaklaşık ' + nf0.format(e.excess) + ' kWh/yıl fazla üretim çıkıyor; fazla üretimin mahsubu ve satışı mevzuata ve dağıtım şirketi koşullarına bağlıdır, tasarrufa dahil edilmedi.</p>';
    if (body.battery) h += '<p class="gp-note">Depolamalı sistemlerde, anlaşma gücünün üzerindeki depolama için otomatik güç kontrolü ve enerji kesme sistemi gerekir; bu kalem tahminidir, keşifte netleşir.</p>';
    if (fab || body.battery) h += '<div class="gp-legal"><b>Mevzuat uygunluk süreci</b>Lisanssız üretim ve depolama mevzuatındaki son değişiklikler nedeniyle teklif yalnız panel ve montajdan oluşmaz. Keşifte mühendislerimiz şunları netleştirir:<ol><li>Bağlantı anlaşması ve anlaşma gücü (gerekiyorsa revizyon ve süreleri)</li><li>Uzaktan izleme ve acil durumda uzaktan kontrol (haberleşme / SCADA) gereklilikleri</li><li>Depolama varsa otomatik güç kontrolü ve enerji kesme sistemi</li></ol>Bu hükümler her tesise aynı şekilde uygulanmaz; tesisinizin kapsamı keşifte kontrol edilir.</div>';
    h += '<p class="gp-note">Üretim: ' + (y.source === 'PVGIS' ? 'PVGIS (Avrupa Komisyonu JRC) güneş verisi' : 'yaklaşık bölgesel değer (PVGIS şu an yanıt vermedi)') + ', %14 sistem kaybı. Maliyet aralığı −%15 / +%20; ön tasarımdır, keşifte netleşir.</p>';
    h += '<div class="gp-ctas"><a class="gp-btn" href="' + esc(quote) + '">Bu tasarımla teklif iste</a><button class="gp-btn o" type="button" onclick="window.print()">Yazdır</button></div>';
    $('gpOut').innerHTML = h;
}

// ── 3D ──
const three = { on: false, ready: null };
document.querySelectorAll('.gp-tab').forEach((t) => t.addEventListener('click', () => {
    document.querySelectorAll('.gp-tab').forEach((x) => x.setAttribute('aria-selected', x === t ? 'true' : 'false'));
    const is3d = t.dataset.tab === '3d';
    $('gpMap').hidden = is3d; $('gp3d').hidden = !is3d; $('gpDrawBar').hidden = is3d || !S.drawing;
    three.on = is3d;
    if (is3d) build3d(); else map.invalidateSize();
}));

async function init3d() {
    const THREE = await import('three');
    const { OrbitControls } = await import('three/addons/controls/OrbitControls.js');
    const { CSS2DRenderer, CSS2DObject } = await import('three/addons/renderers/CSS2DRenderer.js');
    const el = $('gp3d');
    const renderer = new THREE.WebGLRenderer({ antialias: true });
    renderer.setPixelRatio(Math.min(2, devicePixelRatio));
    el.appendChild(renderer.domElement);
    const labels = new CSS2DRenderer(); labels.domElement.style.cssText = 'position:absolute;top:0;left:0;pointer-events:none';
    el.appendChild(labels.domElement);
    const scene = new THREE.Scene(); scene.background = new THREE.Color('#cfd8e3');
    const cam = new THREE.PerspectiveCamera(45, 1, 0.5, 5000);
    const ctl = new OrbitControls(cam, renderer.domElement); ctl.enableDamping = true; ctl.maxPolarAngle = Math.PI * 0.49;
    scene.add(new THREE.HemisphereLight(0xffffff, 0x8a8f99, 1.1));
    const sun = new THREE.DirectionalLight(0xffffff, 1.4); sun.position.set(-40, 80, 60); scene.add(sun);
    const group = new THREE.Group(); scene.add(group);
    function size() { const w = el.clientWidth, h = el.clientHeight; renderer.setSize(w, h); labels.setSize(w, h); cam.aspect = w / h; cam.updateProjectionMatrix(); }
    new ResizeObserver(size).observe(el); size();
    (function loop() { requestAnimationFrame(loop); if (!three.on) return; ctl.update(); renderer.render(scene, cam); labels.render(scene, cam); })();
    return { THREE, CSS2DObject, scene, cam, ctl, group };
}

// Uydu zemin dokusu (yakın karolar birleştirilir; yüklenemezse düz zemin)
function groundTexture(T, span) {
    const lat = S.proj.origin[0], lon = S.proj.origin[1], cosl = Math.cos(lat * Math.PI / 180);
    const z = Math.max(15, Math.min(19, Math.floor(Math.log2(3 * 40075016.686 * cosl / Math.max(80, 2.5 * span)))));
    const n = 2 ** z, mPerTile = 40075016.686 * cosl / n;
    const tx = (lon + 180) / 360 * n, ty = (1 - Math.log(Math.tan(lat * Math.PI / 180) + 1 / cosl) / Math.PI) / 2 * n;
    const x0 = Math.floor(tx) - 1, y0 = Math.floor(ty) - 1, cv = document.createElement('canvas'); cv.width = cv.height = 768;
    const ctx = cv.getContext('2d'); ctx.fillStyle = '#9aa5a0'; ctx.fillRect(0, 0, 768, 768);
    const tex = new T.CanvasTexture(cv); tex.colorSpace = T.SRGBColorSpace;
    for (let i = 0; i < 3; i++) for (let j = 0; j < 3; j++) {
        const img = new Image(); img.crossOrigin = 'anonymous';
        img.onload = () => { try { ctx.drawImage(img, i * 256, j * 256); tex.needsUpdate = true; } catch (e) {} };
        img.src = C.imagery.url.replace('{z}', z).replace('{x}', x0 + i).replace('{y}', y0 + j);
    }
    // bina merkezinin, 3×3 karonun ortasına göre kayması (m): doğu ve güney
    return { tex, size: 3 * mPerTile, offX: (tx - (x0 + 1.5)) * mPerTile, offY: (ty - (y0 + 1.5)) * mPerTile };
}

async function build3d() {
    if (!S.roof) return;
    three.ready = three.ready || init3d();
    const { THREE: T, CSS2DObject, cam, ctl, group } = await three.ready;
    while (group.children.length) { const c = group.children.pop(); c.traverse?.((o) => { o.geometry?.dispose(); if (o.element) o.element.remove(); }); }
    const P = params(), H = P.floors * 3, o = S.roof.obb, span = Math.max(o.L, o.W);
    const V3 = (x, y, h) => new T.Vector3(x, h, -y);

    // zemin
    const g = groundTexture(T, span);
    const ground = new T.Mesh(new T.PlaneGeometry(g.size, g.size), new T.MeshLambertMaterial({ map: g.tex }));
    ground.rotation.x = -Math.PI / 2; ground.position.set(-g.offX, -0.02, -g.offY); group.add(ground);

    // duvarlar
    const ring = S.roof.wallRing, pos = [];
    ring.forEach((a, i) => {
        const b = ring[(i + 1) % ring.length], ha = S.roof.wallTop(a[0], a[1]), hb = S.roof.wallTop(b[0], b[1]);
        pos.push(a[0], 0, -a[1], b[0], 0, -b[1], b[0], hb, -b[1], a[0], 0, -a[1], b[0], hb, -b[1], a[0], ha, -a[1]);
    });
    const wg = new T.BufferGeometry(); wg.setAttribute('position', new T.Float32BufferAttribute(pos, 3)); wg.computeVertexNormals();
    group.add(new T.Mesh(wg, new T.MeshLambertMaterial({ color: 0x1f2329, side: T.DoubleSide })));

    // çatı yüzeyleri
    S.roof.faces.forEach((f, i) => {
        const contour = f.pts.map((p) => new T.Vector2(p[0], p[1]));
        const tris = T.ShapeUtils.triangulateShape(contour, []);
        const v = [];
        tris.forEach((t) => t.forEach((k) => { const p = f.pts[k]; v.push(p[0], f.h(p[0], p[1]) + 0.01, -p[1]); }));
        const geo = new T.BufferGeometry(); geo.setAttribute('position', new T.Float32BufferAttribute(v, 3)); geo.computeVertexNormals();
        const lay = S.layouts[i], col = S.off.has(f.id) ? '#9CA3AF' : effColor(eff(lay.azimuth, lay.tilt));
        group.add(new T.Mesh(geo, new T.MeshLambertMaterial({ color: col, side: T.DoubleSide })));
        const edge = new T.LineSegments(new T.EdgesGeometry(geo), new T.LineBasicMaterial({ color: 0x101318 })); group.add(edge);
    });

    // paneller (tek çizim çağrısı)
    const all = []; S.layouts.forEach((l) => l.panels.forEach((p) => all.push([p, l])));
    if (all.length) {
        const box = new T.BoxGeometry(P.panel.s, 0.04, P.panel.l);
        const mesh = new T.InstancedMesh(box, new T.MeshStandardMaterial({ color: 0x0f2747, metalness: 0.35, roughness: 0.35 }), all.length);
        const m = new T.Matrix4(), q = new T.Quaternion(), sc = new T.Vector3(1, 1, 1);
        all.forEach(([p, l], k) => {
            const dx = l.down[0], dy = l.down[1], t = l.tilt * Math.PI / 180, flat = S.roof.faces[0].slope === 0 && P.roof === 'duz';
            let E = new T.Vector3(-dy, 0, -dx);                         // saçak boyunca (plan → 3B: y → -z)
            const Sv = new T.Vector3(-dx * Math.cos(t), Math.sin(t), dy * Math.cos(t)).normalize();   // eğim yukarı
            let N = new T.Vector3().crossVectors(Sv, E);
            if (N.y < 0) { E.negate(); N = new T.Vector3().crossVectors(Sv, E); }
            m.makeBasis(E, N, Sv);
            q.setFromRotationMatrix(m);
            const lift = flat ? P.panel.l / 2 * Math.sin(t) + 0.25 : 0.08;
            m.compose(new T.Vector3(p.c[0], p.h + lift, -p.c[1]), q, sc);
            mesh.setMatrixAt(k, m);
        });
        group.add(mesh);
    }

    // kenar ölçüleri
    G.edges(S.proj.pts).forEach((ed) => {
        if (ed.len < 1.5) return;
        const div = document.createElement('div'); div.className = 'gp-lab'; div.textContent = nf2.format(ed.len) + ' m';
        const lab = new CSS2DObject(div); const mx = (ed.a[0] + ed.b[0]) / 2, my = (ed.a[1] + ed.b[1]) / 2;
        lab.position.set(mx, S.roof.wallTop(mx, my) + 0.4, -my); group.add(lab);
    });

    if (!three.framed) {
        const d = Math.max(18, span * 1.35);
        cam.position.set(d * 0.7, d * 0.75, d * 0.9); ctl.target.set(0, H * 0.6, 0); ctl.update(); three.framed = true;
    }
}
</script>
</body>
</html>
