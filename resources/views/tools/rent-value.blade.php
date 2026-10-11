<!DOCTYPE html><html lang="tr"><head>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
<meta charset="UTF-8"><title>Daire Kira Değeri | WORKSİTEM</title><link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"><style>body{font-family:'Inter',sans-serif;background:#fdfdfd;color:#111827;margin:0;padding-bottom:60px;} .container{max-width:800px;margin:40px auto;padding:30px;background:#fff;border:1px solid #e5e7eb;border-radius:16px;box-shadow:0 4px 6px rgba(0,0,0,0.05);} h1{font-size:24px;font-weight:800;margin-bottom:10px;color:#101318;} .form-group{margin-bottom:20px;} label{display:block;font-size:13px;font-weight:700;color:#6b7280;margin-bottom:8px;text-transform:uppercase;} input,select{width:100%;padding:14px;border:1px solid #e5e7eb;border-radius:8px;font-family:inherit;font-size:15px;} .grid-2{display:grid;grid-template-columns:1fr 1fr;gap:15px;} .result-box{background:#fff8e1;border:2px solid #fde68a;padding:20px;border-radius:12px;margin-top:20px;text-align:center;} .result-val{font-size:32px;font-weight:800;color:#101318;} .cta{display:block;width:100%;padding:16px;background:#101318;color:#fff;text-align:center;font-weight:800;text-decoration:none;border-radius:8px;margin-top:20px; transition:all 0.2s;} .cta:hover{opacity:0.9; transform:translateY(-2px);}</style>    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body data-tool-slug="rent-value"><div class="container"><h1>Daire Kira ve Amortisman Değeri</h1><p style="color:#6b7280; margin-bottom:30px;">Gayrimenkulün piyasa değerine ve hedeflenen amortisman (geri dönüş) süresine göre adil aylık kira bedelini hesaplayın.</p>
<div class="grid-2"><div class="form-group"><label>Daire / Mülk Değeri (TL)</label><input type="number" id="val" value="5000000" oninput="calc()"></div><div class="form-group"><label>Amortisman Süresi (Yıl)</label><input type="number" id="years" value="18" oninput="calc()"></div></div>
<div class="result-box"><div style="font-size:14px; color:#101318; font-weight:600;">ADİL AYLIK KİRA DEĞERİ</div><div class="result-val" id="res">0 TL</div></div>
<a href="/maliyet-hesapla?service=yatirim-danismanligi" class="cta">Gayrimenkul ve Kat Karşılığı Danışmanlığı Al →</a></div>

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
        function calc() { let v = parseFloat(document.getElementById('val').value)||0; let y = parseFloat(document.getElementById('years').value)||1; let rent = v / (y * 12); document.getElementById('res').innerText = new Intl.NumberFormat('tr-TR').format(Math.round(rent)) + ' TL'; } document.addEventListener('DOMContentLoaded', calc);</script>    <script src="/js/tool-events.js" defer></script>
</body></html>
