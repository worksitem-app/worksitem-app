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
    <meta name="viewport" content="width=device-width, initial-scale=1"><title>Kolon Filiz Boyu | WORKSİTEM</title>
    <meta name="description" content="TS 500 ve TBDY 2018 esaslarına göre kolon boyuna donatısının kenetlenme boyunu ve bindirme (ek) boyunu ayrı ayrı hesaplayın, üst kata bırakılacak filiz uzunluğunu planlayın.">
    <link rel="canonical" href="https://worksitem.com/araclar/kolon-filiz-boyu"><link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"><style>*,*::before,*::after{box-sizing:border-box} .ws-warn{color:#b91c1c;font-size:13px;font-weight:600;margin-top:10px;} .ws-sub{font-size:13px;color:#374151;margin-top:8px;line-height:1.5;} .result-val{overflow-wrap:anywhere;} body{font-family:'Inter',sans-serif;background:#fdfdfd;color:#111827;margin:0;padding-bottom:60px;} .container{max-width:800px;margin:40px auto;padding:30px;background:#fff;border:1px solid #e5e7eb;border-radius:16px;box-shadow:0 4px 6px rgba(0,0,0,0.05);} h1{font-size:24px;font-weight:800;margin-bottom:10px;color:#101318;} .form-group{margin-bottom:20px;} label{display:block;font-size:13px;font-weight:700;color:#6b7280;margin-bottom:8px;text-transform:uppercase;} input,select{width:100%;padding:14px;border:1px solid #e5e7eb;border-radius:8px;font-family:inherit;font-size:15px;} .grid-2{display:grid;grid-template-columns:1fr 1fr;gap:15px;} .result-box{background:#fff8e1;border:2px solid #fde68a;padding:20px;border-radius:12px;margin-top:20px;text-align:center;} .result-val{font-size:32px;font-weight:800;color:#101318;} .cta{display:block;width:100%;padding:16px;background:#101318;color:#fff;text-align:center;font-weight:800;text-decoration:none;border-radius:8px;margin-top:20px; transition:all 0.2s;} .cta:hover{opacity:0.9; transform:translateY(-2px);}</style>    <meta name="csrf-token" content="{{ csrf_token() }}">
    <x-calc.head />
</head>
<body class="wc" data-tool-slug="column-starter-bar">
    <x-site.contech-header /><div class="container"><x-calc.hero title="Kolon filiz ve bindirme boyu hesaplama" crumb="Kolon filiz boyu">Kolon boyuna donatısının TS 500'e göre kenetlenme boyunu (lb) ve ek yerine göre gereken bindirme boyunu (l0) ayrı ayrı bulun.</x-calc.hero>
<div class="grid-2"><div class="form-group"><label>Donatı Çapı (Ø) mm</label><select id="dia" onchange="calc()"><option value="14">Ø14</option><option value="16" selected>Ø16</option><option value="18">Ø18</option><option value="20">Ø20</option><option value="22">Ø22</option><option value="24">Ø24</option><option value="26">Ø26</option><option value="28">Ø28</option></select></div><div class="form-group"><label>Beton Sınıfı</label><select id="concrete" onchange="calc()"><option value="25" selected>C25</option><option value="30">C30</option><option value="35">C35</option><option value="40">C40</option></select></div></div>
<div class="form-group"><label>Ek Yeri</label><select id="splice" onchange="calc()"><option value="1.5" selected>Kolon alt ucunda, donatının yarısından fazlası aynı kesitte (l0 ≥ 1,5·lb)</option><option value="1.25">Kolon alt ucunda, donatının yarısı veya azı aynı kesitte (l0 ≥ 1,25·lb)</option><option value="1">Kolon serbest yüksekliğinin orta üçte birinde, TBDY 2018 7.3.3.1 (l0 ≥ lb)</option></select></div>
<div class="result-box" data-wc-result><div style="font-size:14px; color:#101318; font-weight:600;">MİNİMUM BİNDİRME (EK) BOYU l0</div><div class="result-val" id="res" data-wc-price>0 cm</div><div class="ws-sub" id="detail"></div><div style="font-size:12px; color:#101318; margin-top:8px; line-height:1.5;">Kenetlenme boyu (lb) donatının betona tutunması için gereken boydur; bindirme boyu (l0) iki çubuğun üst üste bindirildiği ek boyudur ve lb'den kısa olamaz. TBDY 2018 7.3.3.1 kolon eklerinin serbest yüksekliğin orta üçte birinde yapılmasını ister; bu durumda filiz döşeme üstünden en az (kolon boyu / 3 + l0) kadar bırakılır. S420 donatı, Konum II ve kancasız düz uç varsayılmıştır. Kesin boy statik projeden alınır.</div></div>
<a href="/maliyet-hesapla?service=kaba-insaat" class="cta">Kaba İnşaat ve Demir İşçiliği Teklifi Al →</a></div>

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
            const d = parseFloat(document.getElementById('dia').value);
            const fck = parseFloat(document.getElementById('concrete').value);
            const k = parseFloat(document.getElementById('splice').value) || 1.5;
            // TS 500 9.1.2 Denk. 9.1: lb = 0,12·(fyd/fctd)·Ø ≥ 20Ø ; fctd = 0,35·√fck / 1,5 ; S420: fyd = 420/1,15
            const fyd = 420 / 1.15, fctd = 0.35 * Math.sqrt(fck) / 1.5;
            const lb = Math.max(0.12 * (fyd / fctd) * d, 20 * d) / 10;   // cm
            // Bindirme: TS 500 9.3 / TBDY 2018 7.3.3.1 — orta bölgede l0 ≥ lb; alt uçta 1,25·lb (≤ yarısı) veya 1,5·lb (yarısından fazlası)
            const lap = k * lb;
            const up5 = (x) => Math.ceil(x / 5) * 5;   // şantiye için 5 cm'ye yukarı yuvarla
            document.getElementById('res').innerText = up5(lap) + ' cm';
            document.getElementById('detail').innerText = 'Kenetlenme boyu lb = ' + up5(lb) + ' cm (' + Math.round(lb * 10 / d) + 'Ø) · Bindirme l0 = ' + k.toLocaleString('tr-TR') + ' × lb = ' + up5(lap) + ' cm (' + Math.round(lap * 10 / d) + 'Ø)';
        } document.addEventListener('DOMContentLoaded', calc);</script>    <script src="/js/tool-events.js" defer></script>
<x-calc.kit />
    <x-site.contech-footer />
</body></html>
