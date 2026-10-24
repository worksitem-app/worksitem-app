<!DOCTYPE html><html lang="tr"><head>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <meta property="og:image" content="https://worksitem.com/brand/og-image.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="https://worksitem.com/brand/og-image.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="manifest" href="/site.webmanifest">
    <meta name="theme-color" content="#ffffff">
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1"><title>Bombeli Tank Hacmi | WORKSİTEM</title>
    <meta name="description" content="Bombeli kapaklı yatay silindirik yakıt veya su tanklarının toplam hacmini litre cinsinden hesaplayın ve tank kapasitesini belirleyin.">
    <link rel="canonical" href="https://worksitem.com/araclar/bombeli-tank-hacmi"><link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"><style>*,*::before,*::after{box-sizing:border-box} .ws-warn{color:#b91c1c;font-size:13px;font-weight:600;margin-top:10px;} .ws-sub{font-size:13px;color:#374151;margin-top:8px;line-height:1.5;} .result-val{overflow-wrap:anywhere;} body{font-family:'Inter',sans-serif;background:#fdfdfd;color:#111827;margin:0;padding-bottom:60px;} .container{max-width:800px;margin:40px auto;padding:30px;background:#fff;border:1px solid #e5e7eb;border-radius:16px;box-shadow:0 4px 6px rgba(0,0,0,0.05);} h1{font-size:24px;font-weight:800;margin-bottom:10px;color:#101318;} .form-group{margin-bottom:20px;} label{display:block;font-size:13px;font-weight:700;color:#6b7280;margin-bottom:8px;text-transform:uppercase;} input,select{width:100%;padding:14px;border:1px solid #e5e7eb;border-radius:8px;font-family:inherit;font-size:15px;} .grid-2{display:grid;grid-template-columns:1fr 1fr;gap:15px;} .result-box{background:#fff8e1;border:2px solid #fde68a;padding:20px;border-radius:12px;margin-top:20px;text-align:center;} .result-val{font-size:32px;font-weight:800;color:#101318;} .cta{display:block;width:100%;padding:16px;background:#101318;color:#fff;text-align:center;font-weight:800;text-decoration:none;border-radius:8px;margin-top:20px; transition:all 0.2s;} .cta:hover{opacity:0.9; transform:translateY(-2px);}</style>    <meta name="csrf-token" content="{{ csrf_token() }}">
    <x-calc.head />
</head>
<body class="wc" data-tool-slug="domed-tank">
    <x-site.contech-header /><div class="container"><x-calc.hero title="Bombeli tank hacmi hesaplama" crumb="Bombeli tank">Kapakları bombeli olan yatay silindirik sıvı tanklarının toplam hacmini (Litre) hesaplayın.</x-calc.hero>
<div class="grid-2"><div class="form-group"><label>Tank Çapı (m)</label><input type="number" id="dia" value="2" step="0.1" min="0.1" max="20" oninput="calc()"></div><div class="form-group"><label>Düz Silindir Boyu (m)</label><input type="number" id="length" value="5" step="0.1" min="0" max="100" oninput="calc()"></div></div>
<div class="form-group"><label>Kapak (Bombe) Tipi</label><select id="headType" onchange="calc()"><option value="ell" selected>2:1 elips kapak (bombe derinliği D/4)</option><option value="klop">Klöpper kapak (DIN 28011, torisferik)</option><option value="hemi">Yarım küre kapak (bombe derinliği D/2)</option></select></div>
<div class="result-box" data-wc-result><div style="font-size:14px; color:#101318; font-weight:600;">TOPLAM HACİM</div><div class="result-val" id="res" data-wc-price>0 Litre</div><div class="ws-sub" id="detail"></div><div class="ws-warn" id="warn" role="alert" hidden></div><div style="font-size:12px; color:#101318; margin-top:8px;">İki kapak dahil iç hacimdir; sac kalınlığı ihmal edilmiştir. Kapak tipi tank etiketinde ya da imalat çiziminde yazar.</div></div>
<a href="/maliyet-hesapla?service=mekanik-tesisat" class="cta">Mekanik Tesisat Projesi İçin Keşif İste →</a></div>

    <script>
                // --- MERKEZİ ÇŞB FİYAT MOTORU & KAİNTA ÇARPANI ---
        const CSB_PRICES = {!! isset($SYSTEM_PRICES) ? $SYSTEM_PRICES : '{}' !!};
        const KAINTA_MULTIPLIER = {!! isset($KAINTA_MULTIPLIER) ? $KAINTA_MULTIPLIER : 1.40 !!};

        function getDbPrice(key, fallback) {
            let baseCsbPrice = CSB_PRICES[key] !== undefined ? parseFloat(CSB_PRICES[key]) : fallback;
            // ÇŞB Ham Fiyatı x 1.40 Kainta Çarpanı
            return baseCsbPrice * KAINTA_MULTIPLIER; 
        }
        
        function getRawCsbPrice(key, fallback) {
            // Sadece ham ÇŞB maliyeti görmek istenirse (İhale modülü vb. için çarpanı yoksay)
            return CSB_PRICES[key] !== undefined ? parseFloat(CSB_PRICES[key]) : fallback;
        }
        // ------------------------------------------------
        function calc() {
            const d = parseFloat(document.getElementById('dia').value), l = parseFloat(document.getElementById('length').value);
            const type = document.getElementById('headType').value;
            const res = document.getElementById('res'), warn = document.getElementById('warn'), det = document.getElementById('detail');
            // v118: negatif/0/boş girdide hesap yapılmaz
            if (!(d > 0) || !(l >= 0) || d > 50 || l > 500) { res.innerText = '—'; det.innerText = ''; warn.innerText = 'Çap için pozitif, silindir boyu için 0 veya pozitif bir değer girin.'; warn.hidden = false; return; }
            warn.hidden = true;
            const vCyl = Math.PI * Math.pow(d, 2) / 4 * l;
            // Tek kapak hacmi: yarım küre π·D³/12 ; 2:1 elips π·D³/24 ; klöpper ≈ 0,0998·D³ (DIN 28011)
            const vHead = { hemi: Math.PI * Math.pow(d, 3) / 12, ell: Math.PI * Math.pow(d, 3) / 24, klop: 0.0998 * Math.pow(d, 3) }[type] || Math.PI * Math.pow(d, 3) / 24;
            const vTotal = (vCyl + 2 * vHead) * 1000;
            const L = (x) => Math.round(x * 1000).toLocaleString('tr-TR') + ' L';
            res.innerText = Math.round(vTotal).toLocaleString('tr-TR') + ' Litre';
            det.innerText = 'Silindir ' + L(vCyl) + ' + 2 kapak × ' + L(vHead);
        } document.addEventListener('DOMContentLoaded', calc);</script>    <script src="/js/tool-events.js" defer></script>
<x-calc.kit />
    <x-site.contech-footer />
</body></html>
