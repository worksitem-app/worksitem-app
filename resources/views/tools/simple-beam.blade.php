<!DOCTYPE html>
<html lang="tr">
<head>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Basit Kiriş Statiği | WORKSİTEM</title>
    <meta name="description" content="Düzgün yayılı yüke maruz iki ucu mafsallı basit kirişte maksimum eğilme momenti ve kesme kuvvetini hızlıca hesaplayın.">
    <link rel="canonical" href="https://worksitem.com/araclar/basit-kiris-statigi">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *,*::before,*::after{box-sizing:border-box}
        body { font-family:'Inter', sans-serif; background:#fdfdfd; color:#111827; margin:0; padding-bottom:60px;}
        .container { max-width:800px; margin:40px auto; padding:30px; background:#fff; border:1px solid #e5e7eb; border-radius:16px; box-shadow:0 4px 6px rgba(0,0,0,0.05); }
        h1 { font-size:24px; font-weight:800; margin-bottom:10px; color:#101318;}
        .form-group { margin-bottom:20px; }
        label { display:block; font-size:13px; font-weight:700; color:#6b7280; margin-bottom:8px; text-transform:uppercase;}
        input { width:100%; padding:14px; border:1px solid #e5e7eb; border-radius:8px; font-family:inherit; font-size:15px; }
        .grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:15px; }
        .result-box { background:#f5f3ff; border:2px solid #ddd6fe; padding:20px; border-radius:12px; margin-top:20px; text-align:center;}
        .res-row { display:flex; justify-content:space-around; gap:12px; flex-wrap:wrap; }
        @media (max-width:480px) { .res-row .res-col + .res-col { border-left:0 !important; padding-left:0 !important; } }
        .ws-warn { color:#b91c1c; font-size:13px; font-weight:600; margin-top:10px; }
        .res-col { display:flex; flex-direction:column; gap:5px; }
        .result-val { font-size:28px; font-weight:800; color:#101318; }
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
<body class="wc" data-tool-slug="simple-beam">
    <x-site.contech-header />
    <div class="container">
        <x-calc.hero title="Basit kiriş statiği hesaplama" crumb="Basit kiriş">Düzgün yayılı yüke maruz iki ucu mafsallı basit kirişin maksimum eğilme momenti ve kesme kuvvetini hesaplayın.</x-calc.hero>
        
        <div class="grid-2">
            <div class="form-group">
                <label>Kiriş Açıklığı (L) - Metre</label>
                <input type="number" id="length" value="5" step="0.1" min="0.1" max="100" oninput="calc()">
            </div>
            <div class="form-group">
                <label>Düzgün Yayılı Yük (q) - kN/m</label>
                <input type="number" id="load" value="20" step="0.5" min="0" oninput="calc()">
            </div>
        </div>

        <div class="result-box" data-wc-result>
            <div class="res-row">
            <div class="res-col">
                <span style="font-size:13px; color:#5b21b6; font-weight:700;">MAKSİMUM MOMENT (M)</span>
                <span class="result-val" id="resM" data-wc-price>0 kNm</span>
            </div>
            <div class="res-col" style="border-left:2px solid #ddd6fe; padding-left:20px;">
                <span style="font-size:13px; color:#5b21b6; font-weight:700;">MAKS. KESME KUVVETİ (V)</span>
                <span class="result-val" id="resV">0 kN</span>
            </div>
            </div>
            <div class="ws-warn" id="warn" role="alert" hidden></div>
        </div>

        <a href="/maliyet-hesapla?service=celik-betonarme-proje" class="cta">Çelik veya Betonarme Tasarım Keşfi İste →</a>
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
            let l = parseFloat(document.getElementById('length').value);
            let q = parseFloat(document.getElementById('load').value);
            const warn = document.getElementById('warn');
            // v120: negatif/0/boş girdide hesap yapılmaz
            if (!(l > 0) || !(q > 0) || l > 100 || q > 10000) {
                document.getElementById('resM').innerText = '—';
                document.getElementById('resV').innerText = '—';
                warn.innerText = (l > 100 || q > 10000) ? 'Değerler aralık dışında (açıklık en çok 100 m, yük en çok 10.000 kN/m).' : 'Açıklık ve yük için pozitif bir değer girin.';
                warn.hidden = false;
                return;
            }
            warn.hidden = true;
            const f2 = (x) => x.toLocaleString('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            // Moment = (q * L^2) / 8 ; Kesme = (q * L) / 2
            let maxM = (q * Math.pow(l, 2)) / 8;
            let maxV = (q * l) / 2;
            document.getElementById('resM').innerText = f2(maxM) + ' kNm';
            document.getElementById('resV').innerText = f2(maxV) + ' kN';
        }
        document.addEventListener('DOMContentLoaded', calc);
    </script>
<x-calc.kit />
    <x-site.contech-footer />
    <script src="/js/tool-events.js" defer></script>
</body>
</html>
