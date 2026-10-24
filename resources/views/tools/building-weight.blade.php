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
    <meta name="viewport" content="width=device-width, initial-scale=1"><title>Bina Ağırlık Hesabı | WORKSİTEM</title>
    <meta name="description" content="Deprem analizlerinde kullanılmak üzere betonarme yapının yaklaşık toplam ağırlığını ve kütlesini ton cinsinden hesaplayın.">
    <link rel="canonical" href="https://worksitem.com/araclar/bina-agirlik-hesabi"><link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"><style>*,*::before,*::after{box-sizing:border-box} .ws-warn{color:#b91c1c;font-size:13px;font-weight:600;margin-top:10px;} .ws-sub{font-size:13px;color:#374151;margin-top:8px;line-height:1.5;} .result-val{overflow-wrap:anywhere;} body{font-family:'Inter',sans-serif;background:#fdfdfd;color:#111827;margin:0;padding-bottom:60px;} .container{max-width:800px;margin:40px auto;padding:30px;background:#fff;border:1px solid #e5e7eb;border-radius:16px;box-shadow:0 4px 6px rgba(0,0,0,0.05);} h1{font-size:24px;font-weight:800;margin-bottom:10px;color:#101318;} .form-group{margin-bottom:20px;} label{display:block;font-size:13px;font-weight:700;color:#6b7280;margin-bottom:8px;text-transform:uppercase;} input,select{width:100%;padding:14px;border:1px solid #e5e7eb;border-radius:8px;font-family:inherit;font-size:15px;} .grid-2{display:grid;grid-template-columns:1fr 1fr;gap:15px;} .result-box{background:#fff8e1;border:2px solid #fde68a;padding:20px;border-radius:12px;margin-top:20px;text-align:center;} .result-val{font-size:32px;font-weight:800;color:#101318;} .cta{display:block;width:100%;padding:16px;background:#101318;color:#fff;text-align:center;font-weight:800;text-decoration:none;border-radius:8px;margin-top:20px; transition:all 0.2s;} .cta:hover{opacity:0.9; transform:translateY(-2px);}</style>    <meta name="csrf-token" content="{{ csrf_token() }}">
    <x-calc.head />
</head>
<body class="wc" data-tool-slug="building-weight">
    <x-site.contech-header /><div class="container"><x-calc.hero title="Bina ağırlığı hesaplama (G + n·Q, deprem ağırlığı)" crumb="Bina ağırlığı">Betonarme bir yapının deprem hesabında kullanılan yaklaşık ağırlığını (W = G + n·Q, TBDY 2018) ton cinsinden hesaplayın.</x-calc.hero>
<div class="grid-2"><div class="form-group"><label>Toplam Kat Sayısı</label><input type="number" id="floors" value="5" min="1" max="60" step="1" oninput="calc()"></div><div class="form-group"><label>Kat Taban Alanı (m²)</label><input type="number" id="area" value="200" min="1" oninput="calc()"></div></div>
<div class="grid-2"><div class="form-group"><label>Kullanım Amacı</label><select id="use" onchange="calc()"><option value="0.2|0.3" selected>Konut (Q = 2 kN/m², n = 0,3)</option><option value="0.2|0.3">Ofis / işyeri (Q = 2 kN/m², n = 0,3)</option><option value="0.35|0.6">Okul / yurt (Q = 3,5 kN/m², n = 0,6)</option><option value="0.5|0.6">AVM / toplanma (Q = 5 kN/m², n = 0,6)</option><option value="0.5|0.8">Depo (Q = 5 kN/m², n = 0,8)</option></select></div><div class="form-group"><label>Ortalama Ölü Yük G (t/m²)</label><input type="number" id="dead" value="1.05" min="0.5" max="2" step="0.05" oninput="calc()"></div></div>
<div class="result-box" data-wc-result><div style="font-size:14px; color:#101318; font-weight:600;">YAKLAŞIK DEPREM AĞIRLIĞI (W = G + n·Q)</div><div class="result-val" id="res" data-wc-price>0 Ton</div><div class="ws-sub" id="detail"></div><div class="ws-warn" id="warn" role="alert" hidden></div><div style="font-size:12px; color:#101318; margin-top:5px;">Not: TBDY 2018 4.5.9 ve Tablo 4.3'e göre deprem ağırlığı ölü yük ile hareketli yükün n katının toplamıdır (konutta n = 0,3; konutta yaklaşık 1,0–1,2 t/m²). Hareketli yükler TS 498'den alınmıştır. Ön tahmindir; kesin değer statik projede bulunur.</div></div>
<a href="/maliyet-hesapla?service=zemin-etudu" class="cta">Zemin Etüdü ve Statik Proje İçin Başvur →</a></div>

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
            const f = parseFloat(document.getElementById('floors').value), a = parseFloat(document.getElementById('area').value), g = parseFloat(document.getElementById('dead').value);
            const [q, n] = document.getElementById('use').value.split('|').map(Number);
            const res = document.getElementById('res'), warn = document.getElementById('warn'), det = document.getElementById('detail');
            // v118: negatif/0/boş girdide hesap yapılmaz
            if (!(f > 0) || !(a > 0) || !(g > 0) || f > 200 || a > 1000000) { res.innerText = '—'; det.innerText = ''; warn.innerText = 'Kat sayısı, alan ve ölü yük için pozitif bir değer girin.'; warn.hidden = false; return; }
            warn.hidden = true;
            // W = Σ (G + n·Q) · A  (TBDY 2018 4.5.9, Tablo 4.3: n konut 0,3)
            const w1 = g + n * q;
            const weight = Math.round(f) * a * w1;
            const f2 = (x) => x.toLocaleString('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            res.innerText = Math.round(weight).toLocaleString('tr-TR') + ' Ton';
            det.innerText = 'G = ' + f2(g) + ' t/m², Q = ' + f2(q) + ' t/m², n = ' + n.toLocaleString('tr-TR') + ' → ' + f2(w1) + ' t/m² × ' + Math.round(f) + ' kat × ' + a.toLocaleString('tr-TR') + ' m²';
        } document.addEventListener('DOMContentLoaded', calc);</script>    <script src="/js/tool-events.js" defer></script>
<x-calc.kit />
    <x-site.contech-footer />
</body></html>
