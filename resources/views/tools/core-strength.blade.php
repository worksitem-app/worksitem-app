<!DOCTYPE html>
<html lang="tr">
<head>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Karot Basınç Dayanımı | WORKSİTEM</title>
    <meta name="description" content="Karot numunesinin kırılma yükü ve geometrisine göre eşdeğer silindir beton basınç dayanımını MPa cinsinden hesaplayın.">
    <link rel="canonical" href="https://worksitem.com/araclar/karot-dayanim">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>*,*::before,*::after{box-sizing:border-box} .ws-warn{color:#b91c1c;font-size:13px;font-weight:600;margin-top:10px;} .ws-sub{font-size:13px;color:#374151;margin-top:8px;line-height:1.5;} .result-val{overflow-wrap:anywhere;} 
        body { font-family:'Inter', sans-serif; background:#fdfdfd; color:#111827; margin:0; padding-bottom:60px;}
        .container { max-width:800px; margin:40px auto; padding:30px; background:#fff; border:1px solid #e5e7eb; border-radius:16px; box-shadow:0 4px 6px rgba(0,0,0,0.05); }
        h1 { font-size:24px; font-weight:800; margin-bottom:10px; color:#101318;}
        .form-group { margin-bottom:20px; }
        label { display:block; font-size:13px; font-weight:700; color:#6b7280; margin-bottom:8px; text-transform:uppercase;}
        input { width:100%; padding:14px; border:1px solid #e5e7eb; border-radius:8px; font-family:inherit; font-size:15px; }
        .grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:15px; }
        .result-box { background:#fff8e1; border:2px solid #fde68a; padding:20px; border-radius:12px; margin-top:20px; text-align:center;}
        .result-val { font-size:32px; font-weight:800; color:#101318; }
        .cta { display:block; width:100%; padding:16px; background:#101318; color:#fff; text-align:center; font-weight:800; text-decoration:none; border-radius:8px; margin-top:20px; }
    </style>
    <meta property="og:image" content="https://worksitem.com/brand/og-image.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="https://worksitem.com/brand/og-image.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="manifest" href="/site.webmanifest">
    <meta name="theme-color" content="#ffffff">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <x-calc.head />
</head>
<body class="wc" data-tool-slug="core-strength">
    <x-site.contech-header />
    <div class="container">
        <x-calc.hero title="Karot basınç dayanımı hesaplama" crumb="Karot dayanımı">Pres makinesinde kırılan karot numunesinin kırılma yüküne ve geometrisine göre eşdeğer silindir beton dayanımını (MPa) hesaplayın.</x-calc.hero>
        
        <div class="grid-2">
            <div class="form-group">
                <label>Karot Çapı (d) - mm</label>
                <input type="number" id="dia" value="100" min="1" oninput="calc()">
            </div>
            <div class="form-group">
                <label>Karot Boyu (h) - mm</label>
                <input type="number" id="height" value="100" min="1" oninput="calc()">
            </div>
        </div>
        <div class="form-group">
            <label>Kırılma Yükü (F) - kN</label>
            <input type="number" id="force" value="150" min="0" oninput="calc()">
        </div>

        <div class="result-box" data-wc-result>
            <div style="font-size:14px; color:#101318; font-weight:600; margin-bottom:5px;">EŞDEĞER SİLİNDİR DAYANIMI</div>
            <div class="result-val" id="res" data-wc-price>0 MPa</div>
            <div class="ws-warn" id="warn" role="alert" hidden></div>
            <div style="font-size:12px; color:#101318; margin-top:10px; line-height:1.5;">h/d düzeltme katsayısı otomatik uygulanmıştır. Bu değer tek bir karotun dayanımıdır, binanın beton dayanımı değildir. Yerindeki beton dayanımı en az 3 karotun ortalaması ve standart sapmasıyla (TS EN 13791; mevcut binalarda TBDY 2018 Bölüm 15) değerlendirilir.</div>
        </div>

        <a href="/maliyet-hesapla?service=deprem-testi" class="cta">Bina Deprem Testi ve Karot Alımı İçin Uzman Çağır →</a>
    </div>

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
            const h = parseFloat(document.getElementById('height').value);
            const F = parseFloat(document.getElementById('force').value);
            const res = document.getElementById('res'), warn = document.getElementById('warn');

            // v118: negatif/0/boş girdide hesap yapılmaz (eskiden d=0 → 1 mm sayılıyordu)
            if (!(d > 0) || !(h > 0) || !(F > 0)) { res.innerText = '—'; warn.innerText = 'Çap, boy ve kırılma yükü için pozitif bir değer girin.'; warn.hidden = false; return; }
            const ratio = h / d;
            // TS EN 12504-1: h/d < 1 karot geçerli bir deney numunesi değildir
            if (ratio < 1) { res.innerText = '—'; warn.innerText = 'Boy/çap oranı ' + ratio.toLocaleString('tr-TR', { maximumFractionDigits: 2 }) + '. h/d 1\'den küçük karot geçerli değildir (TS EN 12504-1); boyu en az çap kadar olmalı.'; warn.hidden = false; return; }

            // Alan A = π·d²/4 (mm²) ; f = F(N)/A = MPa
            const A = (Math.PI * Math.pow(d, 2)) / 4;
            const f_raw = (F * 1000) / A;

            // h/d düzeltmesi: h/d = 1 → 0,85 ; h/d ≥ 2 → 1,0 ; arası doğrusal
            const factor = ratio < 2.0 ? 0.85 + 0.15 * (ratio - 1) : 1.0;
            const f_core = f_raw * factor;

            res.innerText = f_core.toLocaleString('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' MPa';
            const notes = [];
            if (d < 75) notes.push('Çap 75 mm\'den küçük; küçük çaplı karotlarda sonuç dağılımı büyüktür, daha fazla karot gerekir.');
            if (ratio > 2.2) notes.push('h/d 2\'den büyük; standart karotlar h/d = 1 veya 2 hazırlanır.');
            warn.innerText = notes.join(' ');
            warn.hidden = notes.length === 0;
        }
        document.addEventListener('DOMContentLoaded', calc);
    </script>
<x-calc.kit />
    <x-site.contech-footer />
    <script src="/js/tool-events.js" defer></script>
</body>
</html>
