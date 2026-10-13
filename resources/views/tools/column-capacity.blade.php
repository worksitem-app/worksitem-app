<!DOCTYPE html>
<html lang="tr">
<head>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    <meta charset="UTF-8">
    <title>Kolon Eksenel Taşıma Kapasitesi | WORKSİTEM</title>
    <meta name="description" content="Beton sınıfı, kolon kesiti ve donatı miktarına göre betonarme kolonun teorik maksimum eksenel taşıma kapasitesini hesaplayın.">
    <link rel="canonical" href="https://worksitem.com/araclar/kolon-tasima-kapasitesi">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
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
        <x-calc.hero title="Kolon eksenel taşıma kapasitesi hesaplama" crumb="Kolon kapasitesi">Beton sınıfı ve donatı miktarına göre betonarme bir kolonun teorik maksimum eksenel yük taşıma kapasitesini (Nd) hesaplayın.</x-calc.hero>
        
        <div class="grid-2">
            <div class="form-group">
                <label>Kolon Genişliği (b) - cm</label>
                <input type="number" id="width" value="30" oninput="calc()">
            </div>
            <div class="form-group">
                <label>Kolon Derinliği (h) - cm</label>
                <input type="number" id="depth" value="50" oninput="calc()">
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
                <input type="number" id="steelArea" value="15" oninput="calc()">
            </div>
        </div>

        <div class="result-box" data-wc-result>
            <div style="font-size:14px; color:#101318; font-weight:600; margin-bottom:5px;">TEORİK EKSENEL KAPASİTE (Yaklaşık)</div>
            <div class="result-val" id="res" data-wc-price>0 Ton</div>
            <div style="font-size:12px; color:#101318; margin-top:10px;">Not: TS500 basitleştirilmiş formülü kullanılmıştır. Moment etkileri dahil değildir.</div>
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
            let b = parseFloat(document.getElementById('width').value) || 0;
            let h = parseFloat(document.getElementById('depth').value) || 0;
            let fck = parseFloat(document.getElementById('concrete').value) || 25;
            let ast = parseFloat(document.getElementById('steelArea').value) || 0;
            
            // Alan cm2'den mm2'ye
            let Ac = (b * 10) * (h * 10); 
            let Ast_mm = ast * 100;
            
            // Fyd (Donatı akma) genelde S420 için 365 MPa tasarım değeri alınır
            let fyd = 365;
            // Fcd (Beton tasarım) fck / 1.5
            let fcd = fck / 1.5;

            // Nd = 0.85 * fcd * (Ac - Ast) + fyd * Ast
            let Nd_Newton = (0.85 * fcd * (Ac - Ast_mm)) + (fyd * Ast_mm);
            let Nd_Ton = Nd_Newton / 9810; // Newton to Ton
            
            document.getElementById('res').innerText = Math.round(Nd_Ton).toLocaleString('tr-TR') + ' Ton';
        }
        document.addEventListener('DOMContentLoaded', calc);
    </script>
<x-calc.kit />
    <x-site.contech-footer />
    <script src="/js/tool-events.js" defer></script>
</body>
</html>
