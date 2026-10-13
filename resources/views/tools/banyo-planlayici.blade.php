@php
    $year = now()->year;
    $isWc = ($variant ?? 'banyo') === 'wc';
    $title = $isWc ? "2D / 3D WC Planlayıcı $year: Tuvaletinizi Ölçüsüyle Çizin | Worksitem"
        : "2D / 3D Banyo Planlayıcı $year: Ölçülerinizle Banyonuzu Çizin | Worksitem";
    $desc = $isWc ? 'WC veya misafir tuvaletinizin ölçülerini girin; lavabo ve klozet otomatik yerleşsin. 2D planda sürükleyin, 3D görünümde seramik ve boya deneyin, maliyetini hesaplayın. Ücretsiz.'
        : 'Banyonuzun şeklini ve ölçülerini girin; lavabo, klozet, duşakabin veya küvet otomatik yerleşsin. 2D planda sürükleyin, kapıyı taşıyın, 3D görünümde seramikleri deneyin. Ücretsiz.';
    $url = $isWc ? 'https://worksitem.com/araclar/wc-planlayici' : 'https://worksitem.com/araclar/banyo-planlayici';
    $name = $isWc ? 'WC' : 'Banyo';
    $jsonFlags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP;
    $importMap = ['imports' => ['three' => $three['module'], 'three/addons/' => $three['addons']]];
@endphp
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <meta name="description" content="{{ $desc }}">
    <link rel="canonical" href="{{ $url }}">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $desc }}">
    <meta property="og:url" content="{{ $url }}">
    <meta property="og:image" content="https://worksitem.com/brand/og-image.png">
    <meta name="theme-color" content="#ffffff">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script type="application/ld+json">{!! json_encode([
        '@context' => 'https://schema.org', '@type' => 'WebApplication', 'name' => '2D / 3D '.$name.' Planlayıcı',
        'url' => $url, 'applicationCategory' => 'DesignApplication', 'operatingSystem' => 'Web', 'inLanguage' => 'tr-TR',
        'description' => $desc, 'offers' => ['@type' => 'Offer', 'price' => '0', 'priceCurrency' => 'TRY'],
        'publisher' => ['@type' => 'Organization', 'name' => 'Worksitem', 'url' => 'https://worksitem.com'],
    ], $jsonFlags) !!}</script>
    <script type="importmap">{!! json_encode($importMap, $jsonFlags) !!}</script>
    <style>
        :root{--ink:#101318;--yellow:#FFB800;--yellow-50:#FFF8E1;--page:#F5F6F8;--line:#E3E6EA;--muted:#5B6472;--white:#fff;--red:#D92D20;--r-lg:18px;--r-md:12px}
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Inter',system-ui,-apple-system,'Segoe UI',sans-serif;background:var(--page);color:var(--ink);line-height:1.5;-webkit-text-size-adjust:100%}
        a{color:inherit}
        button,input,select{font:inherit;color:inherit}
        :focus-visible{outline:3px solid var(--yellow);outline-offset:2px}
        .pl-wrap{max-width:1280px;margin:0 auto;padding:20px 16px 40px}
        .pl-crumb{font-size:13px;color:var(--muted);margin-bottom:14px}
        .pl-crumb a{text-decoration:none}
        .pl-head h1{font-size:clamp(26px,5vw,38px);line-height:1.12;letter-spacing:-.02em;font-weight:900}
        .pl-lede{margin-top:8px;font-size:16px;color:#363c46;max-width:72ch}
        .pl-bar{display:flex;flex-wrap:wrap;align-items:center;gap:10px;margin:20px 0 14px}
        .pl-tabs{display:inline-flex;background:var(--white);border:1px solid var(--line);border-radius:999px;padding:4px}
        .pl-tabs button{border:0;background:none;border-radius:999px;padding:8px 16px;font-weight:700;font-size:14px;cursor:pointer;color:var(--muted)}
        .pl-tabs button[aria-pressed="true"]{background:var(--ink);color:var(--white)}
        .pl-dims{font-size:14px;color:var(--muted);font-weight:600}
        .pl-dims b{color:var(--ink)}
        .pl-spacer{flex:1}
        .pl-btn{display:inline-flex;align-items:center;gap:6px;border:1.5px solid var(--line);background:var(--white);border-radius:var(--r-md);padding:9px 14px;font-weight:700;font-size:14px;cursor:pointer;text-decoration:none}
        .pl-btn:hover{border-color:#9aa1ab}
        .pl-btn.is-main{background:var(--yellow);border-color:var(--yellow)}
        .pl-btn.is-main:hover{background:#E5A500}
        .pl-grid{display:grid;grid-template-columns:330px minmax(0,1fr);grid-template-areas:'a s' 'b s';grid-template-rows:auto 1fr;gap:18px;align-items:start}
        .pl-side-a{grid-area:a}.pl-side-b{grid-area:b}
        .pl-stage{grid-area:s;position:sticky;top:12px}
        .pl-side{background:var(--white);border:1px solid var(--line);border-radius:var(--r-lg);padding:6px 18px 18px}
        .pl-sec{padding:16px 0;border-bottom:1px solid var(--line)}
        .pl-sec:last-child{border-bottom:0}
        .pl-sec h2{display:flex;align-items:center;gap:9px;font-size:15px;font-weight:800;margin-bottom:10px}
        .pl-sec h2 i{font-style:normal;display:inline-grid;place-items:center;width:22px;height:22px;border-radius:50%;background:var(--ink);color:var(--white);font-size:11px;flex:0 0 22px}
        .pl-shapes{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:6px;margin-bottom:12px}
        .pl-shape{border:1.5px solid var(--line);background:var(--white);border-radius:10px;padding:8px 4px 6px;cursor:pointer;display:flex;flex-direction:column;align-items:center;gap:4px;font-size:11.5px;font-weight:700;color:var(--muted)}
        .pl-shape svg{width:40px;height:32px}
        .pl-shape[aria-pressed="true"]{border-color:var(--ink);background:var(--yellow-50);color:var(--ink)}
        .pl-fields{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:8px}
        .pl-fields.c2{grid-template-columns:repeat(2,minmax(0,1fr))}
        .pl-f label{display:block;font-size:12px;font-weight:700;color:var(--muted);margin-bottom:4px}
        .pl-in{display:flex;align-items:center;border:1.5px solid var(--line);border-radius:10px;background:var(--white);overflow:hidden}
        .pl-in input,.pl-in select{border:0;outline:0;background:none;width:100%;min-width:0;padding:8px 8px;font-size:15px;font-weight:700}
        .pl-in span{padding-right:8px;font-size:12px;color:var(--muted)}
        .pl-in:focus-within{border-color:var(--ink)}
        .pl-cut[hidden]{display:none}
        .pl-note{font-size:12px;color:var(--muted);margin-top:8px}
        .pl-items{list-style:none;display:grid;gap:6px}
        .pl-item{display:flex;align-items:center;gap:10px;border:1.5px solid var(--line);border-radius:10px;padding:8px 10px;cursor:pointer;background:var(--white)}
        .pl-item:hover{border-color:#9aa1ab}
        .pl-item.is-sel{border-color:var(--ink);background:var(--yellow-50)}
        .pl-item.is-bad{border-color:var(--red)}
        .pl-item b{font-size:12px;background:var(--ink);color:var(--white);border-radius:6px;padding:2px 6px;flex:0 0 auto}
        .pl-item span{font-size:13px;line-height:1.3}
        .pl-item small{display:block;color:var(--muted);font-size:11.5px}
        .pl-add{display:flex;flex-wrap:wrap;gap:6px;margin-top:10px}
        .pl-add button{border:1.5px dashed #b6bcc5;background:none;border-radius:999px;padding:5px 10px;font-size:12.5px;font-weight:700;cursor:pointer}
        .pl-add button:hover{border-style:solid;border-color:var(--ink)}
        .pl-tag{display:inline-block;font-size:10.5px;font-weight:800;border-radius:999px;padding:2px 7px;background:#EEF0F3;color:var(--muted);text-transform:uppercase;letter-spacing:.03em;vertical-align:middle}
        .pl-tag.is-sp{background:var(--ink);color:var(--yellow)}
        .pl-tiles{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px}
        .pl-sw{display:flex;gap:8px;align-items:center;margin-top:6px;font-size:12px;color:var(--muted)}
        .pl-sw i{width:34px;height:34px;border-radius:8px;border:1px solid var(--line);background-size:cover;background-position:center;flex:0 0 34px}
        .pl-stage{background:var(--white);border:1px solid var(--line);border-radius:var(--r-lg);overflow:hidden;position:relative}
        .pl-canvas{height:min(74vh,680px);min-height:400px;position:relative;background:#EEF0F3;touch-action:none;overflow:hidden}
        #plan2d{position:absolute;inset:0}
        #plan2d[hidden]{display:none}
        #plan2d svg{width:100%;height:100%;display:block;user-select:none;-webkit-user-select:none}
        #plan3d{position:absolute;inset:0}
        #plan3d[hidden]{display:none}
        #plan3d canvas{display:block;width:100%!important;height:100%!important}
        .pl-msg{position:absolute;inset:0;display:grid;place-items:center;text-align:center;padding:24px;color:var(--muted);font-weight:600}
        .pl-warn{position:absolute;left:12px;top:12px;background:#FEF3F2;color:var(--red);border:1px solid #FECDCA;border-radius:10px;padding:6px 10px;font-size:13px;font-weight:700;z-index:3}
        .pl-warn[hidden]{display:none}
        .pl-tool{position:absolute;z-index:4;display:flex;align-items:center;gap:4px;background:var(--white);border:1px solid var(--line);border-radius:999px;padding:5px;box-shadow:0 8px 24px rgba(16,19,24,.16);transform:translate(-50%,-100%);white-space:nowrap}
        .pl-tool[hidden]{display:none}
        .pl-tool strong{font-size:12px;padding:0 8px 0 6px;max-width:160px;overflow:hidden;text-overflow:ellipsis}
        .pl-tool button{width:36px;height:36px;border-radius:50%;border:1px solid var(--line);background:var(--white);display:grid;place-items:center;cursor:pointer}
        .pl-tool button:hover{border-color:var(--ink)}
        .pl-tool button svg{width:18px;height:18px}
        .pl-hint{font-size:12.5px;color:var(--muted);padding:10px 14px;border-top:1px solid var(--line);background:var(--white)}
        .pl-it{cursor:grab}
        .pl-it:active{cursor:grabbing}
        .pl-door{cursor:ew-resize}
        .pl-list{margin-top:18px;background:var(--white);border:1px solid var(--line);border-radius:var(--r-lg);padding:18px}
        .pl-list h2{font-size:17px;font-weight:800;margin-bottom:10px}
        .pl-table{width:100%;border-collapse:collapse;font-size:14px}
        .pl-table th{text-align:left;font-size:12px;color:var(--muted);font-weight:700;padding:6px 8px;border-bottom:1px solid var(--line)}
        .pl-table td{padding:8px;border-bottom:1px solid var(--line);vertical-align:middle}
        .pl-legal{font-size:12.5px;color:var(--muted);margin-top:14px;max-width:90ch}
        .pl-copied{position:fixed;left:50%;bottom:24px;transform:translateX(-50%);background:var(--ink);color:var(--white);padding:10px 16px;border-radius:999px;font-weight:700;font-size:14px;z-index:50}
        .pl-copied[hidden]{display:none}
        dialog.pl-dlg{margin:auto;border:0;border-radius:18px;padding:0;max-width:440px;width:calc(100% - 32px);box-shadow:0 24px 60px rgba(0,0,0,.25);color:var(--ink);background:var(--white)}
        dialog.pl-dlg::backdrop{background:rgba(11,15,23,.5)}
        .pl-dlg form{padding:22px}
        .pl-dlg h3{font-size:19px;font-weight:800;margin-bottom:4px}
        .pl-dlg p{font-size:13px;color:var(--muted);margin-bottom:14px}
        .pl-dlg .pl-f{margin-bottom:10px}
        .pl-dlg small{display:block;font-size:11.5px;color:var(--muted);margin-top:3px}
        .pl-dlg .pl-row{display:flex;gap:8px;margin-top:14px}
        .pl-dlg .pl-row .pl-btn{flex:1;justify-content:center}
        .pl-els{display:flex;flex-wrap:wrap;gap:6px;margin-top:10px}
        .pl-els button{border:1.5px solid var(--line);background:var(--white);border-radius:999px;padding:5px 11px;font-size:12.5px;font-weight:700;cursor:pointer}
        .pl-els button:hover{border-color:var(--ink)}
        .pl-notes{width:100%;min-height:76px;border:1.5px solid var(--line);border-radius:10px;padding:8px 10px;font-size:14px;resize:vertical;background:var(--white)}
        .pl-notes:focus{outline:0;border-color:var(--ink)}
        .pl-sum{margin-top:12px;font-size:14px;font-weight:700}
        .pl-sum small{display:block;font-weight:500;color:var(--muted);font-size:12px}
        .pl-info{display:flex;gap:10px;align-items:center;border:1px solid var(--line);border-radius:10px;padding:8px;margin-bottom:10px;font-size:13px}
        .pl-info img{width:52px;height:52px;object-fit:contain;border-radius:8px;background:#F5F6F8}
        .pl-info a{font-weight:700}
        .pl-info[hidden]{display:none}
        .pl-nudge{display:flex;gap:6px;margin-top:8px}
        .pl-nudge button{flex:1;border:1.5px solid var(--line);background:var(--white);border-radius:8px;padding:6px 0;font-weight:800;cursor:pointer}
        .pl-ai-tabs{display:flex;gap:6px;margin-bottom:12px}
        .pl-ai-tabs button{flex:1;border:1.5px solid var(--line);background:var(--white);border-radius:10px;padding:8px;font-weight:700;font-size:13px;cursor:pointer}
        .pl-ai-tabs button[aria-pressed="true"]{border-color:var(--ink);background:var(--yellow-50)}
        .pl-ai-tabs button small{display:block;font-weight:500;font-size:11px;color:var(--muted);margin-top:2px}
        .pl-ai-out{margin-top:12px}
        .pl-ai-out img{width:100%;border-radius:12px;display:block}
        .pl-ai-msg{font-size:13px;margin-top:8px;color:var(--muted)}
        .pl-ai-msg.is-err{color:var(--red);font-weight:700}
        dialog.pl-dlg.is-wide{max-width:640px}
        .pl-tlink{font-weight:700;font-size:12px;margin-left:auto;color:var(--ink)}
        .pl-paints{display:flex;flex-wrap:wrap;gap:6px}
        .pl-paints button{width:34px;height:34px;border-radius:50%;border:2px solid var(--line);cursor:pointer}
        .pl-paints button[aria-pressed="true"]{border-color:var(--ink);box-shadow:0 0 0 2px var(--yellow)}
        .pl-fill{border:1px solid var(--line);background:var(--white);border-radius:8px;padding:3px 8px;font-size:12px;font-weight:700;cursor:pointer;margin-top:6px}
        html[data-theme="dark"]{--page:#0B0F17;--white:#141A24;--line:#2A3342;--ink:#E6E9EE;--muted:#97A1AF;--yellow-50:#2A2410}
        html[data-theme="dark"] .pl-lede{color:#C9CED6}
        html[data-theme="dark"] .pl-tabs button[aria-pressed="true"]{background:var(--yellow);color:#101318}
        html[data-theme="dark"] .pl-item b{background:var(--yellow);color:#101318}
        html[data-theme="dark"] .pl-btn.is-main{color:#101318}
        html[data-theme="dark"] .pl-canvas{background:#0F141D}
        @media (max-width:900px){.pl-grid{grid-template-columns:1fr;grid-template-areas:'a' 's' 'b';grid-template-rows:auto}.pl-stage{position:relative;top:auto}.pl-canvas{height:62vh;min-height:340px}}
        @media (max-width:560px){.pl-bar .pl-btn{flex:1;justify-content:center}.pl-spacer{display:none}.pl-table th:nth-child(3),.pl-table td:nth-child(3){display:none}}
    </style>
</head>
<body data-tool-slug="banyo-planlayici">

<x-site.contech-header />

<main class="pl-wrap">
    <nav class="pl-crumb" aria-label="Sayfa yolu"><a href="/">Ana sayfa</a> / <a href="/araclar">Araçlar</a> / <a href="{{ $back }}">{{ $name }} tadilat maliyeti</a> / <span>{{ $name }} planlayıcı</span></nav>

    <header class="pl-head">
        <h1>2D / 3D {{ $isWc ? 'WC' : 'banyo' }} planlayıcı</h1>
        <p class="pl-lede">{{ $isWc ? 'WC\'nizin' : 'Banyonuzun' }} şeklini ve ölçülerini girin; lavabo, klozet{{ $isWc ? '' : ' ve duş' }} otomatik yerleşsin. Parçaya dokunun: araç çubuğundan döndürün, ölçüsünü ve ürününü değiştirin. Kapıyı tutup duvar boyunca kaydırın; 3D görünümde seramikleri deneyin.</p>
    </header>

    <div class="pl-bar">
        <div class="pl-tabs" role="group" aria-label="Görünüm">
            <button type="button" id="tab2d" aria-pressed="true">2D plan</button>
            <button type="button" id="tab3d" aria-pressed="false">3D görünüm</button>
        </div>
        <div class="pl-dims" id="plDims"></div>
        <div class="pl-spacer"></div>
        <button type="button" class="pl-btn" id="plAi">✨ Gerçekçi görsel</button>
        <button type="button" class="pl-btn" id="plPrint">PDF / yazdır</button>
        <button type="button" class="pl-btn" id="plCopy">Bağlantıyı kopyala</button>
        <a class="pl-btn" id="plBack" href="{{ $back }}" title="Planın ölçüleri, duş tipi ve seçimleri maliyet hesabına aktarılır">Maliyeti hesapla</a>
        <a class="pl-btn is-main" id="plQuote" href="{{ $back }}#teklif">Bu plan için teklif iste</a>
    </div>

    <div class="pl-grid">
        <aside class="pl-side pl-side-a" aria-label="Oda şekli ve ölçüler">
            <section class="pl-sec">
                <h2><i>1</i>Oda şekli ve ölçüler</h2>
                <div class="pl-shapes" id="plShapes"></div>
                <div class="pl-fields">
                    <div class="pl-f"><label for="inW">En</label><div class="pl-in"><input type="number" id="inW" inputmode="numeric" step="5"><span>cm</span></div></div>
                    <div class="pl-f"><label for="inL">Boy</label><div class="pl-in"><input type="number" id="inL" inputmode="numeric" step="5"><span>cm</span></div></div>
                    <div class="pl-f"><label for="inH">Yükseklik</label><div class="pl-in"><input type="number" id="inH" inputmode="numeric" step="5"><span>cm</span></div></div>
                </div>
                <div class="pl-fields c2 pl-cut" id="plCut" style="margin-top:8px" hidden>
                    <div class="pl-f"><label for="inCW">Kesim eni</label><div class="pl-in"><input type="number" id="inCW" inputmode="numeric" step="5"><span>cm</span></div></div>
                    <div class="pl-f"><label for="inCL">Kesim boyu</label><div class="pl-in"><input type="number" id="inCL" inputmode="numeric" step="5"><span>cm</span></div></div>
                </div>
                <div class="pl-fields c2" style="margin-top:8px">
                    <div class="pl-f"><label for="inDoorW">Kapı genişliği</label><div class="pl-in"><input type="number" id="inDoorW" inputmode="numeric" step="5"><span>cm</span></div></div>
                </div>
                <p class="pl-note">Ölçüleri duvarın içinden alın. Kapıyı (sarı) ve pencereyi (mavi) tutamacından tutup duvar boyunca sürükleyin; başka duvara da taşınır.</p>
                <div class="pl-under">
                    <label class="pl-btn" style="justify-content:center;width:100%;margin-top:10px;cursor:pointer">📐 Kat planı resmini altlık yap<input type="file" id="ulFile" accept="image/*" hidden></label>
                    <div id="ulBox" hidden>
                        <div class="pl-fields c2" style="margin-top:8px">
                            <div class="pl-f"><label for="ulW">Resim genişliği</label><div class="pl-in"><input type="number" id="ulW" step="5"><span>cm</span></div></div>
                            <div class="pl-f"><label for="ulOp">Saydamlık</label><div class="pl-in"><input type="range" id="ulOp" min="10" max="90" value="45"></div></div>
                        </div>
                        <div class="pl-nudge">
                            <button type="button" data-d="-10,0">←</button><button type="button" data-d="10,0">→</button><button type="button" data-d="0,-10">↑</button><button type="button" data-d="0,10">↓</button>
                            <button type="button" id="ulDel">Kaldır</button>
                        </div>
                        <p class="pl-note">Resim yalnızca bu cihazda kalır, sunucuya gönderilmez. Genişliği ve yerini ayarlayıp oda ölçülerini resmin üstüne oturtun.</p>
                    </div>
                </div>
                <div class="pl-els" id="plEls">
                    <button type="button" data-el="win">+ Pencere</button>
                    <button type="button" data-el="icduvar">+ İç duvar</button>
                    <button type="button" data-el="engel">+ Kolon / şaft</button>
                    <button type="button" data-el="nis">+ Niş</button>
                </div>
            </section>
        </aside>

        <aside class="pl-side pl-side-b" aria-label="Parçalar ve seramik">

            <section class="pl-sec">
                <h2><i>2</i>Parçalar</h2>
                <ul class="pl-items" id="plItems"></ul>
                <div class="pl-add" id="plAdd"></div>
                <div style="display:flex;margin-top:10px"><button type="button" class="pl-btn" id="plAuto" style="flex:1;justify-content:center">Otomatik yerleştir</button></div>
            </section>

            <section class="pl-sec">
                <h2><i>3</i>Seramik ve boya</h2>
                <div class="pl-tiles">
                    <div class="pl-f"><label for="tFloor">Zemin</label><div class="pl-in"><select id="tFloor"></select></div><div class="pl-sw"><i id="swFloor"></i><span id="szFloor"></span><a class="pl-tlink" id="lkFloor" target="_blank" rel="sponsored nofollow noopener" hidden>İncele ↗</a></div></div>
                    <div class="pl-f"><label for="tWall">Duvar</label><div class="pl-in"><select id="tWall"></select></div><div class="pl-sw"><i id="swWall"></i><span id="szWall"></span><a class="pl-tlink" id="lkWall" target="_blank" rel="sponsored nofollow noopener" hidden>İncele ↗</a></div></div>
                </div>
                <div class="pl-fields c2" style="margin-top:10px">
                    <div class="pl-f"><label for="tH">Duvar seramiği</label><div class="pl-in"><select id="tH"><option value="0">Tavana kadar</option><option value="210">210 cm (üstü boya)</option><option value="120">120 cm (üstü boya)</option><option value="-1">Seramik yok, boya</option></select></div></div>
                    <div class="pl-f"><label for="tAcc">Vurgu duvarı</label><div class="pl-in"><select id="tAcc"></select></div></div>
                </div>
                <div class="pl-f" id="tAccF" style="margin-top:8px" hidden><label for="tAccT">Vurgu duvarı seramiği</label><div class="pl-in"><select id="tAccT"></select></div><div class="pl-sw"><i id="swAcc"></i><span id="szAcc"></span><a class="pl-tlink" id="lkAcc" target="_blank" rel="sponsored nofollow noopener" hidden>İncele ↗</a></div></div>
                <div class="pl-f" style="margin-top:10px"><label>Boya rengi (üst duvar ve tavan)</label><div class="pl-paints" id="plPaints"></div></div>
                <p class="pl-note">Seramikler 3D görünümde gerçek ebadıyla döşenir. Duvar harfleri (A, B, C…) planda duvarların iç tarafında yazar.</p>
            </section>

            <section class="pl-sec">
                <h2><i>4</i>Notlarım</h2>
                <textarea class="pl-notes" id="plNotes" maxlength="300" placeholder="Örn: lavabo pencerenin altında olsun, gömme rezervuar istiyorum"></textarea>
                <p class="pl-note">Notunuz plan bağlantısına ve PDF'e eklenir; teklif isterken ustaya iletilir.</p>
            </section>
        </aside>

        <section class="pl-stage">
            <div class="pl-canvas" id="plCanvas">
                <div id="plan2d"></div>
                <div id="plan3d" hidden><div class="pl-msg" id="msg3d">3D görünüm hazırlanıyor…</div></div>
                <div class="pl-warn" id="plWarn" hidden>Çakışan ya da sığmayan parça var</div>
                <div class="pl-tool" id="plTool" hidden role="toolbar" aria-label="Seçili parça">
                    <strong id="toolName"></strong>
                    <button type="button" id="tRot" title="Döndür (R)" aria-label="Döndür"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M20 12a8 8 0 1 1-2.3-5.6"/><path d="M20 4v5h-5"/></svg></button>
                    <button type="button" id="tEdit" title="Düzenle: ölçü ve ürün" aria-label="Düzenle"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 20h4L19 9l-4-4L4 16z"/><path d="M13 7l4 4"/></svg></button>
                    <button type="button" id="tCopy" title="Kopyala" aria-label="Kopyala"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="8" y="8" width="12" height="12" rx="2"/><path d="M16 8V5a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h3"/></svg></button>
                    <button type="button" id="tDel" title="Kaldır (Delete)" aria-label="Kaldır"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 7h16M9 7V4h6v3M6 7l1 13h10l1-13"/></svg></button>
                </div>
            </div>
            <p class="pl-hint" id="plHint"></p>
        </section>
    </div>

    <section class="pl-list" aria-labelledby="plListH">
        <h2 id="plListH">Planınızdaki ürünler</h2>
        <table class="pl-table">
            <thead><tr><th>No</th><th>Parça</th><th>Ölçü</th><th>Ürün</th></tr></thead>
            <tbody id="plRows"></tbody>
        </table>
        <div class="pl-sum" id="plSum" hidden></div>
        <p class="pl-legal">Plan yaklaşık bir yerleşim taslağıdır; tesisat yerleri, eğim ve uygulama ayrıntıları yerinde keşifte netleşir. "Örnek ürün" etiketli ürünler temsilîdir; sponsor markaların ürünleri eklendikçe burada listelenir.</p>
    </section>

    <div style="margin-top:18px"><x-sponsor.slot :tool="$isWc ? 'wc-planlayici' : 'banyo-planlayici'" :title="$isWc ? 'WC\'niz için markalar' : 'Banyonuz için markalar'" /></div>
</main>

<dialog class="pl-dlg" id="plDlg">
    <form method="dialog" id="dlgForm" novalidate>
        <h3 id="dlgTitle">Parçayı düzenle</h3>
        <p id="dlgText">Ölçüyü ve ürünü değiştirin; parça yerinde kalır.</p>
        <div class="pl-f" id="dlgProdF"><label for="dlgProd">Ürün</label><div class="pl-in"><select id="dlgProd"></select></div></div>
        <div class="pl-info" id="dlgInfo" hidden><img id="dlgImg" alt=""><div><b id="dlgBrand"></b><div id="dlgPrice"></div><a id="dlgLink" target="_blank" rel="sponsored nofollow noopener">Ürünü incele ↗</a></div></div>
        <div class="pl-fields c2">
            <div class="pl-f"><label for="dlgW">Genişlik</label><div class="pl-in"><input type="number" id="dlgW" step="5"><span>cm</span></div><small id="dlgWr"></small><button type="button" class="pl-fill" id="dlgFillW">↔ Boşluğu doldur</button></div>
            <div class="pl-f"><label for="dlgD">Derinlik</label><div class="pl-in"><input type="number" id="dlgD" step="5"><span>cm</span></div><small id="dlgDr"></small><button type="button" class="pl-fill" id="dlgFillD">↕ Boşluğu doldur</button></div>
        </div>
        <div class="pl-row">
            <button type="button" class="pl-btn" id="dlgCancel">Vazgeç</button>
            <button type="submit" class="pl-btn is-main" id="dlgOk" value="ok">Uygula</button>
        </div>
    </form>
</dialog>

<dialog class="pl-dlg" id="winDlg">
    <form method="dialog" id="winForm" novalidate>
        <h3 id="wnTitle">Pencere</h3>
        <p id="wnText">Ölçüleri girin; pencereyi mavi tutamacından duvar boyunca kaydırabilirsiniz.</p>
        <div class="pl-fields">
            <div class="pl-f"><label for="wnW">Genişlik</label><div class="pl-in"><input type="number" id="wnW" step="5"><span>cm</span></div><small>30–200 cm</small></div>
            <div class="pl-f"><label for="wnS">Denizlik</label><div class="pl-in"><input type="number" id="wnS" step="5"><span>cm</span></div><small>Yerden</small></div>
            <div class="pl-f"><label for="wnH">Yükseklik</label><div class="pl-in"><input type="number" id="wnH" step="5"><span>cm</span></div><small>20–200 cm</small></div>
        </div>
        <div class="pl-row">
            <button type="button" class="pl-btn" id="wnDel">Kaldır</button>
            <button type="submit" class="pl-btn is-main" value="ok">Uygula</button>
        </div>
    </form>
</dialog>

<dialog class="pl-dlg is-wide" id="aiDlg">
    <form method="dialog" id="aiForm" novalidate>
        <h3>Gerçekçi görsel (yapay zekâ)</h3>
        <p>Seçtiğiniz seramikler, boya ve ürünlerle fotoğraf gerçekliğinde bir banyo görseli oluşturulur. Telefonunuzla giriş yapınca günde 1 ücretsiz (filigranlı önizleme); kredi ile filigransız, tam çözünürlük.</p>
        <div class="pl-ai-tabs" role="group">
            <button type="button" id="aiPlan" aria-pressed="true">3D planımdan</button>
            <button type="button" id="aiPhoto" aria-pressed="false">Kendi banyomun fotoğrafından</button>
        </div>
        <div id="aiPhotoBox" hidden>
            <div class="pl-f"><label for="aiFile">Banyonuzun fotoğrafı (JPG/PNG, en çok 10 MB)</label><div class="pl-in"><input type="file" id="aiFile" accept="image/jpeg,image/png,image/webp"></div></div>
            <p class="pl-note">Fotoğraf yalnızca görseli oluşturmak için kullanılır. Kapı köşesinden, tüm odayı görecek şekilde çekin.</p>
        </div>
        <div class="pl-ai-tabs" role="group" aria-label="Görsel kalitesi" style="margin-top:8px">
            <button type="button" id="aiStd" aria-pressed="true">Standart <small>1 kredi · günde 1 ücretsiz önizleme</small></button>
            <button type="button" id="aiHd" aria-pressed="false">HD (yüksek çözünürlük) <small>2 kredi</small></button>
        </div>
        <div class="pl-f" style="margin-top:8px"><label for="aiNote">İsteğiniz (isteğe bağlı)</label><div class="pl-in"><input type="text" id="aiNote" maxlength="300" placeholder="Örn: sıcak ışık, ahşap dolap"></div></div>
        <div class="pl-ai-out" id="aiOut" hidden><img id="aiImg" alt="Yapay zekâ ile oluşturulan banyo görseli"><a class="pl-btn" id="aiDl" download="worksitem-banyo.jpg" style="margin-top:8px;justify-content:center">Görseli indir</a></div>
        <p class="pl-ai-msg" id="aiMsg">Yapay zekâ görselleri temsilîdir; ürünler birebir aynı görünmeyebilir.</p>
        <div class="pl-row">
            <button type="button" class="pl-btn" id="aiClose">Kapat</button>
            <button type="button" class="pl-btn is-main" id="aiGo">Görseli oluştur</button>
        </div>
    </form>
</dialog>

<div class="pl-copied" id="plCopied" hidden>Bağlantı kopyalandı</div>

<script type="application/json" id="plCfg">{!! json_encode($plannerCfg, $jsonFlags) !!}</script>
<script type="module">
const C = JSON.parse(document.getElementById('plCfg').textContent);
const $ = id => document.getElementById(id);
const NS = 'http://www.w3.org/2000/svg';
const LIM = C.limits, TYPES = C.types, T = 10;
const clamp = (v, a, b) => Math.max(a, Math.min(b, v));
let uid = 0;

/* ================= durum ================= */
const S = {
    shape: 'rect', w: C.initial.w, l: C.initial.l, h: C.initial.h,
    cw: Math.round(C.initial.w * 0.4 / 5) * 5, cl: Math.round(C.initial.l * 0.35 / 5) * 5,
    door: { e: null, off: null, w: C.doorW },
    floor: C.tileDefaults.floor, wall: C.tileDefaults.wall, items: [], sel: null, view: '2d',
    wins: [], notes: '',
    tileH: 0, paint: '#F7F7F5', acc: { e: -1, tile: null },
    under: null,   // kat planı altlığı: { url, w, x, y, op } — yalnızca bu cihazda
};
const PAINTS = [['#F7F7F5', 'Beyaz'], ['#F1ECE2', 'Kırık beyaz'], ['#E6DCCB', 'Bej'], ['#D9DCDF', 'Açık gri'], ['#C9D3C3', 'Adaçayı'], ['#B9CBD8', 'Açık mavi'], ['#4A4F57', 'Antrasit']];
const wallName = i => String.fromCharCode(65 + i);
let P = [], E = [];   // oda köşeleri (cm, saat yönünde) ve duvarlar

/* ================= oda geometrisi ================= */
function shapePoints(s, W, L, cw, cl) {
    switch (s) {
        case 'ltr': return [[0, 0], [W - cw, 0], [W - cw, cl], [W, cl], [W, L], [0, L]];
        case 'ltl': return [[cw, 0], [W, 0], [W, L], [0, L], [0, cl], [cw, cl]];
        case 'lbr': return [[0, 0], [W, 0], [W, L - cl], [W - cw, L - cl], [W - cw, L], [0, L]];
        case 'lbl': return [[0, 0], [W, 0], [W, L], [cw, L], [cw, L - cl], [0, L - cl]];
        case 'cham': return [[cw, 0], [W, 0], [W, L], [0, L], [0, cl]];
        default: return [[0, 0], [W, 0], [W, L], [0, L]];
    }
}
function buildRoom() {
    if (S.shape !== 'rect') {
        S.cw = clamp(S.cw, 30, S.w - 60); S.cl = clamp(S.cl, 30, S.l - 60);
    }
    P = shapePoints(S.shape, S.w, S.l, S.cw, S.cl);
    const n = P.length;
    E = P.map((a, i) => {
        const b = P[(i + 1) % n], dx = b[0] - a[0], dy = b[1] - a[1], len = Math.hypot(dx, dy);
        const d = [dx / len, dy / len], nn = [-d[1], d[0]];            // nn: odanın içine bakan normal
        const axis = Math.abs(dx) < 0.01 ? 'v' : (Math.abs(dy) < 0.01 ? 'h' : null);
        const cat = axis === 'h' ? (nn[1] > 0 ? 'N' : 'S') : axis === 'v' ? (nn[0] < 0 ? 'E' : 'W') : 'D';
        const lo = axis === 'h' ? Math.min(a[0], b[0]) : axis === 'v' ? Math.min(a[1], b[1]) : 0;
        const c = axis === 'h' ? a[1] : axis === 'v' ? a[0] : 0;
        return { i, a, b, len, d, n: nn, axis, cat, lo, c };
    });
    E.forEach((e, i) => { const p = E[(i - 1 + n) % n]; e.convexStart = p.d[0] * e.d[1] - p.d[1] * e.d[0] > 0; });
    E.forEach((e, i) => { e.convexEnd = E[(i + 1) % n].convexStart; });
    // kapı geçerli bir duvarda mı?
    const de = E[S.door.e];
    if (!de || !de.axis || de.len < S.door.w + 10) {
        const cand = E.filter(e => e.axis && e.len >= S.door.w + 10).sort((x, y) => (x.cat === 'S' ? 0 : 1) - (y.cat === 'S' ? 0 : 1) || y.len - x.len)[0];
        S.door.e = cand ? cand.i : 0; S.door.off = null;
    }
}
function area() { let s = 0; P.forEach((a, i) => { const b = P[(i + 1) % P.length]; s += a[0] * b[1] - b[0] * a[1]; }); return Math.abs(s) / 2; }
function inPoly(x, y) {
    let c = false;
    for (let i = 0, j = P.length - 1; i < P.length; j = i++) {
        const [xi, yi] = P[i], [xj, yj] = P[j];
        if ((yi > y) !== (yj > y) && x < (xj - xi) * (y - yi) / (yj - yi) + xi) c = !c;
    }
    return c;
}
function segHit(p1, p2, q1, q2) {
    const d = (p2[0] - p1[0]) * (q2[1] - q1[1]) - (p2[1] - p1[1]) * (q2[0] - q1[0]);
    if (Math.abs(d) < 1e-9) return false;
    const t = ((q1[0] - p1[0]) * (q2[1] - q1[1]) - (q1[1] - p1[1]) * (q2[0] - q1[0])) / d;
    const u = ((q1[0] - p1[0]) * (p2[1] - p1[1]) - (q1[1] - p1[1]) * (p2[0] - p1[0])) / d;
    return t > 0 && t < 1 && u > 0 && u < 1;
}
function rectInRoom(f) {
    const e = 0.6, x0 = f.x + e, y0 = f.y + e, x1 = f.x + f.w - e, y1 = f.y + f.d - e;
    const cs = [[x0, y0], [x1, y0], [x1, y1], [x0, y1]];
    if (!cs.every(p => inPoly(p[0], p[1]))) return false;
    if (P.some(([px, py]) => px > x0 && px < x1 && py > y0 && py < y1)) return false;
    for (const ed of E) if (!ed.axis) for (let k = 0; k < 4; k++) if (segHit(ed.a, ed.b, cs[k], cs[(k + 1) % 4])) return false;
    return true;
}
function bbox() { const xs = P.map(p => p[0]), ys = P.map(p => p[1]); return { x0: Math.min(...xs), y0: Math.min(...ys), x1: Math.max(...xs), y1: Math.max(...ys) }; }
function ray(x, y, dx, dy) {           // (x,y)'den duvara kadar mesafe
    let best = Infinity;
    for (const e of E) {
        const ex = e.b[0] - e.a[0], ey = e.b[1] - e.a[1], den = dx * ey - dy * ex;
        if (Math.abs(den) < 1e-9) continue;
        const t = ((e.a[0] - x) * ey - (e.a[1] - y) * ex) / den;
        const u = ((e.a[0] - x) * dy - (e.a[1] - y) * dx) / den;
        if (t > 0.01 && u >= -0.001 && u <= 1.001) best = Math.min(best, t);
    }
    return best;
}

/* ================= parçalar ================= */
function product(it) { const list = C.products[it.type] || []; return list.find(p => p.id === it.pid) || list[0]; }
function dims(it) { const p = product(it); return { w: it.w || p.w, d: it.d || p.d, h: p.h || S.h }; }
const isEl = it => !!(TYPES[it.type] && TYPES[it.type].element);
function foot(it) { const s = dims(it), flat = it.rot % 180 === 0; return { x: it.x, y: it.y, w: flat ? s.w : s.d, d: flat ? s.d : s.w }; }
function mk(type, pid) { const list = C.products[type] || []; return { uid: ++uid, type, pid: pid || (list[0] && list[0].id), x: 0, y: 0, rot: 0, w: 0, d: 0, bad: false }; }
function defaultItems() {
    const it = [], i = C.initial;
    if (i.shower) it.push(mk(i.shower));
    // sponsor ürünü varsa varsayılan o (liste sponsorlu önce sıralı)
    const pick = (type, ok) => ((C.products[type] || []).find(ok) || {}).id;
    it.push(mk('lavabo', pick('lavabo', p => !!p.cabinet === !!i.dolap)));
    const tank = !(i.rezervuar || !i.fromCalc);
    it.push(mk('klozet', pick('klozet', p => !!p.tank === tank)));
    if (i.havlupan) it.push(mk('havlupan'));
    return it;
}
const label = i => 'P' + String(i + 1).padStart(2, '0');

/* ================= kapı ================= */
function doorEdge() { return E[S.door.e]; }
function doorW() { const e = doorEdge(); return Math.min(S.door.w, e.len - 10); }
function doorOff() {
    const e = doorEdge(), w = doorW();
    const off = S.door.off == null ? e.len - w - 10 : S.door.off;
    return clamp(off, 0, Math.max(0, e.len - w));
}
function doorZone() {
    const e = doorEdge(), w = doorW(), o = e.lo + doorOff();
    switch (e.cat) {
        case 'N': return { x: o, y: e.c, w, d: w };
        case 'S': return { x: o, y: e.c - w, w, d: w };
        case 'W': return { x: e.c, y: o, w, d: w };
        default: return { x: e.c - w, y: o, w, d: w };
    }
}
function doorSeg() {            // kapı boşluğunun iki ucu (menteşe ilk nokta)
    const e = doorEdge(), w = doorW(), o = e.lo + doorOff();
    return e.axis === 'h' ? [[o, e.c], [o + w, e.c]] : [[e.c, o], [e.c, o + w]];
}

/* ================= pencereler ================= */
function winFix(wn) {
    let e = E[wn.e];
    if (!e || !e.axis || e.len < 40) { e = E.filter(x => x.axis && x.len >= 40).sort((a, b) => b.len - a.len)[0]; wn.e = e.i; }
    wn.w = clamp(wn.w, 30, Math.min(200, e.len - 10));
    wn.off = clamp(wn.off == null ? (e.len - wn.w) / 2 : wn.off, 0, e.len - wn.w);
    wn.sill = clamp(wn.sill, 0, S.h - 30); wn.h = clamp(wn.h, wn.k === 'nis' ? 15 : 20, Math.min(200, S.h - wn.sill - 5));
    return e;
}
function winSeg(wn) { const e = winFix(wn), o = e.lo + wn.off; return e.axis === 'h' ? [[o, e.c], [o + wn.w, e.c]] : [[e.c, o], [e.c, o + wn.w]]; }
function addWindow(kind) {
    if (S.wins.length >= 8) return;
    const de = S.door.e, pref = kind === 'nis' ? 'E' : 'N';
    const e = E.filter(x => x.axis && x.len >= 50).sort((a, b) => (a.i === de) - (b.i === de) || (a.cat === pref ? 0 : 1) - (b.cat === pref ? 0 : 1) || b.len - a.len)[0];
    S.wins.push(kind === 'nis' ? { k: 'nis', e: e.i, off: null, w: 60, sill: 110, h: 30 } : { e: e.i, off: null, w: C.window.w, sill: C.window.sill, h: C.window.h });
    changed();
}

/* ================= yerleşim ================= */
const PREF = { kabin: ['N', 'W', 'E', 'S'], kuvet: ['N', 'W', 'E', 'S'], gomme: ['N', 'W', 'E', 'S'], lavabo: ['N', 'E', 'W', 'S'], klozet: ['E', 'W', 'N', 'S'], havlupan: ['W', 'E', 'S', 'N'] };
const ROT = { N: 0, E: 90, S: 180, W: 270 };
const hit = (a, b, g = 0) => a.x < b.x + b.w + g && b.x < a.x + a.w + g && a.y < b.y + b.d + g && b.y < a.y + a.d + g;
function placeOn(it, e, t) {
    it.rot = ROT[e.cat];
    const f = foot(it);
    if (e.cat === 'N') { it.x = e.lo + t; it.y = e.c; }
    else if (e.cat === 'S') { it.x = e.lo + t; it.y = e.c - f.d; }
    else if (e.cat === 'W') { it.x = e.c; it.y = e.lo + t; }
    else { it.x = e.c - f.w; it.y = e.lo + t; }
}
function fits(it, others) {
    const f = foot(it);
    if (!rectInRoom(f) || hit(f, doorZone())) return false;
    return others.every(o => o === it || !hit(f, foot(o), 4));
}
function autoPlace(it, placed) {
    const order = PREF[it.type] || ['N', 'E', 'S', 'W'];
    const edges = E.filter(e => e.axis).sort((a, b) => order.indexOf(a.cat) - order.indexOf(b.cat) || b.len - a.len);
    const corner = TYPES[it.type] && TYPES[it.type].corner;
    for (const e of edges) {
        it.rot = ROT[e.cat];
        const f = foot(it), span = e.axis === 'h' ? f.w : f.d, max = e.len - span;
        if (max < 0) continue;
        const ts = [];
        if (corner) { if (e.convexStart) ts.push(0); if (e.convexEnd) ts.push(max); }
        for (let t = 0; t <= max; t += 5) ts.push(t);
        if (!ts.includes(max)) ts.push(max);
        for (const t of ts) { placeOn(it, e, t); if (fits(it, placed)) return true; }
    }
    const bb = bbox(), f = foot(it); it.rot = 0;
    it.x = (bb.x0 + bb.x1 - f.w) / 2; it.y = (bb.y0 + bb.y1 - f.d) / 2;
    return false;
}
function autoLayout() {
    const order = [...S.items].sort((a, b) => TYPES[a.type].order - TYPES[b.type].order);
    const placed = [];
    order.forEach(it => { autoPlace(it, placed); placed.push(it); });
}
function check() {
    S.items.forEach(it => {
        const f = foot(it);
        it.bad = !rectInRoom(f) || hit(f, doorZone()) || S.items.some(o => o !== it && hit(f, foot(o)));
    });
    $('plWarn').hidden = !S.items.some(it => it.bad);
}

/* ================= bağlantı ================= */
function encode() {
    const o = { v: 2, s: S.shape, w: S.w, l: S.l, h: S.h, cw: S.cw, cl: S.cl, d: [S.door.e, Math.round(doorOff()), S.door.w], f: S.floor, t: S.wall,
        i: S.items.map(it => [it.type, it.pid, Math.round(it.x), Math.round(it.y), it.rot, it.w || 0, it.d || 0]),
        wn: S.wins.map(w => [w.e, Math.round(w.off || 0), w.w, w.sill, w.h, w.k === 'nis' ? 1 : 0]), n: S.notes || undefined,
        st: [S.tileH, S.paint, S.acc.e, S.acc.tile] };
    return btoa(unescape(encodeURIComponent(JSON.stringify(o)))).replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');
}
function decode(s) {
    try {
        const o = JSON.parse(decodeURIComponent(escape(atob(s.replace(/-/g, '+').replace(/_/g, '/')))));
        if (!o || o.v !== 2) return false;
        S.shape = C.shapes[o.s] ? o.s : 'rect';
        S.w = clamp(+o.w || S.w, LIM.min, LIM.max); S.l = clamp(+o.l || S.l, LIM.min, LIM.max); S.h = clamp(+o.h || S.h, LIM.h_min, LIM.h_max);
        S.cw = +o.cw || S.cw; S.cl = +o.cl || S.cl;
        if (Array.isArray(o.d)) { S.door.e = +o.d[0] || 0; S.door.off = +o.d[1] || 0; S.door.w = clamp(+o.d[2] || C.doorW, 60, 120); }
        if (C.tiles[o.f]) S.floor = o.f;
        if (C.tiles[o.t]) S.wall = o.t;
        S.wins = (o.wn || []).slice(0, 8).map(a => ({ k: a[5] ? 'nis' : undefined, e: +a[0] || 0, off: +a[1] || 0, w: +a[2] || 60, sill: +a[3] || 0, h: +a[4] || 60 }));
        if (Array.isArray(o.st)) {
            S.tileH = [0, 210, 120, -1].includes(+o.st[0]) ? +o.st[0] : 0;
            S.paint = PAINTS.some(p => p[0] === o.st[1]) ? o.st[1] : S.paint;
            S.acc = { e: Number.isInteger(+o.st[2]) ? +o.st[2] : -1, tile: C.tiles[o.st[3]] ? o.st[3] : null };
        }
        S.notes = String(o.n || '').slice(0, 300);
        S.items = (o.i || []).filter(a => C.products[a[0]]).slice(0, 20).map(a => {
            const it = mk(a[0], a[1]); it.x = +a[2] || 0; it.y = +a[3] || 0; it.rot = [0, 90, 180, 270].includes(+a[4]) ? +a[4] : 0;
            const p = product(it); it.w = a[5] ? clamp(+a[5], p.wr[0], p.wr[1]) : 0; it.d = a[6] ? clamp(+a[6], p.dr[0], p.dr[1]) : 0; return it;
        });
        return true;
    } catch (e) { return false; }
}
let saveT = null;
function save() { clearTimeout(saveT); saveT = setTimeout(() => { try { history.replaceState(null, '', location.pathname + location.search + '#p=' + encode()); } catch (e) {} }, 250); }

/* ================= 2D çizim ================= */
function el(tag, attrs, parent) { const n = document.createElementNS(NS, tag); for (const k in attrs) n.setAttribute(k, attrs[k]); if (parent) parent.appendChild(n); return n; }
function txt(parent, x, y, s, attrs) { const t = el('text', { x, y, ...attrs }, parent); t.textContent = s; return t; }
function offsetPoly(pts, dist) {   // dışa doğru kaydırılmış çokgen (duvar dış çizgisi)
    const n = pts.length, lines = pts.map((a, i) => {
        const b = pts[(i + 1) % n], dx = b[0] - a[0], dy = b[1] - a[1], len = Math.hypot(dx, dy), nx = dy / len, ny = -dx / len;
        return [[a[0] + nx * dist, a[1] + ny * dist], [b[0] + nx * dist, b[1] + ny * dist]];
    });
    return lines.map((l, i) => {
        const m = lines[(i - 1 + n) % n];
        const [p1, p2] = m, [p3, p4] = l;
        const d = (p1[0] - p2[0]) * (p3[1] - p4[1]) - (p1[1] - p2[1]) * (p3[0] - p4[0]);
        if (Math.abs(d) < 1e-9) return p3;
        const a = p1[0] * p2[1] - p1[1] * p2[0], b = p3[0] * p4[1] - p3[1] * p4[0];
        return [(a * (p3[0] - p4[0]) - (p1[0] - p2[0]) * b) / d, (a * (p3[1] - p4[1]) - (p1[1] - p2[1]) * b) / d];
    });
}
const ptsStr = pts => pts.map(p => p.join(',')).join(' ');
function glyph(g, it, s) {
    const p = product(it), W = s.w, D = s.d, sel = S.sel === it.uid;
    const st = { stroke: it.bad ? '#D92D20' : (sel ? '#101318' : '#3A3F47'), 'stroke-width': it.bad || sel ? 2.4 : 1.2, fill: '#fff' };
    switch (it.type) {
        case 'kabin':
            el('rect', { x: 0, y: 0, width: W, height: D, ...st, fill: '#EAF3F8' }, g);
            el('path', { d: `M0 0L${W} ${D}M${W} 0L0 ${D}`, stroke: '#9CC3D8', 'stroke-width': 1, fill: 'none' }, g);
            el('circle', { cx: W / 2, cy: D / 2, r: 3.5, fill: '#9aa1ab' }, g); break;
        case 'gomme':
            el('rect', { x: 0, y: 0, width: W, height: D, ...st, fill: '#EAF3F8', 'stroke-dasharray': '6 4' }, g);
            el('line', { x1: 0, y1: D, x2: W * 0.65, y2: D, stroke: '#5E8FAA', 'stroke-width': 3 }, g);
            el('rect', { x: W / 2 - 15, y: D / 2 - 2, width: 30, height: 4, fill: '#9aa1ab' }, g); break;
        case 'kuvet':
            el('rect', { x: 0, y: 0, width: W, height: D, rx: 6, ...st }, g);
            el('rect', { x: 8, y: 8, width: W - 16, height: D - 16, rx: Math.min(24, (D - 16) / 2), fill: '#EAF3F8', stroke: '#9CC3D8', 'stroke-width': 1 }, g);
            el('circle', { cx: 20, cy: D / 2, r: 3, fill: '#9aa1ab' }, g); break;
        case 'lavabo':
            el('rect', { x: 0, y: 0, width: W, height: D, rx: 4, ...st, fill: p.cabinet ? '#F4EEE6' : '#fff' }, g);
            el('ellipse', { cx: W / 2, cy: D * 0.55, rx: Math.min(W * 0.32, 22), ry: D * 0.28, fill: '#EAF3F8', stroke: '#9CC3D8', 'stroke-width': 1 }, g);
            el('rect', { x: W / 2 - 2, y: 3, width: 4, height: 9, fill: '#9aa1ab' }, g); break;
        case 'klozet': {
            const tk = p.tank ? 18 : 8;
            el('rect', { x: 0, y: 0, width: W, height: tk, rx: 3, ...st }, g);
            el('ellipse', { cx: W / 2, cy: tk + (D - tk) / 2, rx: W / 2 - 2, ry: (D - tk) / 2 - 1, ...st }, g); break;
        }
        case 'icduvar':
            el('rect', { x: 0, y: 0, width: W, height: D, ...st, fill: '#B9BEC6' }, g); break;
        case 'engel':
            el('rect', { x: 0, y: 0, width: W, height: D, ...st, fill: '#C9CED6' }, g);
            el('path', { d: `M0 0L${W} ${D}M${W} 0L0 ${D}`, stroke: '#8B929C', 'stroke-width': 1, fill: 'none' }, g); break;
        case 'havlupan':
            el('rect', { x: 0, y: 0, width: W, height: D, rx: 2, ...st, fill: '#F1F3F5' }, g);
            for (let i = 1; i < 5; i++) el('line', { x1: i * W / 5, y1: 1, x2: i * W / 5, y2: D - 1, stroke: '#9aa1ab', 'stroke-width': 1 }, g);
            break;
    }
}
function transformFor(it) {
    const f = foot(it);
    if (it.rot === 90) return `translate(${f.x + f.w} ${f.y}) rotate(90)`;
    if (it.rot === 180) return `translate(${f.x + f.w} ${f.y + f.d}) rotate(180)`;
    if (it.rot === 270) return `translate(${f.x} ${f.y + f.d}) rotate(270)`;
    return `translate(${f.x} ${f.y})`;
}
let svg = null, sc = 1, VB = null;
const px = v => v / sc;
function dimLine(g, a, b, n, off, label, color) {   // n: dışa bakan birim vektör
    const ax = a[0] + n[0] * off, ay = a[1] + n[1] * off, bx = b[0] + n[0] * off, by = b[1] + n[1] * off, tk = px(5);
    el('path', { d: `M${ax} ${ay}L${bx} ${by}M${ax - n[0] * tk} ${ay - n[1] * tk}L${ax + n[0] * tk} ${ay + n[1] * tk}M${bx - n[0] * tk} ${by - n[1] * tk}L${bx + n[0] * tk} ${by + n[1] * tk}`, stroke: color, 'stroke-width': px(1.1), fill: 'none' }, g);
    let ang = Math.atan2(by - ay, bx - ax) * 180 / Math.PI; if (ang >= 90) ang -= 180; if (ang < -90) ang += 180;
    const mx = (ax + bx) / 2, my = (ay + by) / 2, w = px(label.length * 7.4 + 12), h = px(18);
    const lg = el('g', { transform: `translate(${mx} ${my}) rotate(${ang})` }, g);
    el('rect', { x: -w / 2, y: -h / 2, width: w, height: h, rx: px(5), fill: color === '#101318' ? '#fff' : color, stroke: color === '#101318' ? '#C9CED6' : 'none', 'stroke-width': px(1) }, lg);
    txt(lg, 0, px(4.5), label, { 'text-anchor': 'middle', 'font-size': px(12.5), 'font-weight': 700, fill: color === '#101318' ? '#101318' : '#fff' });
}
function draw2d() {
    const box = $('plan2d'); box.innerHTML = '';
    const bb = bbox(), m = 70;
    VB = { x: bb.x0 - m, y: bb.y0 - m, w: bb.x1 - bb.x0 + 2 * m, h: bb.y1 - bb.y0 + 2 * m };
    sc = Math.min((box.clientWidth || 700) / VB.w, (box.clientHeight || 560) / VB.h) || 1;
    svg = el('svg', { viewBox: `${VB.x} ${VB.y} ${VB.w} ${VB.h}`, role: 'img', 'aria-label': `Banyo planı ${S.w} × ${S.l} cm` }, box);
    const defs = el('defs', {}, svg), cp = el('clipPath', { id: 'roomClip' }, defs); el('polygon', { points: ptsStr(P) }, cp);
    // duvarlar ve zemin
    el('polygon', { points: ptsStr(offsetPoly(P, T)), fill: '#8B929C' }, svg);
    el('polygon', { points: ptsStr(P), fill: '#FAFAF8' }, svg);
    if (S.under) {   // kat planı altlığı
        const im = el('image', { x: S.under.x, y: S.under.y, width: S.under.w, opacity: S.under.op, preserveAspectRatio: 'xMinYMin meet', 'pointer-events': 'none' }, svg);
        im.setAttributeNS('http://www.w3.org/1999/xlink', 'href', S.under.url); im.setAttribute('href', S.under.url);
        if (S.under.h) im.setAttribute('height', S.under.w * S.under.h);
    }
    const tf = C.tiles[S.floor], gw = Math.max(10, tf.tw), gh = Math.max(10, tf.th);
    const grid = el('g', { stroke: '#E6E7EA', 'stroke-width': px(0.8), 'clip-path': 'url(#roomClip)' }, svg);
    for (let x = bb.x0 + gw; x < bb.x1; x += gw) el('line', { x1: x, y1: bb.y0, x2: x, y2: bb.y1 }, grid);
    for (let y = bb.y0 + gh; y < bb.y1; y += gh) el('line', { x1: bb.x0, y1: y, x2: bb.x1, y2: y }, grid);
    // duvar ölçüleri (her duvar)
    const dg = el('g', {}, svg);
    E.forEach(e => dimLine(dg, e.a, e.b, [-e.n[0], -e.n[1]], T + px(26), Math.round(e.len) + '', '#101318'));
    E.forEach((e, i) => {   // duvar harfleri ve vurgu duvarı
        const mx = (e.a[0] + e.b[0]) / 2 + e.n[0] * px(14), my = (e.a[1] + e.b[1]) / 2 + e.n[1] * px(14);
        if (S.acc.e === i && S.acc.tile) el('line', { x1: e.a[0] + e.n[0] * px(3), y1: e.a[1] + e.n[1] * px(3), x2: e.b[0] + e.n[0] * px(3), y2: e.b[1] + e.n[1] * px(3), stroke: '#FFB800', 'stroke-width': px(6), 'stroke-opacity': 0.75 }, dg);
        txt(dg, mx, my + px(4), wallName(i), { 'text-anchor': 'middle', 'font-size': px(12), 'font-weight': 800, fill: '#8B929C' });
    });
    // kapı (tut-sürükle)
    const e = doorEdge(), w = doorW(), [h0, h1] = doorSeg(), n = e.n, out = [-n[0], -n[1]];
    const dgp = el('g', { class: 'pl-door', 'aria-label': 'Kapı: sürükleyerek taşıyın' }, svg);
    el('polygon', { points: ptsStr([h0, h1, [h1[0] + out[0] * T, h1[1] + out[1] * T], [h0[0] + out[0] * T, h0[1] + out[1] * T]]), fill: '#FAFAF8' }, dgp);
    const leaf = [h0[0] + n[0] * w, h0[1] + n[1] * w], dd = [(h1[0] - h0[0]) / w, (h1[1] - h0[1]) / w];
    const sweep = (n[0] * dd[1] - n[1] * dd[0]) > 0 ? 1 : 0;
    el('line', { x1: h0[0], y1: h0[1], x2: leaf[0], y2: leaf[1], stroke: '#3A3F47', 'stroke-width': px(3) }, dgp);
    el('path', { d: `M${leaf[0]} ${leaf[1]}A${w} ${w} 0 0 ${sweep} ${h1[0]} ${h1[1]}`, fill: 'none', stroke: '#9aa1ab', 'stroke-width': px(1.2), 'stroke-dasharray': `${px(5)} ${px(4)}` }, dgp);
    const z = doorZone();
    el('rect', { x: z.x, y: z.y, width: z.w, height: z.d, fill: 'transparent' }, dgp);   // geniş tutma alanı
    const hx = (h0[0] + h1[0]) / 2 + out[0] * T / 2, hy = (h0[1] + h1[1]) / 2 + out[1] * T / 2;
    el('circle', { cx: hx, cy: hy, r: px(11), fill: '#FFB800', stroke: '#101318', 'stroke-width': px(1.5) }, dgp);
    el('path', { d: `M${hx - px(5)} ${hy}h${px(10)}M${hx} ${hy - px(5)}v${px(10)}`, stroke: '#101318', 'stroke-width': px(1.6), transform: e.axis === 'h' ? '' : '' }, dgp);
    dgp.addEventListener('pointerdown', startDoor);
    // pencereler (mavi tutamaç: sürükle; dokun: ölçü)
    S.wins.forEach((wn, k) => {
        const [a, b] = winSeg(wn), e = E[wn.e], o2 = [-e.n[0], -e.n[1]];
        const nis = wn.k === 'nis', col = nis ? '#8A6200' : '#2F6F8F';
        const wg = el('g', { class: 'pl-door', 'aria-label': (nis ? 'Niş' : 'Pencere') + ': sürükleyerek taşıyın' }, svg);
        const q = [a, b, [b[0] + o2[0] * T, b[1] + o2[1] * T], [a[0] + o2[0] * T, a[1] + o2[1] * T]];
        el('polygon', { points: ptsStr(q), fill: nis ? '#F3E6CC' : '#D7ECF7', stroke: col, 'stroke-width': px(1.2), 'stroke-dasharray': nis ? `${px(4)} ${px(3)}` : '' }, wg);
        if (!nis) el('line', { x1: a[0] + o2[0] * T / 2, y1: a[1] + o2[1] * T / 2, x2: b[0] + o2[0] * T / 2, y2: b[1] + o2[1] * T / 2, stroke: col, 'stroke-width': px(1.2) }, wg);
        const cx2 = (a[0] + b[0]) / 2 + o2[0] * (T + px(12)), cy2 = (a[1] + b[1]) / 2 + o2[1] * (T + px(12));
        el('circle', { cx: cx2, cy: cy2, r: px(10), fill: col, stroke: '#fff', 'stroke-width': px(1.5) }, wg);
        el('rect', { x: cx2 - px(4), y: cy2 - px(4), width: px(8), height: px(8), fill: 'none', stroke: '#fff', 'stroke-width': px(1.3) }, wg);
        wg.addEventListener('pointerdown', ev => startWin(ev, k));
    });
    // parçalar
    S.items.forEach((it, i) => {
        const s = dims(it);
        const g = el('g', { class: 'pl-it', 'data-uid': it.uid, transform: transformFor(it), tabindex: 0, role: 'button', 'aria-label': `${label(i)} ${TYPES[it.type].label}` }, svg);
        glyph(g, it, s);
        g.addEventListener('pointerdown', ev => startDrag(ev, it));
        g.addEventListener('keydown', ev => keyItem(ev, it));
    });
    S.items.forEach((it, i) => {
        const f = foot(it), tx = f.x + f.w / 2, ty = f.y + f.d / 2, tg = el('g', { 'pointer-events': 'none' }, svg);
        el('rect', { x: tx - px(17), y: ty - px(10), width: px(34), height: px(20), rx: px(6), fill: it.bad ? '#D92D20' : '#101318' }, tg);
        txt(tg, tx, ty + px(4.5), label(i), { 'text-anchor': 'middle', 'font-size': px(12), 'font-weight': 800, fill: '#fff' });
    });
    // seçili parçanın duvarlara uzaklığı
    const it = S.items.find(o => o.uid === S.sel);
    if (it) {
        const f = foot(it), g = el('g', { 'pointer-events': 'none' }, svg), col = '#C2410C';
        [[f.x, f.y + f.d / 2, -1, 0], [f.x + f.w, f.y + f.d / 2, 1, 0], [f.x + f.w / 2, f.y, 0, -1], [f.x + f.w / 2, f.y + f.d, 0, 1]].forEach(([x, y, dx, dy]) => {
            const t = ray(x, y, dx, dy);
            if (!isFinite(t) || t < 3) return;
            const b = [x + dx * t, y + dy * t];
            el('line', { x1: x, y1: y, x2: b[0], y2: b[1], stroke: col, 'stroke-width': px(1.2), 'stroke-dasharray': `${px(4)} ${px(3)}` }, g);
            const lb = Math.round(t) + '', w2 = px(lb.length * 7.4 + 12), h2 = px(18), mx = (x + b[0]) / 2, my = (y + b[1]) / 2;
            el('rect', { x: mx - w2 / 2, y: my - h2 / 2, width: w2, height: h2, rx: px(5), fill: col }, g);
            txt(g, mx, my + px(4.5), lb, { 'text-anchor': 'middle', 'font-size': px(12), 'font-weight': 700, fill: '#fff' });
        });
        // parçanın kendi ölçüsü
        const s = dims(it);
        dimLine(g, [f.x, f.y + f.d], [f.x + f.w, f.y + f.d], [0, 1], px(16), (it.rot % 180 === 0 ? s.w : s.d) + '', '#101318');
    }
    placeTool();
}

/* ================= etkileşim ================= */
let drag = null;
function pt(ev) { const p = svg.createSVGPoint(); p.x = ev.clientX; p.y = ev.clientY; return p.matrixTransform(svg.getScreenCTM().inverse()); }
function startDrag(ev, it) {
    ev.preventDefault(); ev.stopPropagation();
    const was = S.sel === it.uid;
    if (!was) { S.sel = it.uid; renderPanel(); }
    const p = pt(ev);
    drag = { kind: 'item', it, dx: p.x - it.x, dy: p.y - it.y, sx: ev.clientX, sy: ev.clientY, moved: false, was };
    $('plTool').hidden = true;
    window.addEventListener('pointermove', moveDrag);
    window.addEventListener('pointerup', endDrag, { once: true });
    draw2d(); $('plTool').hidden = true;
}
function snapItem(it, x, y) {
    const f = foot(it), s = px(14);
    for (const e of E) {
        if (e.axis === 'v' && y + f.d > e.lo && y < e.lo + e.len) {
            if (e.cat === 'W' && Math.abs(x - e.c) < s) x = e.c;
            if (e.cat === 'E' && Math.abs(x + f.w - e.c) < s) x = e.c - f.w;
        }
        if (e.axis === 'h' && x + f.w > e.lo && x < e.lo + e.len) {
            if (e.cat === 'N' && Math.abs(y - e.c) < s) y = e.c;
            if (e.cat === 'S' && Math.abs(y + f.d - e.c) < s) y = e.c - f.d;
        }
    }
    return [x, y];
}
function moveDrag(ev) {
    if (!drag) return;
    if (Math.hypot(ev.clientX - drag.sx, ev.clientY - drag.sy) > 4) drag.moved = true;
    if (!drag.moved) return;
    if (drag.kind === 'door') { moveDoor(ev); return; }
    if (drag.kind === 'win') { moveWin(ev); return; }
    const it = drag.it, p = pt(ev), f = foot(it), bb = bbox();
    let x = clamp(p.x - drag.dx, bb.x0, bb.x1 - f.w), y = clamp(p.y - drag.dy, bb.y0, bb.y1 - f.d);
    [x, y] = snapItem(it, x, y);
    it.x = Math.round(x); it.y = Math.round(y);
    check(); draw2d(); $('plTool').hidden = true;
}
function endDrag() {
    window.removeEventListener('pointermove', moveDrag);
    const d = drag; drag = null;
    if (d && d.kind === 'item' && !d.moved && d.was) { rotate(d.it); return; }   // seçili parçaya dokununca döner
    if (d && d.kind === 'win' && !d.moved) { openWin(d.k); return; }
    changed();
}
function startWin(ev, k) {
    ev.preventDefault(); ev.stopPropagation();
    drag = { kind: 'win', k, sx: ev.clientX, sy: ev.clientY, moved: false };
    window.addEventListener('pointermove', moveDrag);
    window.addEventListener('pointerup', endDrag, { once: true });
}
function moveWin(ev) {
    const p = pt(ev), wn = S.wins[drag.k];
    let best = null;
    for (const e of E) {
        if (!e.axis || e.len < wn.w + 10) continue;
        const along = e.axis === 'h' ? p.x : p.y, across = e.axis === 'h' ? p.y : p.x;
        const proj = clamp(along, e.lo, e.lo + e.len), dist = Math.hypot(along - proj, across - e.c);
        if (!best || dist < best.dist) best = { e, dist, along };
    }
    if (!best) return;
    wn.e = best.e.i; wn.off = Math.round(clamp(best.along - best.e.lo - wn.w / 2, 0, best.e.len - wn.w));
    draw2d();
}
let winK = null;
function openWin(k) {
    winK = k; const wn = S.wins[k];
    $('wnW').value = wn.w; $('wnS').value = wn.sill; $('wnH').value = wn.h;
    const nis = wn.k === 'nis';
    $('wnTitle').textContent = nis ? 'Niş' : 'Pencere';
    $('wnText').textContent = nis ? 'Duvara gömülü raf boşluğu (şampuan nişi). Kahverengi tutamacından duvar boyunca kaydırın.' : 'Ölçüleri girin; pencereyi mavi tutamacından duvar boyunca kaydırabilirsiniz.';
    $('winDlg').showModal();
}
$('winForm').addEventListener('submit', () => {
    const wn = S.wins[winK]; if (!wn) return;
    wn.w = Math.round(+$('wnW').value || wn.w); wn.sill = Math.round(+$('wnS').value || 0); wn.h = Math.round(+$('wnH').value || wn.h);
    winFix(wn); changed();
});
$('wnDel').addEventListener('click', () => { S.wins.splice(winK, 1); $('winDlg').close(); changed(); });
function startDoor(ev) {
    ev.preventDefault(); ev.stopPropagation();
    drag = { kind: 'door', sx: ev.clientX, sy: ev.clientY, moved: false };
    window.addEventListener('pointermove', moveDrag);
    window.addEventListener('pointerup', endDrag, { once: true });
}
function moveDoor(ev) {
    const p = pt(ev), w = S.door.w;
    let best = null;
    for (const e of E) {
        if (!e.axis || e.len < Math.min(w, 60) + 10) continue;
        const along = e.axis === 'h' ? p.x : p.y, across = e.axis === 'h' ? p.y : p.x;
        const proj = clamp(along, e.lo, e.lo + e.len), dist = Math.hypot(along - proj, across - e.c);
        if (!best || dist < best.dist) best = { e, dist, along };
    }
    if (!best) return;
    S.door.e = best.e.i;
    const ww = doorW();
    S.door.off = Math.round(clamp(best.along - best.e.lo - ww / 2, 0, best.e.len - ww));
    check(); draw2d();
}
function keyItem(ev, it) {
    const k = ev.key;
    if (k === 'r' || k === 'R') { rotate(it); ev.preventDefault(); return; }
    if (k === 'Delete' || k === 'Backspace') { remove(it); ev.preventDefault(); return; }
    const mv = { ArrowLeft: [-5, 0], ArrowRight: [5, 0], ArrowUp: [0, -5], ArrowDown: [0, 5] }[k];
    if (!mv) return;
    ev.preventDefault();
    const bb = bbox(), f = foot(it);
    it.x = clamp(it.x + mv[0], bb.x0, bb.x1 - f.w); it.y = clamp(it.y + mv[1], bb.y0, bb.y1 - f.d);
    S.sel = it.uid; changed();
}
function rotate(it) {
    const f0 = foot(it), cx = it.x + f0.w / 2, cy = it.y + f0.d / 2;
    it.rot = (it.rot + 90) % 360;
    const f = foot(it), bb = bbox();
    let x = clamp(Math.round(cx - f.w / 2), bb.x0, bb.x1 - f.w), y = clamp(Math.round(cy - f.d / 2), bb.y0, bb.y1 - f.d);
    [x, y] = snapItem(it, x, y); it.x = x; it.y = y;
    S.sel = it.uid; changed();
}
function remove(it) { S.items = S.items.filter(o => o !== it); if (S.sel === it.uid) S.sel = null; changed(); }
function copy(it) {
    if (S.items.length >= 12) return;
    const c = mk(it.type, it.pid); c.w = it.w; c.d = it.d;
    autoPlace(c, S.items); S.items.push(c); S.sel = c.uid; changed();
}
function placeTool() {
    const tool = $('plTool'), it = S.items.find(o => o.uid === S.sel);
    if (!it || S.view !== '2d' || !svg) { tool.hidden = true; return; }
    const g = svg.querySelector(`g.pl-it[data-uid="${it.uid}"]`); if (!g) { tool.hidden = true; return; }
    const r = g.getBoundingClientRect(), cr = $('plCanvas').getBoundingClientRect();
    const s = dims(it);
    $('toolName').textContent = `${TYPES[it.type].short} · ${s.w}×${s.d}`;
    tool.hidden = false;
    let x = r.left + r.width / 2 - cr.left, y = r.top - cr.top - 10;
    const tw = tool.offsetWidth;
    x = clamp(x, tw / 2 + 6, cr.width - tw / 2 - 6);
    if (y < tool.offsetHeight + 6) y = r.bottom - cr.top + tool.offsetHeight + 10;
    tool.style.left = x + 'px'; tool.style.top = y + 'px';
}
$('plCanvas').addEventListener('pointerdown', ev => {
    if (ev.target.closest('.pl-it, .pl-door, .pl-tool') || S.view !== '2d') return;
    if (S.sel != null) { S.sel = null; renderPanel(); draw2d(); }
});
const selIt = () => S.items.find(o => o.uid === S.sel);
$('tRot').addEventListener('click', () => { const it = selIt(); if (it) rotate(it); });
$('tCopy').addEventListener('click', () => { const it = selIt(); if (it) copy(it); });
$('tDel').addEventListener('click', () => { const it = selIt(); if (it) remove(it); });
$('tEdit').addEventListener('click', () => { const it = selIt(); if (it) openEdit(it); });

/* ================= düzenleme penceresi ================= */
let editIt = null;
function fillSpan(it, axis) {   // parçayı duvar boyunca / derinlemesine boşluğu dolduracak kadar büyüt
    const p = product(it), r = axis === 'w' ? p.wr : p.dr, keep = { w: it.w, d: it.d };
    let best = axis === 'w' ? dims(it).w : dims(it).d;
    for (let v = r[1]; v >= r[0]; v -= 5) {
        it[axis] = v;
        if (fits(it, S.items)) { best = v; break; }
    }
    it.w = keep.w; it.d = keep.d;
    return best;
}
function openEdit(it) {
    editIt = it;
    const p = product(it), s = dims(it);
    $('dlgTitle').textContent = `${TYPES[it.type].label} düzenle`;
    const sel = $('dlgProd'); sel.innerHTML = '';
    (C.products[it.type] || []).forEach(q => { const o = document.createElement('option'); o.value = q.id; o.textContent = q.name + (q.sponsor ? ' · ' + q.sponsor : ' · Örnek ürün'); o.selected = q.id === it.pid; sel.appendChild(o); });
    $('dlgProdF').hidden = isEl(it);
    setRanges(p, s); prodInfo(p);
    $('plDlg').showModal();
}
function prodInfo(p) {
    const box = $('dlgInfo'); box.hidden = !p.sponsor;
    if (!p.sponsor) return;
    $('dlgBrand').textContent = p.sponsor + ' · Sponsorlu';
    $('dlgPrice').textContent = p.price ? `₺${p.price[0].toLocaleString('tr-TR')} – ₺${p.price[1].toLocaleString('tr-TR')}` : '';
    $('dlgImg').hidden = !p.image; if (p.image) $('dlgImg').src = p.image;
    $('dlgLink').hidden = !p.link; if (p.link) $('dlgLink').href = p.link;
}
function setRanges(p, s) {
    $('dlgW').value = s.w; $('dlgW').min = p.wr[0]; $('dlgW').max = p.wr[1]; $('dlgWr').textContent = `${p.wr[0]}–${p.wr[1]} cm`;
    $('dlgD').value = s.d; $('dlgD').min = p.dr[0]; $('dlgD').max = p.dr[1]; $('dlgDr').textContent = `${p.dr[0]}–${p.dr[1]} cm`;
    $('dlgFillD').hidden = p.dr[0] === p.dr[1];
}
$('dlgProd').addEventListener('change', e => { if (!editIt) return; const q = (C.products[editIt.type] || []).find(x => x.id === e.target.value); if (q) { setRanges(q, { w: q.w, d: q.d }); prodInfo(q); } });
$('dlgFillW').addEventListener('click', () => { if (!editIt) return; const keep = editIt.pid; editIt.pid = $('dlgProd').value; editIt.d = +$('dlgD').value || 0; $('dlgW').value = fillSpan(editIt, 'w'); editIt.pid = keep; });
$('dlgFillD').addEventListener('click', () => { if (!editIt) return; const keep = editIt.pid; editIt.pid = $('dlgProd').value; editIt.w = +$('dlgW').value || 0; $('dlgD').value = fillSpan(editIt, 'd'); editIt.pid = keep; });
$('dlgCancel').addEventListener('click', () => $('plDlg').close());
$('dlgForm').addEventListener('submit', () => {
    const it = editIt; if (!it) return;
    it.pid = $('dlgProd').value;
    const p = product(it);
    it.w = clamp(Math.round(+$('dlgW').value || p.w), p.wr[0], p.wr[1]);
    it.d = clamp(Math.round(+$('dlgD').value || p.d), p.dr[0], p.dr[1]);
    if (it.w === p.w) it.w = 0; if (it.d === p.d) it.d = 0;
    const f = foot(it), bb = bbox();
    it.x = clamp(it.x, bb.x0, bb.x1 - f.w); it.y = clamp(it.y, bb.y0, bb.y1 - f.d);
    [it.x, it.y] = snapItem(it, it.x, it.y);
    editIt = null; changed();
});

/* ================= panel ================= */
const SHAPE_ICON = { rect: 'M4 4H36V28H4Z', ltr: 'M4 4H24V13H36V28H4Z', ltl: 'M16 4H36V28H4V13H16Z', lbr: 'M4 4H36V19H24V28H4Z', lbl: 'M4 4H36V28H16V19H4Z', cham: 'M14 4H36V28H4V14Z' };
function buildShapes() {
    const box = $('plShapes');
    Object.entries(C.shapes).forEach(([k, lb]) => {
        const b = document.createElement('button'); b.type = 'button'; b.className = 'pl-shape'; b.dataset.shape = k;
        b.innerHTML = `<svg viewBox="0 0 40 32" aria-hidden="true"><path d="${SHAPE_ICON[k]}" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linejoin="round"/></svg><span></span>`;
        b.querySelector('span').textContent = lb;
        b.addEventListener('click', () => { if (S.shape === k) return; S.shape = k; S.door.e = null; buildRoom(); autoLayout(); changed(); });
        box.appendChild(b);
    });
}
function renderPanel() {
    $('inW').value = S.w; $('inL').value = S.l; $('inH').value = S.h; $('inCW').value = S.cw; $('inCL').value = S.cl; $('inDoorW').value = S.door.w;
    $('plCut').hidden = S.shape === 'rect';
    document.querySelectorAll('.pl-shape').forEach(b => b.setAttribute('aria-pressed', b.dataset.shape === S.shape));
    $('plDims').innerHTML = `<b>${S.w} × ${S.l} cm</b> · ${(area() / 10000).toLocaleString('tr-TR', { maximumFractionDigits: 2 })} m² · yükseklik ${S.h} cm`;
    const ul = $('plItems'); ul.innerHTML = '';
    S.items.forEach((it, i) => {
        const p = product(it), s = dims(it), li = document.createElement('li');
        li.className = 'pl-item' + (S.sel === it.uid ? ' is-sel' : '') + (it.bad ? ' is-bad' : '');
        li.innerHTML = `<b>${label(i)}</b><span>${TYPES[it.type].label}<small></small></span>`;
        li.querySelector('small').textContent = `${p.name} · ${s.w}×${s.d} cm` + (it.bad ? ' · yerleşimi kontrol edin' : '');
        li.addEventListener('click', () => { S.sel = it.uid; renderPanel(); if (S.view === '2d') draw2d(); });
        li.addEventListener('dblclick', () => openEdit(it));
        ul.appendChild(li);
    });
    const rows = $('plRows'); rows.innerHTML = '';
    S.items.forEach((it, i) => {
        const p = product(it), s = dims(it), tr = document.createElement('tr');
        tr.innerHTML = '<td><b></b></td><td></td><td></td><td><span></span> <span class="pl-tag"></span></td>';
        tr.children[0].firstChild.textContent = label(i);
        tr.children[1].textContent = TYPES[it.type].label;
        tr.children[2].textContent = `${s.w} × ${s.d} × ${s.h} cm`;
        tr.children[3].firstChild.textContent = p.name + (p.price ? ` (₺${p.price[0].toLocaleString('tr-TR')}–${p.price[1].toLocaleString('tr-TR')})` : '');
        if (isEl(it)) tr.querySelector('.pl-tag').remove();
        const tag = tr.querySelector('.pl-tag'); if (tag) { tag.textContent = p.sponsor ? p.sponsor : 'Örnek ürün'; if (p.sponsor) tag.classList.add('is-sp'); }
        rows.appendChild(tr);
    });
    // seramik / boya kontrolleri
    $('tH').value = String(S.tileH);
    const accSel = $('tAcc');
    accSel.innerHTML = '<option value="-1">Yok</option>' + E.map((e, i) => `<option value="${i}">${wallName(i)} duvarı (${Math.round(e.len)} cm)</option>`).join('');
    if (S.acc.e >= E.length) S.acc.e = -1;
    accSel.value = String(S.acc.e);
    $('tAccF').hidden = S.acc.e < 0;
    if (S.acc.e >= 0) { const at = S.acc.tile || S.wall; $('tAccT').value = at; $('swAcc').style.backgroundImage = `url('${C.tiles[at].url}')`; $('szAcc').textContent = C.tiles[at].size + ' cm'; }
    document.querySelectorAll('#plPaints button').forEach(b => b.setAttribute('aria-pressed', b.dataset.c === S.paint));
    planLinks();
    // sponsor ürün fiyatları toplamı
    const priced = S.items.map(product).filter(p => p && p.price);
    const tc = tileCosts();
    const sum = priced.reduce((a, p) => [a[0] + p.price[0], a[1] + p.price[1]], [tc.lo, tc.hi]);
    const nTile = tc.n;
    const tl = v => '₺' + v.toLocaleString('tr-TR');
    $('plSum').hidden = !(priced.length || nTile);
    if (priced.length || nTile) $('plSum').innerHTML = `Seçili sponsor ürünlerinin fiyatı: ${tl(sum[0])} – ${tl(sum[1])}<small>${priced.length ? priced.length + ' ürün' : ''}${priced.length && nTile ? ' + ' : ''}${nTile ? `seramik (${tc.m2.toLocaleString('tr-TR', { maximumFractionDigits: 1 })} m², %10 fire dahil)` : ''}; marka fiyatlarıdır, işçilik ve diğer kalemler maliyet hesabındadır.</small>`;
    if (document.activeElement !== $('plNotes')) $('plNotes').value = S.notes || '';
    [['tFloor', 'swFloor', 'szFloor', S.floor, 'lkFloor'], ['tWall', 'swWall', 'szWall', S.wall, 'lkWall']].forEach(([s, sw, sz, v, lk]) => {
        const t = C.tiles[v];
        $(s).value = v; $(sw).style.backgroundImage = `url('${t.url}')`;
        $(sz).textContent = t.size + ' cm' + (t.brand ? ' · ' + t.brand : '') + (t.price ? ` · ₺${t.price[0].toLocaleString('tr-TR')}–${t.price[1].toLocaleString('tr-TR')}/m²` : '');
        $(lk).hidden = !t.link; if (t.link) $(lk).href = t.link;
    });
    if (S.acc.e >= 0) { const at = C.tiles[S.acc.tile || S.wall]; $('lkAcc').hidden = !at.link; if (at.link) $('lkAcc').href = at.link; }
}
function fillTileSelects() {
    PAINTS.forEach(([c, n]) => {
        const b = document.createElement('button'); b.type = 'button'; b.dataset.c = c; b.title = n; b.setAttribute('aria-label', n); b.style.background = c;
        b.addEventListener('click', () => { S.paint = c; changed(); });
        $('plPaints').appendChild(b);
    });
    ['tFloor', 'tWall', 'tAccT'].forEach(id => {
        const s = $(id), groups = {};
        Object.entries(C.tiles).forEach(([k, t]) => { (groups[t.style] = groups[t.style] || []).push([k, t]); });
        Object.entries(groups).forEach(([g, arr]) => {
            const og = document.createElement('optgroup'); og.label = g;
            arr.forEach(([k, t]) => { const o = document.createElement('option'); o.value = k; o.textContent = `${t.name} ${t.size}`; og.appendChild(o); });
            s.appendChild(og);
        });
    });
}
function addButtons() {
    [['kabin', '+ Duşakabin'], ['gomme', '+ Gömme duş'], ['kuvet', '+ Küvet'], ['lavabo', '+ Lavabo'], ['klozet', '+ Klozet'], ['havlupan', '+ Havlupan']].forEach(([t, s]) => {
        const b = document.createElement('button'); b.type = 'button'; b.textContent = s;
        b.addEventListener('click', () => { if (S.items.length >= 12) return; const it = mk(t); autoPlace(it, S.items); S.items.push(it); S.sel = it.uid; changed(); });
        $('plAdd').appendChild(b);
    });
}

/* ================= değişiklik ================= */
function changed() { check(); renderPanel(); if (S.view === '2d') draw2d(); save(); if (S.view === '3d') schedule3d(); }
function readDims() {
    const v = (id, a, b, d) => { const n = parseFloat($(id).value); return isFinite(n) ? clamp(Math.round(n), a, b) : d; };
    const w = v('inW', LIM.min, LIM.max, S.w), l = v('inL', LIM.min, LIM.max, S.l), h = v('inH', LIM.h_min, LIM.h_max, S.h);
    const cw = v('inCW', 30, 900, S.cw), cl = v('inCL', 30, 900, S.cl), dw = v('inDoorW', 60, 120, S.door.w);
    const relayout = w !== S.w || l !== S.l || cw !== S.cw || cl !== S.cl || dw !== S.door.w;
    Object.assign(S, { w, l, h, cw, cl }); S.door.w = dw;
    buildRoom();
    if (relayout) { S.door.off = null; autoLayout(); }
    changed();
}
['inW', 'inL', 'inH', 'inCW', 'inCL', 'inDoorW'].forEach(id => $(id).addEventListener('change', readDims));
$('plAuto').addEventListener('click', () => { autoLayout(); changed(); });
document.querySelectorAll('#plEls button').forEach(b => b.addEventListener('click', () => {
    const t = b.dataset.el;
    if (t === 'win' || t === 'nis') { addWindow(t); return; }
    if (S.items.length >= 14) return;
    const it = mk(t); autoPlace(it, S.items); S.items.push(it); S.sel = it.uid; changed();
}));
$('plNotes').addEventListener('input', e => { S.notes = e.target.value.slice(0, 300); save(); });
$('plPrint').addEventListener('click', () => {
    const keepSel = S.sel; S.sel = null; draw2d();
    const svgHtml = svg.outerHTML.replace('<svg ', '<svg style="width:100%;height:auto;max-height:150mm" ');
    S.sel = keepSel; if (S.view === '2d') draw2d();
    try { history.replaceState(null, '', location.pathname + location.search + '#p=' + encode()); } catch (e) {}
    const esc = v => String(v).replace(/[&<>"]/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]));
    const rows = S.items.map((it, i) => { const p = product(it), d = dims(it); return `<tr><td>${label(i)}</td><td>${esc(TYPES[it.type].label)}</td><td>${d.w} × ${d.d} × ${d.h} cm</td><td>${esc(p.name)}${isEl(it) ? '' : ' · ' + esc(p.sponsor || 'Örnek ürün')}</td></tr>`; }).join('');
    const w = window.open('', '_blank');
    if (!w) { alert('Açılır pencere engellendi; tarayıcıda bu site için açılır pencerelere izin verin.'); return; }
    w.document.write(`<!DOCTYPE html><html lang="tr"><head><meta charset="utf-8"><title>Banyo planım · Worksitem</title><style>
        body{font-family:Inter,Arial,sans-serif;color:#101318;margin:18mm}h1{font-size:20px;margin:0 0 4px}p{font-size:12px;color:#5B6472;margin:0 0 10px}
        table{width:100%;border-collapse:collapse;font-size:12px;margin-top:10px}td,th{border-bottom:1px solid #E3E6EA;padding:6px;text-align:left}
        .brand{display:flex;justify-content:space-between;align-items:center;border-bottom:3px solid #FFB800;padding-bottom:8px;margin-bottom:12px}
        .brand b{font-size:18px}.note{border:1px solid #E3E6EA;border-radius:8px;padding:8px;font-size:12px;margin-top:10px;white-space:pre-wrap}
        </style></head><body><div class="brand"><b>worksitem · Banyo planı</b><span style="font-size:12px">${new Date().toLocaleDateString('tr-TR')}</span></div>
        <h1>${S.w} × ${S.l} cm · ${(area() / 10000).toLocaleString('tr-TR', { maximumFractionDigits: 2 })} m² · yükseklik ${S.h} cm</h1>
        <p>${esc(C.shapes[S.shape])} oda · zemin: ${esc(C.tiles[S.floor].name)} ${C.tiles[S.floor].size} · duvar: ${S.tileH < 0 ? 'boya' : esc(C.tiles[S.wall].name) + ' ' + C.tiles[S.wall].size + (S.tileH > 0 ? ` (${S.tileH} cm, üstü boya)` : ' (tavana kadar)')}${S.acc.e >= 0 && S.acc.tile ? ` · vurgu duvarı ${wallName(S.acc.e)}: ${esc(C.tiles[S.acc.tile].name)}` : ''} · boya: ${esc((PAINTS.find(p => p[0] === S.paint) || ['', ''])[1])}${S.wins.length ? ` · ${S.wins.filter(w => w.k !== 'nis').length} pencere, ${S.wins.filter(w => w.k === 'nis').length} niş` : ''}</p>
        ${svgHtml}
        <table><thead><tr><th>No</th><th>Parça</th><th>Ölçü</th><th>Ürün</th></tr></thead><tbody>${rows}</tbody></table>
        ${S.notes ? `<div class="note"><b>Notlarım:</b> ${esc(S.notes)}</div>` : ''}
        <p style="margin-top:12px;word-break:break-all">Plan bağlantısı: ${esc(location.href)}</p>
        <p>Bu plan yaklaşık bir yerleşim taslağıdır; tesisat, eğim ve uygulama ayrıntıları yerinde keşifte netleşir. Kainta İnşaat · WhatsApp destek 0850 346 49 83</p>
        <script>window.onload=function(){setTimeout(function(){window.print()},300)}<\/script><\/body><\/html>`);
    w.document.close();
});
$('tFloor').addEventListener('change', e => { S.floor = e.target.value; changed(); });
$('tWall').addEventListener('change', e => { S.wall = e.target.value; changed(); });
$('tH').addEventListener('change', e => { S.tileH = +e.target.value; changed(); });
$('tAcc').addEventListener('change', e => { S.acc.e = +e.target.value; if (S.acc.e >= 0 && !S.acc.tile) S.acc.tile = Object.keys(C.tiles).find(k => k !== S.wall); changed(); });
$('tAccT').addEventListener('change', e => { S.acc.tile = e.target.value; changed(); });

/* ================= sponsor seramik maliyeti ================= */
function tileCosts() {   // m² fiyatı olan sponsor seramikleri × alan (%10 fire)
    let lo = 0, hi = 0, n = 0, m2 = 0;
    const add = (id, a) => { const t = C.tiles[id]; if (!t || !t.price || a <= 0) return; lo += t.price[0] * a * 1.1; hi += t.price[1] * a * 1.1; m2 += a * 1.1; n++; };
    add(S.floor, area() / 10000);
    if (S.tileH >= 0) {
        const th = (S.tileH > 0 ? Math.min(S.tileH, S.h) : S.h) / 100;
        E.forEach((e, i) => {
            let a = e.len / 100 * th;
            if (i === S.door.e) a -= doorW() / 100 * Math.min(2.1, th);
            S.wins.forEach(w => { if (w.e === i) a -= w.w / 100 * Math.max(0, Math.min(th, (w.sill + w.h) / 100) - Math.min(th, w.sill / 100)); });
            add(S.acc.e === i && S.acc.tile ? S.acc.tile : S.wall, Math.max(0, a));
        });
    }
    return { lo: Math.round(lo / 100) * 100, hi: Math.round(hi / 100) * 100, n, m2 };
}

/* ================= maliyet hesabına aktarım ================= */
function planLinks() {
    // plan → banyo hesaplayıcısı: gerçek alan, duş tipi ve seçimler
    const bb = bbox(), W = (bb.x1 - bb.x0), A = area();
    const q = new URLSearchParams(C.query || {});
    const shower = (S.items.find(it => ['kabin', 'kuvet', 'gomme'].includes(it.type)) || {}).type;
    const lav = S.items.find(it => it.type === 'lavabo'), kl = S.items.find(it => it.type === 'klozet');
    q.set('width', (W / 100).toFixed(2)); q.set('length', (A / W / 100).toFixed(2)); q.set('height', (S.h / 100).toFixed(2));
    if (shower) { q.set('shower', shower); if (q.get('bathType') === 'wc') q.set('bathType', 'standart'); } else { q.delete('shower'); }
    q.set('ex_dolap', lav && product(lav).cabinet ? '1' : '0');
    q.set('ex_havlupan', S.items.some(it => it.type === 'havlupan') ? '1' : '0');
    if (kl) q.set('ex_rezervuar', product(kl).tank ? '0' : '1');
    const priced = S.items.map(product).filter(p => p && p.price), tc = tileCosts();
    if (priced.length || tc.n) { q.set('plan_low', priced.reduce((a, p) => a + p.price[0], tc.lo)); q.set('plan_high', priced.reduce((a, p) => a + p.price[1], tc.hi)); q.set('plan_n', priced.length + (tc.n ? 1 : 0)); }
    else ['plan_low', 'plan_high', 'plan_n'].forEach(k => q.delete(k));
    q.set('hesapla', '1');
    // WC planı → WC hesap motoru (alan adları: w, l, h, extras[]); duş eklenirse banyo hesabına gider
    let base = (C.variant === 'wc' && shower ? C.bathCalc : C.back).split('?')[0];
    if (C.variant === 'wc' && !shower) {
        const k = new URLSearchParams(C.back.split('?')[1] || '');
        k.set('w', q.get('width')); k.set('l', q.get('length')); k.set('h', q.get('height'));
        ['ex_dolap', 'ex_rezervuar', 'ex_havlupan'].forEach(x => { if (q.get(x) === '1') k.append('extras[]', x); });
        q.forEach((v, x) => { if (x.startsWith('plan_')) k.set(x, v); });
        $('plBack').href = base + '?' + k.toString(); $('plQuote').href = base + '?' + k.toString() + '#teklif';
        return;
    }
    $('plBack').href = base + '?' + q.toString();
    $('plQuote').href = base + '?' + q.toString() + '#teklif';
}
['plBack', 'plQuote'].forEach(id => $(id).addEventListener('click', () => {
    try { history.replaceState(null, '', location.pathname + location.search + '#p=' + encode()); sessionStorage.setItem('ws_bath_plan', location.href); } catch (e) {}
}));
$('plCopy').addEventListener('click', async () => {
    try { history.replaceState(null, '', location.pathname + location.search + '#p=' + encode()); } catch (e) {}
    try { await navigator.clipboard.writeText(location.href); } catch (e) { window.prompt('Bağlantıyı kopyalayın:', location.href); return; }
    $('plCopied').hidden = false; setTimeout(() => { $('plCopied').hidden = true; }, 1800);
});


/* ================= görünüm ================= */
const HINT2D = 'Parçaya dokunun: araç çubuğu açılır; seçili parçaya tekrar dokunmak 90° döndürür. Sürükleyin, duvara yaklaşınca yaslanır. Sarı tutamaçlı kapıyı duvar boyunca kaydırın. Klavye: R döndürür, Delete kaldırır, oklar 5 cm kaydırır.';
const HINT3D = 'Sürükleyerek odanın etrafında dönün, tekerlek ya da iki parmakla yakınlaşın. Kameraya bakan duvarlar içeriyi görebilmeniz için gizlenir.';
function setView(v) {
    S.view = v;
    $('tab2d').setAttribute('aria-pressed', v === '2d'); $('tab3d').setAttribute('aria-pressed', v === '3d');
    $('plan2d').hidden = v !== '2d'; $('plan3d').hidden = v !== '3d';
    $('plHint').textContent = v === '2d' ? HINT2D : HINT3D;
    if (v === '3d') { $('plTool').hidden = true; schedule3d(true); } else draw2d();
}
$('tab2d').addEventListener('click', () => setView('2d'));
$('tab3d').addEventListener('click', () => setView('3d'));
if ('ResizeObserver' in window) { let rt = null; new ResizeObserver(() => { clearTimeout(rt); rt = setTimeout(() => { if (S.view === '2d') draw2d(); }, 80); }).observe($('plan2d')); }

/* ================= 3D (ilk açılışta yüklenir) ================= */
let T3 = null, t3timer = null;
function schedule3d(now) { clearTimeout(t3timer); t3timer = setTimeout(build3d, now ? 0 : 150); }
async function init3d() {
    if (T3) return T3;
    let THREE, OrbitControls;
    let GLTFLoader = null;
    try { THREE = await import('three'); ({ OrbitControls } = await import('three/addons/controls/OrbitControls.js')); }
    catch (e) { $('msg3d').textContent = '3D görünüm yüklenemedi. İnternet bağlantınızı kontrol edip sayfayı yenileyin; 2D plan kullanılabilir.'; return null; }
    const box = $('plan3d');
    const renderer = new THREE.WebGLRenderer({ antialias: true });
    renderer.setPixelRatio(Math.min(2, window.devicePixelRatio || 1));
    renderer.outputColorSpace = THREE.SRGBColorSpace;
    box.innerHTML = ''; box.appendChild(renderer.domElement);
    const scene = new THREE.Scene(); scene.background = new THREE.Color(0xE9ECEF);
    const camera = new THREE.PerspectiveCamera(45, 1, 0.05, 100);
    const controls = new OrbitControls(camera, renderer.domElement);
    controls.enableDamping = true; controls.maxPolarAngle = Math.PI * 0.49; controls.minDistance = 1; controls.maxDistance = 16;
    // aydınlık banyo ışığı: ortam + gök + iki yönlü + tavan
    scene.add(new THREE.AmbientLight(0xffffff, 0.85));
    scene.add(new THREE.HemisphereLight(0xffffff, 0xd0d0d0, 1.0));
    const sun = new THREE.DirectionalLight(0xffffff, 1.1); sun.position.set(2, 5, 3); scene.add(sun);
    const fill = new THREE.DirectionalLight(0xffffff, 0.5); fill.position.set(-3, 4, -2); scene.add(fill);
    const lamp = new THREE.PointLight(0xfff6e8, 1.2, 0, 0); lamp.position.set(0, S.h / 100 - 0.1, 0); scene.add(lamp);
    const loader = new THREE.TextureLoader(), cache = {}, failed = new Set();
    const tex = id => {
        if (!cache[id]) {
            const t = loader.load(C.tiles[id].url, () => render(), undefined, () => { failed.add(id); schedule3d(); });
            t.colorSpace = THREE.SRGBColorSpace; t.wrapS = t.wrapT = THREE.RepeatWrapping; t.anisotropy = 4; cache[id] = t;
        }
        return cache[id];
    };
    const root = new THREE.Group(); scene.add(root);
    const walls = [];
    function resize() { const w = box.clientWidth || 600, h = box.clientHeight || 400; renderer.setSize(w, h, false); camera.aspect = w / h; camera.updateProjectionMatrix(); render(); }
    function render() {
        walls.forEach(wl => { const v = new THREE.Vector3().subVectors(camera.position, wl.p); wl.g.visible = v.dot(wl.n) < 0; });
        if (T3 && T3.ceil) T3.ceil.visible = camera.position.y < S.h / 100 - 0.05;
        renderer.render(scene, camera);
    }
    controls.addEventListener('change', render);
    (function loop() { requestAnimationFrame(loop); if (S.view === '3d' && controls.update()) render(); })();
    new ResizeObserver(resize).observe(box);
    // sponsor ürünlerinin 3D modelleri (GLB) — yüklenemezse sade şekil çizilir
    try { ({ GLTFLoader } = await import('three/addons/loaders/GLTFLoader.js')); } catch (e) { GLTFLoader = null; }
    const models = {};
    const model = url => {
        if (!GLTFLoader || !url) return null;
        const m = models[url];
        if (m) return m.state === 'ok' ? m.scene : null;
        models[url] = { state: 'loading' };
        new GLTFLoader().load(url, g => { models[url] = { state: 'ok', scene: g.scene }; schedule3d(); }, undefined, () => { models[url] = { state: 'fail' }; });
        return null;
    };
    T3 = { THREE, renderer, scene, camera, controls, root, walls, tex, failed, lamp, render, resize, model, first: true };
    return T3;
}
async function build3d() {
    const t = await init3d(); if (!t) return;
    const { THREE, root, walls, tex, failed, camera, controls } = t;
    t.lamp.position.set(0, S.h / 100 - 0.1, 0);
    while (root.children.length) root.remove(root.children[0]);
    walls.length = 0;
    const bb = bbox(), cx = (bb.x0 + bb.x1) / 200, cz = (bb.y0 + bb.y1) / 200, H = S.h / 100, TH = 0.08;
    const X = x => x / 100 - cx, Z = y => y / 100 - cz;
    const mat = c => new THREE.MeshStandardMaterial({ color: c, roughness: 0.45, metalness: 0.05 });
    const white = mat(0xffffff), chrome = new THREE.MeshStandardMaterial({ color: 0xc9ced6, roughness: 0.2, metalness: 0.9 });
    const glass = new THREE.MeshStandardMaterial({ color: 0xd9eef7, transparent: true, opacity: 0.28, roughness: 0.05, metalness: 0.1, side: THREE.DoubleSide, depthWrite: false });
    const boxAt = (g, w, h, d, x, y, z, m) => { const ms = new THREE.Mesh(new THREE.BoxGeometry(Math.max(w, 0.001), Math.max(h, 0.001), Math.max(d, 0.001)), m); ms.position.set(x + w / 2, y + h / 2, z + d / 2); g.add(ms); return ms; };
    // seramik dokusu yüklenemezse düz açık renk (siyah yüzey olmasın)
    const tiled = (id, w, h) => {
        if (failed.has(id)) return new THREE.MeshLambertMaterial({ color: 0xEDEBE6 });
        const tt = tex(id).clone(); tt.needsUpdate = true; tt.repeat.set(w / (C.tiles[id].tw / 100), h / (C.tiles[id].th / 100));
        return new THREE.MeshLambertMaterial({ map: tt });
    };
    // zemin (oda şeklinde)
    const shape = new THREE.Shape(P.map(p => new THREE.Vector2(X(p[0]), -Z(p[1]))));
    let fm;
    if (failed.has(S.floor)) fm = new THREE.MeshLambertMaterial({ color: 0xD9D6D0 });
    else { const ft = tex(S.floor).clone(); ft.needsUpdate = true; ft.repeat.set(1 / (C.tiles[S.floor].tw / 100), 1 / (C.tiles[S.floor].th / 100)); fm = new THREE.MeshLambertMaterial({ map: ft }); }
    const fl = new THREE.Mesh(new THREE.ShapeGeometry(shape), fm); fl.rotation.x = -Math.PI / 2; root.add(fl);
    // duvarlar: her kenar bir duvar; kapı ve pencere boşluklu; dış köşelerde kalınlık kadar uzar
    const dH = Math.min(2.1, H - 0.1);
    const opensOn = e => {   // [başlangıç, bitiş, alt, üst] metre, kenarın a ucundan
        const res = [], fromA = (lo, w) => ((e.axis === 'h' ? e.a[0] : e.a[1]) === e.lo ? lo : e.len - lo - w) / 100;
        if (e === doorEdge()) { const st = fromA(doorOff(), doorW()); res.push([st, st + doorW() / 100, 0, dH]); }
        S.wins.forEach(wn => { if (wn.e === e.i) { winFix(wn); const st = fromA(wn.off, wn.w); res.push([st, st + wn.w / 100, wn.sill / 100, (wn.sill + wn.h) / 100, wn.k === 'nis']); } });
        return res;
    };
    const winGlass = new THREE.MeshStandardMaterial({ color: 0xcfe8f6, transparent: true, opacity: 0.35, roughness: 0.05, side: THREE.DoubleSide, depthWrite: false });
    const frameMat = new THREE.MeshLambertMaterial({ color: 0xf4f5f6 });
    const paintMat = new THREE.MeshLambertMaterial({ color: new THREE.Color(S.paint) });
    const tileTop = S.tileH < 0 ? 0 : (S.tileH > 0 ? Math.min(S.tileH / 100, H) : H);
    // tavan (yalnızca kamera tavanın altına inince görünür)
    const ceil = new THREE.Mesh(new THREE.ShapeGeometry(shape), new THREE.MeshLambertMaterial({ color: new THREE.Color(S.paint), side: THREE.DoubleSide }));
    ceil.rotation.x = -Math.PI / 2; ceil.position.y = H; ceil.userData.ceiling = true; root.add(ceil); t.ceil = ceil;
    E.forEach(e => {
        const wt = S.acc.e === e.i && S.acc.tile ? S.acc.tile : S.wall;
        const len = e.len / 100, ext0 = e.convexStart ? TH : 0, ext1 = e.convexEnd ? TH : 0, ops = opensOn(e);
        const xs = [...new Set([-ext0, len + ext1, ...ops.flatMap(o => [Math.max(0, o[0]), Math.min(len, o[1])])])].sort((x, y) => x - y);
        const g = new THREE.Group();
        for (let k = 0; k < xs.length - 1; k++) {
            const a = xs[k], b = xs[k + 1], mid = (a + b) / 2, L = b - a;
            if (L <= 0.001) continue;
            const cut = ops.filter(o => mid > o[0] && mid < o[1]).map(o => [o[2], o[3]]).sort((x, y) => x[0] - y[0]);
            let y = 0;
            const spans = [];
            cut.forEach(([y0, y1]) => { if (y0 > y) spans.push([y, y0]); y = Math.max(y, y1); });
            if (y < H) spans.push([y, H]);
            spans.forEach(([y0, y1]) => {
                // seramik yüksekliğine kadar seramik, üstü boya
                [[y0, Math.min(y1, tileTop), true], [Math.max(y0, tileTop), y1, false]].forEach(([p0, p1, isTile]) => {
                    if (p1 - p0 <= 0.001) return;
                    const m = new THREE.Mesh(new THREE.BoxGeometry(L, p1 - p0, TH), isTile ? tiled(wt, L, p1 - p0) : paintMat);
                    m.position.set(a + L / 2, (p0 + p1) / 2, 0); g.add(m);
                });
            });
        }
        ops.forEach(o => { if (o[2] > 0.01 && o[4]) {   // niş: dış yüzde arka plaka (içeri gömülü raf görünümü)
            const L = o[1] - o[0], h = o[3] - o[2];
            const back = new THREE.Mesh(new THREE.BoxGeometry(L, h, 0.01), tiled(wt, L, h)); back.position.set(o[0] + L / 2, o[2] + h / 2, -TH / 2 + 0.005); g.add(back);
            const shelf = new THREE.Mesh(new THREE.BoxGeometry(L, 0.012, TH), frameMat); shelf.position.set(o[0] + L / 2, o[2] + 0.006, 0); g.add(shelf);
        } else if (o[2] > 0.01) {   // pencere camı ve kasası
            const L = o[1] - o[0], h = o[3] - o[2];
            const gl = new THREE.Mesh(new THREE.BoxGeometry(L, h, 0.01), winGlass); gl.position.set(o[0] + L / 2, o[2] + h / 2, 0); g.add(gl);
            const sill = new THREE.Mesh(new THREE.BoxGeometry(L + 0.04, 0.03, TH + 0.04), frameMat); sill.position.set(o[0] + L / 2, o[2] - 0.015, 0); g.add(sill);
        } });
        const out = [-e.n[0], -e.n[1]];
        g.position.set(X(e.a[0]) + out[0] * TH / 2, 0, Z(e.a[1]) + out[1] * TH / 2);
        g.rotation.y = -Math.atan2(e.d[1], e.d[0]);
        root.add(g);
        walls.push({ g, n: new THREE.Vector3(out[0], 0, out[1]), p: new THREE.Vector3(X((e.a[0] + e.b[0]) / 2), H / 2, Z((e.a[1] + e.b[1]) / 2)) });
    });
    // parçalar
    S.items.forEach(it => {
        const p = product(it), s = dims(it), g = new THREE.Group(), w = s.w / 100, d = s.d / 100;
        const src = p.model ? t.model(p.model) : null;
        if (src) {
            // modeli ürün ölçüsüne oturt: en ve derinlik birebir, yükseklik orantılı; zemine oturan model yerde, asılı model montaj yüksekliğinde
            const m = src.clone(true), b0 = new THREE.Box3().setFromObject(m), z0 = b0.getSize(new THREE.Vector3());
            const sx = w / (z0.x || 1), sz = d / (z0.z || 1), sy = (sx + sz) / 2;
            m.scale.set(sx, sy, sz);
            const b1 = new THREE.Box3().setFromObject(m);
            m.position.set(-b1.min.x, b1.min.y < 0 ? -b1.min.y : 0, -b1.min.z);
            g.add(m);
        } else switch (it.type) {
            case 'kabin': {
                boxAt(g, w, 0.05, d, 0, 0, 0, white);
                boxAt(g, w, 1.95, 0.008, 0, 0.05, d - 0.008, glass); boxAt(g, 0.008, 1.95, d, w - 0.008, 0.05, 0, glass);
                boxAt(g, w, 0.02, 0.02, 0, 1.98, d - 0.02, chrome);
                const head = new THREE.Mesh(new THREE.CylinderGeometry(0.1, 0.1, 0.015, 24), chrome); head.position.set(w / 2, 1.95, 0.25); g.add(head);
                boxAt(g, 0.02, 1.1, 0.02, w / 2 - 0.01, 0.95, 0.02, chrome); boxAt(g, 0.02, 0.02, 0.25, w / 2 - 0.01, 2.04, 0.02, chrome);
                break;
            }
            case 'gomme': {
                boxAt(g, w, 0.01, d, 0, 0, 0, mat(0xd8dde3));
                boxAt(g, w * 0.65, 2.0, 0.01, 0, 0, d - 0.01, glass);
                boxAt(g, w * 0.65, 0.02, 0.02, 0, 1.98, d - 0.02, chrome);
                const head = new THREE.Mesh(new THREE.CylinderGeometry(0.12, 0.12, 0.012, 24), chrome); head.position.set(w * 0.4, 2.05, 0.3); g.add(head);
                break;
            }
            case 'kuvet': {
                const hh = p.h / 100;
                boxAt(g, w, hh, d, 0, 0, 0, white);
                boxAt(g, w - 0.12, 0.005, d - 0.12, 0.06, hh - 0.004, 0.06, mat(0xcfe6f1));
                boxAt(g, 0.03, 0.12, 0.03, 0.12, hh, 0.02, chrome);
                break;
            }
            case 'lavabo': {
                if (p.cabinet) boxAt(g, w, 0.5, d - 0.02, 0, 0.3, 0, mat(new THREE.Color(p.cabinet)));
                boxAt(g, w, 0.06, d, 0, 0.8, 0, white);
                const bw = Math.min(w * 0.6, 0.45);
                boxAt(g, bw, 0.005, d * 0.55, (w - bw) / 2, 0.86, d * 0.25, mat(0xdfeef5));
                boxAt(g, 0.025, 0.18, 0.025, w / 2 - 0.012, 0.86, 0.04, chrome);
                const mw = Math.min(w, 0.9) * 0.9;
                boxAt(g, mw, 0.7, 0.01, (w - mw) / 2, 1.15, 0.002, new THREE.MeshStandardMaterial({ color: 0xe9eff3, roughness: 0.1, metalness: 0.1 }));
                if (!p.cabinet) boxAt(g, 0.05, 0.35, 0.05, w / 2 - 0.025, 0.45, 0.06, chrome);
                break;
            }
            case 'klozet': {
                if (p.tank) { boxAt(g, w, 0.4, 0.18, 0, 0.4, 0, white); boxAt(g, w * 0.86, 0.4, d - 0.16, w * 0.07, 0, 0.16, white); }
                else { boxAt(g, w * 0.94, 0.33, d - 0.02, w * 0.03, 0.07, 0.02, white); boxAt(g, 0.22, 0.15, 0.01, w / 2 - 0.11, 0.95, 0.001, chrome); }
                break;
            }
            case 'icduvar':
            case 'engel': {
                const m = new THREE.Mesh(new THREE.BoxGeometry(w, H, d), tiled(S.wall, Math.max(w, d), H));
                m.position.set(w / 2, H / 2, d / 2); g.add(m);
                break;
            }
            case 'havlupan': {
                const c = mat(new THREE.Color(p.color)); c.metalness = 0.6; c.roughness = 0.3;
                boxAt(g, 0.025, 0.8, 0.025, 0, 0.6, 0.05, c); boxAt(g, 0.025, 0.8, 0.025, w - 0.025, 0.6, 0.05, c);
                for (let i = 0; i < 8; i++) boxAt(g, w, 0.015, 0.015, 0, 0.65 + i * 0.1, 0.055, c);
                break;
            }
        }
        const f = foot(it);
        let ox = f.x, oy = f.y;
        if (it.rot === 90) ox = f.x + f.w; else if (it.rot === 180) { ox = f.x + f.w; oy = f.y + f.d; } else if (it.rot === 270) oy = f.y + f.d;
        g.position.set(X(ox), 0, Z(oy));
        g.rotation.y = -it.rot * Math.PI / 180;
        root.add(g);
    });
    if (t.first) {
        t.first = false;
        const W = (bb.x1 - bb.x0) / 100, L = (bb.y1 - bb.y0) / 100;
        camera.position.set(W * 0.55 + 1.4, H + 1.2, L * 0.55 + 1.8);
        controls.target.set(0, H * 0.4, 0); controls.update();
    }
    t.resize();
}

/* ================= kat planı altlığı ================= */
$('ulFile').addEventListener('change', e => {
    const f = e.target.files && e.target.files[0]; if (!f) return;
    if (S.under) URL.revokeObjectURL(S.under.url);
    const url = URL.createObjectURL(f), img = new Image();
    img.onload = () => { S.under = { url, w: S.w, x: 0, y: 0, op: 0.45, h: img.naturalHeight / img.naturalWidth }; ulPanel(); draw2d(); };
    img.src = url;
});
function ulPanel() { $('ulBox').hidden = !S.under; if (S.under) { $('ulW').value = Math.round(S.under.w); $('ulOp').value = Math.round(S.under.op * 100); } }
$('ulW').addEventListener('change', e => { if (!S.under) return; S.under.w = clamp(+e.target.value || S.under.w, 50, 3000); draw2d(); });
$('ulOp').addEventListener('input', e => { if (!S.under) return; S.under.op = +e.target.value / 100; draw2d(); });
document.querySelectorAll('.pl-nudge button[data-d]').forEach(b => b.addEventListener('click', () => {
    if (!S.under) return; const [dx, dy] = b.dataset.d.split(',').map(Number); S.under.x += dx; S.under.y += dy; draw2d();
}));
$('ulDel').addEventListener('click', () => { if (S.under) URL.revokeObjectURL(S.under.url); S.under = null; $('ulFile').value = ''; ulPanel(); draw2d(); });

/* ================= gerçekçi görsel (yapay zekâ, kredi) ================= */
let aiMode = 'plan', aiQ = 'std';
[['aiStd', 'std'], ['aiHd', 'hd']].forEach(([id, q]) => $(id).addEventListener('click', () => {
    aiQ = q; $('aiStd').setAttribute('aria-pressed', q === 'std'); $('aiHd').setAttribute('aria-pressed', q === 'hd');
}));
$('plAi').addEventListener('click', () => { $('aiDlg').showModal(); });
$('aiClose').addEventListener('click', () => $('aiDlg').close());
[['aiPlan', 'plan'], ['aiPhoto', 'photo']].forEach(([id, m]) => $(id).addEventListener('click', () => {
    aiMode = m; $('aiPlan').setAttribute('aria-pressed', m === 'plan'); $('aiPhoto').setAttribute('aria-pressed', m === 'photo'); $('aiPhotoBox').hidden = m !== 'photo';
}));
async function sceneShot() {
    const back = S.view;
    if (S.view !== '3d') setView('3d');
    await new Promise(r => setTimeout(r, 60)); await build3d();
    if (!T3) return null;
    await new Promise(r => setTimeout(r, 400));   // dokular ve modeller yüklensin
    await build3d(); T3.render();
    const url = T3.renderer.domElement.toDataURL('image/jpeg', 0.9);
    if (back !== '3d') setView(back);
    return url;
}
$('aiGo').addEventListener('click', async () => {
    const msg = $('aiMsg'), go = $('aiGo');
    msg.classList.remove('is-err');
    const fd = new FormData();
    fd.append('mode', aiMode); fd.append('quality', aiQ);
    if (aiMode === 'photo') {
        const f = $('aiFile').files[0];
        if (!f) { msg.textContent = 'Önce banyonuzun fotoğrafını seçin.'; msg.classList.add('is-err'); return; }
        fd.append('photo', f);
    } else {
        $('aiDlg').close();
        const shot = await sceneShot();
        $('aiDlg').showModal();
        if (!shot) { msg.textContent = '3D görünüm hazırlanamadı; bağlantınızı kontrol edin.'; msg.classList.add('is-err'); return; }
        fd.append('scene', shot);
    }
    fd.append('floor', S.floor); fd.append('wall', S.acc.e >= 0 && S.acc.tile ? S.wall : S.wall);
    fd.append('paint', S.paint); fd.append('tile_height', String(S.tileH));
    fd.append('items', S.items.filter(it => !isEl(it)).map(it => { const p = product(it), d = dims(it); return `${TYPES[it.type].label} ${d.w}x${d.d} cm (${p.name})`; }).join('; ').slice(0, 790));
    fd.append('note', ($('aiNote').value || '').slice(0, 300));
    go.disabled = true; go.textContent = 'Oluşturuluyor… (30–60 sn)'; msg.textContent = 'Görseliniz hazırlanıyor, lütfen bekleyin.';
    try {
        const r = await fetch(C.renderUrl, { method: 'POST', body: fd, headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, Accept: 'application/json' }, credentials: 'same-origin' });
        const j = await r.json().catch(() => ({}));
        if (!r.ok || !j.image) { msg.textContent = j.message || 'Görsel oluşturulamadı. Lütfen tekrar deneyin.'; msg.classList.add('is-err'); return; }
        $('aiImg').src = j.image; $('aiDl').href = j.image; $('aiOut').hidden = false;
        msg.textContent = j.message + ' Yapay zekâ görselleri temsilîdir.';
    } catch (e) {
        msg.textContent = 'Bağlantı hatası. Lütfen tekrar deneyin.'; msg.classList.add('is-err');
    } finally { go.disabled = false; go.textContent = 'Görseli oluştur'; }
});

/* ================= başlat ================= */
fillTileSelects(); addButtons(); buildShapes();
const hs = location.hash.match(/#p=([A-Za-z0-9_-]+)/);
if (hs && decode(hs[1])) { buildRoom(); }
else { buildRoom(); S.items = defaultItems(); autoLayout(); }
$('plHint').textContent = HINT2D;
check(); renderPanel(); draw2d(); save();
</script>

<x-site.contech-footer />
<script src="/js/tool-events.js" defer></script>
</body>
</html>
