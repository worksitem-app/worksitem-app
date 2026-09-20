<!DOCTYPE html>
<html lang="tr">
<head>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    <meta charset="UTF-8">
    <title>Karot Basınç Dayanımı | WORKSİTEM</title>
    <meta name="description" content="Karot numunesinin kırılma yükü ve geometrisine göre eşdeğer silindir beton basınç dayanımını MPa cinsinden hesaplayın.">
    <link rel="canonical" href="https://worksitem.com/araclar/karot-dayanim">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family:'Inter', sans-serif; background:#fdfdfd; color:#111827; margin:0; padding-bottom:60px;}
        .container { max-width:800px; margin:40px auto; padding:30px; background:#fff; border:1px solid #e5e7eb; border-radius:16px; box-shadow:0 4px 6px rgba(0,0,0,0.05); }
        h1 { font-size:24px; font-weight:800; margin-bottom:10px; color:#1e3a8a;}
        .form-group { margin-bottom:20px; }
        label { display:block; font-size:13px; font-weight:700; color:#6b7280; margin-bottom:8px; text-transform:uppercase;}
        input { width:100%; padding:14px; border:1px solid #e5e7eb; border-radius:8px; font-family:inherit; font-size:15px; }
        .grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:15px; }
        .result-box { background:#fff1f2; border:2px solid #fecdd3; padding:20px; border-radius:12px; margin-top:20px; text-align:center;}
        .result-val { font-size:32px; font-weight:800; color:#be123c; }
        .cta { display:block; width:100%; padding:16px; background:#e11d48; color:#fff; text-align:center; font-weight:800; text-decoration:none; border-radius:8px; margin-top:20px; }
    </style>
    <meta property="og:image" content="https://worksitem.com/brand/og-image.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="https://worksitem.com/brand/og-image.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="manifest" href="/site.webmanifest">
    <meta name="theme-color" content="#ffffff">
</head>
<body>
    <x-site.contech-header />
    <div class="container">
        <h1>🧱 Karot Basınç Dayanımı Çevirici</h1>
        <p style="color:#6b7280; margin-bottom:30px;">Pres makinesinde kırılan karot numunesinin kırılma yüküne ve geometrisine göre eşdeğer silindir beton dayanımını (MPa) hesaplayın.</p>
        
        <div class="grid-2">
            <div class="form-group">
                <label>Karot Çapı (d) - mm</label>
                <input type="number" id="dia" value="100" oninput="calc()">
            </div>
            <div class="form-group">
                <label>Karot Boyu (h) - mm</label>
                <input type="number" id="height" value="100" oninput="calc()">
            </div>
        </div>
        <div class="form-group">
            <label>Kırılma Yükü (F) - kN</label>
            <input type="number" id="force" value="150" oninput="calc()">
        </div>

        <div class="result-box">
            <div style="font-size:14px; color:#9f1239; font-weight:600; margin-bottom:5px;">EŞDEĞER SİLİNDİR DAYANIMI</div>
            <div class="result-val" id="res">0 MPa</div>
            <div style="font-size:12px; color:#be123c; margin-top:10px;">h/d düzeltme katsayısı otomatik uygulanmıştır.</div>
        </div>

        <a href="/maliyet-hesapla?service=deprem-testi" class="cta">Bina Deprem Testi ve Karot Alımı İçin Uzman Çağır →</a>
    </div>

        <!-- STANDART KAİNTA LEAD HUNİSİ (CTA) -->
        <div class="no-print" style="margin-top: 40px; padding: 30px; background: #f0fdf4; border: 2px solid #bbf7d0; border-radius: 16px; text-align: center;">
            <h3 style="color: #166534; font-size: 20px; font-weight: 800; margin-bottom: 10px;">Hesaplamayı Yaptınız, Şimdi İşi Uzmanına Bırakın</h3>
            <p style="color: #15803d; font-size: 14px; margin-bottom: 20px;">Kainta İnşaat mühendislerinden projenize özel resmi keşif ve bütçe teklifi alın.</p>
            <a href="/maliyet-hesapla" style="display: inline-block; background: #166534; color: #fff; padding: 16px 32px; font-size: 16px; font-weight: 800; border-radius: 8px; text-decoration: none; box-shadow: 0 4px 10px rgba(22,101,52,0.3); transition: all 0.2s;">Ön maliyet hesabına geç →</a>
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
            let d = parseFloat(document.getElementById('dia').value) || 1;
            let h = parseFloat(document.getElementById('height').value) || 1;
            let F = parseFloat(document.getElementById('force').value) || 0;
            
            // Alan A = (pi * d^2) / 4 (mm2)
            let A = (Math.PI * Math.pow(d, 2)) / 4;
            
            // Basınç f = F(Newton) / A(mm2) = MPa
            let f_raw = (F * 1000) / A;
            
            // h/d katsayısı düzeltmesi (Yaklaşık formül)
            let ratio = h / d;
            let factor = 1.0;
            if (ratio < 2.0) {
                factor = 0.85 + (0.15 * (ratio - 1)); // Basit lineer interpolasyon
            }
            if (ratio < 1) factor = 0.85; // Limitleme

            let f_core = f_raw * factor;
            
            document.getElementById('res').innerText = f_core.toFixed(2) + ' MPa';
        }
        document.addEventListener('DOMContentLoaded', calc);
    </script>
    <x-site.contech-footer />
</body>
</html>
