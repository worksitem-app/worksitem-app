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
    <meta name="viewport" content="width=device-width, initial-scale=1"><title>Pompa Gücü Hesabı | WORKSİTEM</title>
    <meta name="description" content="Debi, basma yüksekliği ve verim değerlerine göre su pompası veya hidrofor için gerekli motor gücünü kW cinsinden hesaplayın.">
    <link rel="canonical" href="https://worksitem.com/araclar/pompa-gucu-hesabi"><link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"><style>*,*::before,*::after{box-sizing:border-box} .ws-warn{color:#b91c1c;font-size:13px;font-weight:600;margin-top:10px;} .ws-sub{font-size:13px;color:#374151;margin-top:8px;line-height:1.5;} .result-val{overflow-wrap:anywhere;} body{font-family:'Inter',sans-serif;background:#fdfdfd;color:#111827;margin:0;padding-bottom:60px;} .container{max-width:800px;margin:40px auto;padding:30px;background:#fff;border:1px solid #e5e7eb;border-radius:16px;box-shadow:0 4px 6px rgba(0,0,0,0.05);} h1{font-size:24px;font-weight:800;margin-bottom:10px;color:#101318;} .form-group{margin-bottom:20px;} label{display:block;font-size:13px;font-weight:700;color:#6b7280;margin-bottom:8px;text-transform:uppercase;} input,select{width:100%;padding:14px;border:1px solid #e5e7eb;border-radius:8px;font-family:inherit;font-size:15px;} .grid-2{display:grid;grid-template-columns:1fr 1fr;gap:15px;} .result-box{background:#fff8e1;border:2px solid #fde68a;padding:20px;border-radius:12px;margin-top:20px;text-align:center;} .result-val{font-size:32px;font-weight:800;color:#101318;} .cta{display:block;width:100%;padding:16px;background:#101318;color:#fff;text-align:center;font-weight:800;text-decoration:none;border-radius:8px;margin-top:20px; transition:all 0.2s;} .cta:hover{opacity:0.9; transform:translateY(-2px);}</style>    <meta name="csrf-token" content="{{ csrf_token() }}">
    <x-calc.head />
</head>
<body class="wc" data-tool-slug="pump-power">
    <x-site.contech-header /><div class="container"><x-calc.hero title="Su pompası ve hidrofor gücü hesaplama" crumb="Pompa gücü">İstenen debi, basma yüksekliği ve pompa verimine göre pompa mil gücünü ve seçilecek standart motor gücünü (kW) bulun.</x-calc.hero>
<div class="grid-2"><div class="form-group"><label>Debi (Q) - m³/h</label><input type="number" id="flow" value="20" min="0.1" oninput="calc()"></div><div class="form-group"><label>Basma Yüksekliği (Hm) - Metre</label><input type="number" id="head" value="30" min="0.1" oninput="calc()"></div></div>
<div class="grid-2"><div class="form-group"><label>Pompa Verimi (%)</label><input type="number" id="eff" value="70" min="20" max="95" step="1" oninput="calc()"></div><div class="form-group"><label>Motor Güç Payı (%)</label><input type="number" id="margin" value="15" min="0" max="50" step="5" oninput="calc()"></div></div>
<div class="result-box" data-wc-result><div style="font-size:14px; color:#101318; font-weight:600;">ÖNERİLEN STANDART MOTOR GÜCÜ</div><div class="result-val" id="res" data-wc-price>0 kW</div><div class="ws-sub" id="detail"></div><div class="ws-warn" id="warn" role="alert" hidden></div><div style="font-size:12px; color:#101318; margin-top:8px;">Mil gücü P = Q·H / (367·η) (su, Q m³/h, H m). Motor, mil gücüne güç payı eklenip bir üst standart motor kademesine (IEC) yuvarlanarak seçilir. Kesin seçim pompa üreticisinin eğrisinden yapılır.</div></div>
<a href="/maliyet-hesapla?service=tesisat" class="cta">Sıhhi Tesisat ve Hidrofor Sistemi Teklifi Al →</a></div>

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
            const q = parseFloat(document.getElementById('flow').value), h = parseFloat(document.getElementById('head').value);
            const eta = parseFloat(document.getElementById('eff').value) / 100, m = parseFloat(document.getElementById('margin').value);
            const res = document.getElementById('res'), warn = document.getElementById('warn'), det = document.getElementById('detail');
            // v120: negatif/0/boş girdide hesap yapılmaz
            if (!(q > 0) || !(h > 0)) { res.innerText = '—'; det.innerText = ''; warn.innerText = 'Debi ve basma yüksekliği için pozitif bir değer girin.'; warn.hidden = false; return; }
            if (!(eta >= 0.2 && eta <= 0.95) || !(m >= 0 && m <= 100)) { res.innerText = '—'; det.innerText = ''; warn.innerText = 'Verim %20–95, güç payı %0–100 arasında olmalı.'; warn.hidden = false; return; }
            warn.hidden = true;
            const shaft = (q * h) / (367 * eta);   // mil gücü, kW
            const need = shaft * (1 + m / 100);    // motor payı dahil
            // IEC 60034 standart motor kademeleri (kW)
            const steps = [0.37, 0.55, 0.75, 1.1, 1.5, 2.2, 3, 4, 5.5, 7.5, 11, 15, 18.5, 22, 30, 37, 45, 55, 75, 90, 110, 132, 160, 200, 250, 315];
            const motor = steps.find(s => s >= need);
            const f2 = (x) => x.toLocaleString('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            res.innerText = motor ? motor.toLocaleString('tr-TR') + ' kW' : '315 kW üzeri';
            det.innerText = 'Pompa mil gücü: ' + f2(shaft) + ' kW · Pay dahil: ' + f2(need) + ' kW';
        } document.addEventListener('DOMContentLoaded', calc);</script>    <script src="/js/tool-events.js" defer></script>
<x-calc.kit />
    <x-site.contech-footer />
</body></html>
