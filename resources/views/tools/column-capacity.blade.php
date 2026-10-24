<!DOCTYPE html>
<html lang="tr">
<head>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kolon Eksenel Taşıma Kapasitesi | WORKSİTEM</title>
    <meta name="description" content="Beton sınıfı ve kolon kesitine göre TBDY 2018 eksenel yük sınırını (Nd ≤ 0,40·fck·Ac) ve kolonun saf eksenel taşıma gücünü hesaplayın.">
    <link rel="canonical" href="https://worksitem.com/araclar/kolon-tasima-kapasitesi">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>*,*::before,*::after{box-sizing:border-box} .ws-warn{color:#b91c1c;font-size:13px;font-weight:600;margin-top:10px;} .ws-sub{font-size:13px;color:#374151;margin-top:8px;line-height:1.5;} .result-val{overflow-wrap:anywhere;} 
        body { font-family:'Inter', sans-serif; background:#fdfdfd; color:#111827; margin:0; padding-bottom:60px;}
        .container { max-width:800px; margin:40px auto; padding:30px; background:#fff; border:1px solid #e5e7eb; border-radius:16px; box-shadow:0 4px 6px rgba(0,0,0,0.05); }
        h1 { font-size:24px; font-weight:800; margin-bottom:10px; color:#101318;}
        .form-group { margin-bottom:20px; }
        label { display:block; font-size:13px; font-weight:700; color:#6b7280; margin-bottom:8px; text-transform:uppercase;}
        input, select { width:100%; padding:14px; border:1px solid #e5e7eb; border-radius:8px; font-family:inherit; font-size:15px; }
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
<body class="wc" data-tool-slug="column-capacity">
    <x-site.contech-header />
    <div class="container">
        <x-calc.hero title="Kolon eksenel taşıma kapasitesi hesaplama" crumb="Kolon kapasitesi">Betonarme bir kolona deprem yönetmeliğine göre verilebilecek en büyük tasarım eksenel yükünü (TBDY 2018: Nd ≤ 0,40·fck·Ac) ve saf eksenel taşıma gücünü hesaplayın.</x-calc.hero>
        
        <div class="grid-2">
            <div class="form-group">
                <label>Kolon Genişliği (b) - cm</label>
                <input type="number" id="width" value="30" min="1" oninput="calc()">
            </div>
            <div class="form-group">
                <label>Kolon Derinliği (h) - cm</label>
                <input type="number" id="depth" value="50" min="1" oninput="calc()">
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label>Beton Sınıfı (fck)</label>
                <select id="concrete" onchange="calc()">
                    <option value="20">C20 (20 MPa)</option>
                    <option value="25" selected>C25 (25 MPa)</option>
                    <option value="30">C30 (30 MPa)</option>
                    <option value="35">C35 (35 MPa)</option>
                </select>
            </div>
            <div class="form-group">
                <label>Toplam Boyuna Donatı Alanı (cm²)</label>
                <input type="number" id="steelArea" value="15" min="0" oninput="calc()">
            </div>
        </div>

        <div class="result-box" data-wc-result>
            <div style="font-size:14px; color:#101318; font-weight:600; margin-bottom:5px;">TBDY SINIRI: AZAMİ TASARIM EKSENEL YÜKÜ (Nd,max = 0,40·fck·Ac)</div>
            <div class="result-val" id="res" data-wc-price>0 Ton</div>
            <div class="ws-sub" id="nor"></div>
            <div class="ws-warn" id="warn" role="alert" hidden></div>
            <div style="font-size:12px; color:#101318; margin-top:10px;">Not: TBDY 2018 7.3.1.2'ye göre kolonun tasarım eksenel kuvveti Nd ≤ 0,40·fck·Ac olmalıdır; kolona verilecek yük bu sınırla karşılaştırılır. Saf eksenel taşıma gücü (Nor, TS 500) moment ve burkulma etkisini içermez, tasarımda yük sınırı olarak kullanılmaz.</div>
        </div>

        <a href="/maliyet-hesapla?service=statik-guclendirme" class="cta">Bina Güçlendirme ve Statik Proje İçin Teklif Al →</a>
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
            const b = parseFloat(document.getElementById('width').value);
            const h = parseFloat(document.getElementById('depth').value);
            const fck = parseFloat(document.getElementById('concrete').value) || 25;
            const ast = parseFloat(document.getElementById('steelArea').value);
            const res = document.getElementById('res'), nor = document.getElementById('nor'), warn = document.getElementById('warn');

            // v119: negatif/0/boş girdide hesap yapılmaz
            if (!(b > 0) || !(h > 0) || !(ast >= 0)) { res.innerText = '—'; nor.innerText = ''; warn.innerText = 'Kolon ölçüleri için pozitif bir değer, donatı alanı için 0 veya pozitif bir değer girin.'; warn.hidden = false; return; }

            const Ac = (b * 10) * (h * 10);   // mm²
            const Ast_mm = ast * 100;          // mm²
            const fmtT = (n) => Math.round(n / 9810).toLocaleString('tr-TR') + ' Ton';   // N → ton

            // TBDY 2018 7.3.1.2: Nd ≤ 0,40·fck·Ac (ana sonuç)
            const NdMax = 0.40 * fck * Ac;
            // TS 500: saf eksenel taşıma gücü Nor = 0,85·fcd·(Ac − As) + fyd·As ; fcd = fck/1,5 ; S420 fyd = 365 MPa (ikincil)
            const fyd = 365, fcd = fck / 1.5;
            const Nor = 0.85 * fcd * (Ac - Ast_mm) + fyd * Ast_mm;

            res.innerText = fmtT(NdMax);
            nor.innerText = 'Saf eksenel taşıma gücü (Nor, moment yok; tasarımda yük sınırı olarak kullanılmaz): ' + fmtT(Nor);

            const notes = [];
            const rho = Ast_mm / Ac;
            if (rho < 0.01 || rho > 0.04) notes.push('Boyuna donatı oranı %' + (rho * 100).toLocaleString('tr-TR', { maximumFractionDigits: 2 }) + '. TBDY 2018 7.3.2.1: %1 ile %4 arasında olmalı.');
            if (Math.min(b, h) < 30 || Ac < 75000) notes.push('TBDY 2018 7.3.1.1: kolonun kısa kenarı en az 30 cm, kesit alanı en az 750 cm² olmalı.');
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
